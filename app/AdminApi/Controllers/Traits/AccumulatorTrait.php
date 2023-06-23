<?php

namespace App\AdminApi\Controllers\Traits;

use App\AdminApi\Enums\Accumulators\FrequencyEnum;
use App\AdminApi\Enums\Accumulators\TypeEnum;
use App\AdminApi\Enums\Accumulators\ValueTypeEnum;
use App\AdminApi\Repositories\AccumulatorRepository;
use App\AdminApi\Resources\Accumulators\AccumulatorResource;
use App\AdminApi\Resources\Accumulators\AccumulatorTypeResource;
use App\AdminApi\Resources\Accumulators\AccumulatorValueTypeResource;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Symfony\Component\HttpFoundation\Response;

trait AccumulatorTrait
{
    public function getAccumulators(AccumulatorRepository $repository): Response
    {
        $this->authorize('view_accumulators');

        $filters = $this->validate([
            'partner_ids'       => ['nullable', 'array'],
            'partner_ids.*'     => ['int'],
            'types'             => ['nullable', 'array'],
            'types.*'           => ['string', Rule::in(TypeEnum::allValues())],
            'search_phrase'     => ['string', 'nullable'],
        ]);

        $partnerIds = !empty($filters['partner_ids'])
            ? array_intersect($filters['partner_ids'], CurrentAdmin::managedPartnerIds())
            : CurrentAdmin::managedPartnerIds();

        return $this->response200(AccumulatorResource::collection(
            $repository->scopeByPartnerIds($partnerIds)->getAccumulators($filters)
        ));
    }

    public function getAccumulator($id, AccumulatorRepository $repository): Response
    {
        $this->authorize('view_accumulators');

        $accumulator = $repository->scopeByPartnerIds(CurrentAdmin::managedPartnerIds())->getAccumulatorById($id);
        abort_if(!$accumulator, 404);

        return $this->response200(AccumulatorResource::make($accumulator));
    }

    public function createAccumulator(AccumulatorRepository $repository): Response
    {
        $this->authorize('create_accumulators');

        $data = $this->validate($this->getAccumulatorRules());

        abort_if(
            !in_array($data['partner_id'], CurrentAdmin::managedPartnerIds()),
            403
        );

        return $this->response201(AccumulatorResource::make($repository->addAccumulator($data)));
    }

    public function updateAccumulator($id, AccumulatorRepository $repository): Response
    {
        $this->authorize('edit_accumulators');

        $data = $this->validate($this->getAccumulatorRules($id));

        abort_if(
            !in_array($data['partner_id'], CurrentAdmin::managedPartnerIds()),
            403
        );

        $accumulator = $repository->getAccumulatorById($id);
        abort_if(!$accumulator, 404);
        $this->checkAccessPolicy(new AccumulatorPolicy($accumulator));


        return $this->response202(AccumulatorResource::make($repository->updateAccumulator($accumulator, $data)));
    }

    public function deleteAccumulator($id, AccumulatorRepository $accumulatorRepository, LoyaltyRuleRepository $loyaltyRuleRepository): Response
    {
        $this->authorize('delete_accumulators');

        $accumulator = $accumulatorRepository->getAccumulatorById($id);
        abort_if(!$accumulator, 404);
        $this->checkAccessPolicy(new AccumulatorPolicy($accumulator));

        if (
            $accumulator->caps()->exists()
            || $accumulator->tiers()->exists()
        ) {
             return $this->response418('Unable to delete the accumulator since it\'s currently affiliated with an active reward tier or reward cap. Proceed by deleting the reward tier or reward cap before deleting the accumulator.');
        }

        $accumulatorRepository->deleteAccumulatorById($id);

        return $this->response204();
    }

    public function getAccumulatorTypes(): Response
    {
        $this->authorize('view_accumulators');

        return $this->response200(AccumulatorTypeResource::collection(TypeEnum::fromValues(TypeEnum::allValues())));
    }

    public function getAccumulatorValueTypes(): Response
    {
        $this->authorize('view_accumulators');


        return $this->response200(
            AccumulatorValueTypeResource::collection(ValueTypeEnum::fromValues(ValueTypeEnum::allValues()))
        );
    }

    private function getAccumulatorRules(int $accumulatorId = null): array
    {
        return [
            'name'          => [
                'required',
                'string',
                'max:200',
                Rule::unique('reward_accumulator', 'name')
                    ->when(filled($accumulatorId), fn(Unique $rule) => $rule->ignore($accumulatorId)),
            ],
            'partner_id'    => ['required', 'integer', Rule::in(CurrentAdmin::managedPartnerIds())],
            'frequency'     => ['required', 'string', Rule::in(FrequencyEnum::allValues())],
            'type'          => ['required', 'integer', Rule::in(TypeEnum::allValues())],
            'value_type'    => ['required', 'integer', Rule::in(ValueTypeEnum::allValues())],
            'currency_id'   => ['required', 'integer', Rule::exists('currency', 'id')],
        ];
    }
}
