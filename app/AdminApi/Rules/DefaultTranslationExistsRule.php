<?php

namespace App\AdminApi\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DefaultTranslationExistsRule implements Rule
{
    private ?int $keyId;
    private ?int $partnerId;
    private string $message = 'Chosen Lang key is invalid';

    public function __construct(?int $partnerId, int $keyId)
    {
        $this->keyId = $keyId;
        $this->partnerId = $partnerId;
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->partnerId) {
            return true;
        }

        $passes = DB::table('localization_lang_keys')
            ->where('lang_id', $value)
            ->where('key_id', $this->keyId)
            ->exists();

        if (!$passes) {
            $this->message = 'There is no default translation for chosen language with id ' . $value;
        }

        return $passes;
    }

    public function message(): string
    {
        return $this->message;
    }
}
