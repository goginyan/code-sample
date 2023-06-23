<?php

namespace App\AdminApi\Repositories;

use App\AcquirerBin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\Conditionable;

class AcquirerBinRepository
{
    use Conditionable;

    public array $partnerIds = [];
    public ?string $searchPhrase = null;

    public function getAcquirerBinList(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->initialQuery()
            ->with('partner', fn($q) => $q->select('id', 'name'))
            ->when(
                $this->searchPhrase,
                fn($q) => $q->where(
                    fn ($q) => $q->where('acquirer_bin', 'like', "%$this->searchPhrase%")
                        ->orWhere('description', 'like', "%$this->searchPhrase%")
                        ->orWhere('partner_id', $this->searchPhrase)
                        ->orWhereHas('partner', fn($q) => $q->where('name', 'like', "%$this->searchPhrase%"))
                )
            )
            ->paginate();
    }

    public function getAcquirerBinById($id): ?AcquirerBin
    {
        return $this->initialQuery()->find($id);
    }

    public function addAcquirerBin($data): AcquirerBin
    {
        return $this->initialQuery()->create($data);
    }

    public function updateAcquirerBinById($id, $data): ?AcquirerBin
    {
        $this->initialQuery()
            ->where('id', $id)
            ->update($data);

        return $this->getAcquirerBinById($id);
    }

    public function destroyAcquirerBin(AcquirerBin $acquirerBin): ?bool
    {
        return $acquirerBin->delete();
    }

    public function searchPhrase($searchPhrase): AcquirerBinRepository
    {
        $this->searchPhrase = $searchPhrase;

        return $this;
    }

    public function scopeByPartnerIds(array $partnerIds): self
    {
        $this->partnerIds = $partnerIds;

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<AcquirerBin>
     */
    private function initialQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return AcquirerBin::query()
                ->when(
                    filled($this->partnerIds),
                    fn(Builder $q) => $q->filterByPartner($this->partnerIds)
                );
    }
}
