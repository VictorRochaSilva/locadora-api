<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleUpdateRequest extends FormRequest
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
            'plate' => [
                'required',
                'string',
                Rule::unique('vehicles', 'plate')->ignore($this->route('vehicle')->id),
            ],
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'daily_rate' => 'required|numeric|min:0',
        ];
    }
}
