<?php

namespace App\AdminApi\Enums\Accumulators;

use App\Infrastructure\Enum;

class FrequencyEnum extends Enum
{
    protected const MONTHLY = 'monthly';
    protected const QUARTERLY = 'quarterly';
    protected const YEARLY = 'yearly';
}
