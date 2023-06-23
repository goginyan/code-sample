<?php

namespace App\AdminApi\Resources\AcquirerBin;

use Illuminate\Http\Resources\Json\JsonResource;

class AcquirerBinListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                        => (int)$this->resource->id,
            'partner_name'              => $this->resource->partner ? $this->resource->partner->name : null,
            'acquirer_bin'              => $this->resource->acquirer_bin,
            'description'               => $this->resource->description,
        ];
    }
}
