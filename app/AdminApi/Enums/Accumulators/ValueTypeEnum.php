<?php

namespace App\AdminApi\Enums\Accumulators;

use App\Infrastructure\Enum;

class ValueTypeEnum extends Enum
{
    protected const POINTS = 1;
    protected const MONEY = 2;

    public static function descriptions(): array
    {
        return [
            self::POINTS => 'Points',
            self::MONEY => 'Money'
        ];
    }
}
