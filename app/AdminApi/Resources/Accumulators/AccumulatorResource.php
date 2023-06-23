<?php

namespace App\AdminApi\Resources\Accumulators;

use App\AdminApi\Enums\Accumulators\TypeEnum;
use App\AdminApi\Enums\Accumulators\ValueTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class AccumulatorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->resource->id,
            'accumulator_name'  => $this->resource->name,
            'partner_id'        => $this->resource->partner_id,
            'partner_name'      => $this->resource->partner ? $this->resource->partner->name : null,
            'type_id'           => $this->resource->type,
            'type'              => TypeEnum::fromValue((int)$this->resource->type)->description(),
            'value_type_id'     => $this->resource->value_type,
            'value_type'        => ValueTypeEnum::fromValue((int)$this->resource->value_type)->description(),
            'frequency'         => $this->resource->frequency,
            'currency_id'       => $this->resource->currency_id,
        ];
    }
}
