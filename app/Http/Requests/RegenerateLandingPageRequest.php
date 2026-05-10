<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegenerateLandingPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'field' => [
                'required',
                'string',
                'in:headline,subheadline,description,benefits,features,social_proof,pricing,cta',
            ],

            'current_output' => [
                'required',
                'array',
            ],

            'product_name' => [
                'required',
                'string',
            ],

            'description' => [
                'required',
                'string',
            ],

            'target_audience' => [
                'required',
                'string',
            ],

            'features' => [
                'nullable',
                'array',
            ],

            'features.*' => [
                'string',
            ],

            'price' => [
                'nullable',
                'string',
            ],

            'usp' => [
                'nullable',
                'string',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Support JSON String current_output
        |--------------------------------------------------------------------------
        */

        if (is_string($this->current_output)) {

            $decoded = json_decode(
                $this->current_output,
                true
            );

            if (json_last_error() === JSON_ERROR_NONE) {

                $this->merge([
                    'current_output' => $decoded,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Empty Values
        |--------------------------------------------------------------------------
        */

        $this->merge([

            'features' => $this->features ?? [],

            'price' => $this->price ?? '',

            'usp' => $this->usp ?? '',
        ]);
    }
}
