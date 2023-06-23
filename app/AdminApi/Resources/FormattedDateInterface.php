<?php

namespace App\AdminApi\Resources;

interface FormattedDateInterface
{
    public function withDateFormat(string $dateFormat): self;
    public function withDateTimeFormat(string $dateTimeFormat): self;
}
