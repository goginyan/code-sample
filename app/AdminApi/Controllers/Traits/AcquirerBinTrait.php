<?php

namespace App\AdminApi\Controllers\Traits;

use App\AdminApi\Repositories\AcquirerBinRepository;
use App\AdminApi\Resources\AcquirerBin\AcquirerBinListResource;
use App\AdminApi\Resources\AcquirerBin\AcquirerBinResource;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

trait AcquirerBinTrait
{
    public function getAcquirerBins(AcquirerBinRepository $repository): Response
    {
        $this->authorize('view_acquirer_bins');

        $filters = $this->validate([
            'search_phrase' => ['nullable', 'string']
        ]);

        $acquirerBins = $repository->when(
            !CurrentAdmin::canManageAllPartners(),
            fn() => $repository->scopeByPartnerIds(CurrentAdmin::managedPartnerIds())
        )
            ->searchPhrase($filters['search_phrase'] ?? null)
            ->getAcquirerBinList();

        return $this->response200(AcquirerBinListResource::collection($acquirerBins));
    }

    public function getAcquirerBin($id, AcquirerBinRepository $repository): Response
    {
        $this->authorize('view_acquirer_bins');

        return $this->response200(AcquirerBinResource::make($repository->getAcquirerBinById($id)));
    }

    public function createAcquirerBin(AcquirerBinRepository $repository): Response
    {
        $this->authorize('create_acquirer_bins');

        $data = $this->validate($this->getAcquirerBinRules());

        return $this->response201(AcquirerBinResource::make($repository->addAcquirerBin($data)));
    }

    public function updateAcquirerBin($id, AcquirerBinRepository $repository): Response
    {
        $this->authorize('edit_acquirer_bins');

        $data = $this->validate($this->getAcquirerBinRules($id));

        $acquirerBin = $repository->updateAcquirerBinById($id, $data);
        abort_if(!$acquirerBin, 404);

        return $this->response202(AcquirerBinResource::make($acquirerBin));
    }

    public function deleteAcquirerBin($id, AcquirerBinRepository $repository): Response
    {
        $this->authorize('delete_acquirer_bins');

        abort_if(!$acquirerBin = $repository->getAcquirerBinById($id), 404);

        $repository->destroyAcquirerBin($acquirerBin);

        return $this->response204();
    }

    private function getAcquirerBinRules($id = null): array
    {
        return [
            'partner_id'        => ['required', 'int', Rule::exists('partner', 'id')->where('draft', 0)],
            'acquirer_bin'      => [
                'required',
                'numeric',
                Rule::unique('acquirer_bin')
                    ->withoutTrashed()
                    ->ignore($id)
                    ->where('partner_id', request('partner_id'))
            ],
            'description'       => ['nullable', 'string', 'max:128'],
        ];
    }
}
