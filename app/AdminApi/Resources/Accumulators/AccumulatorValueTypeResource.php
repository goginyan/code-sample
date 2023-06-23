<?php

namespace App\AdminApi\Resources\Accumulators;

use App\AdminApi\Enums\Accumulators\TypeEnum;
use App\AdminApi\Enums\Accumulators\ValueTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class AccumulatorValueTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->resource->value(),
            'name'  => $this->resource->description()
        ];
    }
}
