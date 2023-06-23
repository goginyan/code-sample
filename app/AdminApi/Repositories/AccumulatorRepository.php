<?php

namespace App\AdminApi\Repositories;

use App\AdminApi\Enums\Accumulators\TypeEnum;
use App\RewardAccumulator;
use Illuminate\Database\Eloquent\Builder;

class AccumulatorRepository
{
    private ?array $partnerIds = null;

    public function getAccumulators($filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
         return $this->initialQuery()
            ->with(['partner' => fn($q) => $q->select('id', 'name')])
            ->when(
                !empty($filters['search_phrase']),
                fn (Builder $q) => $q->where(
                    fn($q) => $q->where('reward_accumulator.name', 'like', "%" . $filters['search_phrase'] . "%")
                        ->orWhere('reward_accumulator.id', 'like', "%" . $filters['search_phrase'] . "%")
                )
            )
            // If types filter contain all the Types from TypeEnum  => filtering by Type is redundant
            ->when(
                !empty($filters['types']) && array_diff(TypeEnum::allValues(), $filters['types']),
                fn (Builder $q) => $q->whereIn('reward_accumulator.type', $filters['types'])
            )
            ->paginate();
    }

    public function getAccumulatorById($id)
    {
        return $this->initialQuery()->find($id);
    }

    public function addAccumulator($data)
    {
        return RewardAccumulator::query()->create($data);
    }

    public function updateAccumulator($accumulator, $data)
    {
        $accumulator->fill($data);
        $accumulator->save();

        return $accumulator;
    }

    public function deleteAccumulatorById($id)
    {
        RewardAccumulator::destroy($id);
    }

    private function initialQuery(): Builder
    {
        return RewardAccumulator::query()
            ->when(
                $this->partnerIds,
                fn (Builder $q) => $q->whereIn('reward_accumulator.partner_id', $this->partnerIds)
            );
    }

    public function scopeByPartnerIds($partnerIds): AccumulatorRepository
    {
        $this->partnerIds = $partnerIds;

        return $this;
    }
}
