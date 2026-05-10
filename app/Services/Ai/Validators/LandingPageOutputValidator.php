<?php

namespace App\Services\Ai\Validators;

use Illuminate\Support\Facades\Validator;

class LandingPageOutputValidator
{
    public function validate(array $data): array
    {
        Validator::make($data, [

            'headline' => ['required', 'string'],
            'subheadline' => ['required', 'string'],
            'description' => ['required', 'string'],

            'benefits' => ['required', 'array'],
            'benefits.*' => ['string'],

            'features' => ['required', 'array'],
            'features.*' => ['string'],

            'social_proof' => ['required', 'string'],
            'pricing' => ['required', 'string'],
            'cta' => ['required', 'string'],

        ])->validate();

        return $data;
    }
}
