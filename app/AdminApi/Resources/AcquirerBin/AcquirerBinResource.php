<?php

namespace App\AdminApi\Resources\AcquirerBin;

use Illuminate\Http\Resources\Json\JsonResource;

class AcquirerBinResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                        => (int)$this->resource->id,
            'partner_id'                => (int)$this->resource->partner_id,
            'acquirer_bin'              => $this->resource->acquirer_bin,
            'description'               => $this->resource->description,
        ];
    }
}
