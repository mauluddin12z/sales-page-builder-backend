<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLandingPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'max:3000',
            ],

            'target_audience' => [
                'required',
                'string',
                'max:1000',
            ],

            'features' => [
                'nullable',
                'array',
            ],

            'features.*' => [
                'string',
                'max:255',
            ],

            'price' => [
                'nullable',
                'string',
                'max:255',
            ],

            'usp' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
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
