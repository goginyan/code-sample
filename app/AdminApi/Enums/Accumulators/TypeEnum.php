<?php

namespace App\AdminApi\Enums\Accumulators;

use App\Infrastructure\Enum;

class TypeEnum extends Enum
{
    protected const CAP = 1;
    protected const TIER = 2;

    protected static array $intToTextCode =
        [
            self::CAP => 'cap',
            self::TIER => 'tier',
        ];

    public static function allTextCodes(): array
    {
        return self::$intToTextCode;
    }

    public static function descriptions(): array
    {
        return [
            self::CAP => 'Cap',
            self::TIER => 'Tier',
        ];
    }

    public function toResource(): array
    {
        return parent::toResource();
    }

    public static function textCodesToIntCodes(array $textCodes): array
    {
        return array_flip(array_intersect(self::$intToTextCode, $textCodes));
    }
}
