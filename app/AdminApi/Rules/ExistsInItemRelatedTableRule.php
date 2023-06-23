<?php

namespace App\AdminApi\Rules;

use App\Components\Redemption\FeaturedCollections\Enums\FeaturedCollectionItemTypeEnum;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsInItemRelatedTableRule implements Rule
{
    private string $type;

    public function __construct(string $type = null)
    {
        $this->type = $type;
    }

    public function passes($attribute, $value): bool
    {
        return in_array($this->type, FeaturedCollectionItemTypeEnum::allValues())
            && DB::table($this->type)
                ->where('draft', false)
                ->whereNull('deleted_at')
                ->where('id', $value)
                ->exists();
    }

    public function message(): string
    {
        return 'Item doesn\'t exists in related table';
    }
}
