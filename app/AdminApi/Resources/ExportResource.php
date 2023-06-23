<?php

namespace App\AdminApi\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ExportResource extends JsonResource implements FormattedDateInterface
{
    protected string $dateFormat = 'Y-m-d';
    protected string $dateTimeFormat = 'Y-m-d H:i:s';

    public function withDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function withDateTimeFormat(string $dateTimeFormat): self
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }
}
