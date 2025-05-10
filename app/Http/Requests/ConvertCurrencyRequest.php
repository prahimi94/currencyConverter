<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertCurrencyRequest extends FormRequest
{
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
        return [
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'from.required' => 'Please enter the source currency.',
            'from.string' => 'Source currency must be a string.',
            'from.size' => 'Source currency must be 3 characters long.',
            'to.string' => 'Destination currency must be a string.',
            'to.required' => 'Please enter the destination currency.',
            'to.size' => 'Destination currency must be 3 characters long.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
        ];
    }
}
