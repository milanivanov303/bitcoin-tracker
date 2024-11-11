<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlertRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match($this->input('type')) {
            'price' => [
                'email' => 'required|email',
                'price' => 'required|numeric|min:1',
            ],
            'percent' => [
                'email'        => 'required|email',
                'percent'      => 'required|numeric|between:0,10000',
                'interval'     => 'required|numeric|in:1,6,24',
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'price.min'       => 'Price should be positive number greater than 1',
            'percent.between' => 'Percent should be between 0 and 10 000',
            'interval.in'     => 'The interval is invalid. Possible options are 1, 6 and 24.'
        ];
    }
}
