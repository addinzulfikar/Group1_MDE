<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
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
     * warehouse_code dihapus. current_load dihitung otomatis dari paket.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_name' => 'nullable|string|max:100',
            'location'       => 'nullable|string|max:255',
            'capacity'       => 'nullable|integer|min:1',
            'status'         => 'nullable|in:available,full,overload',
        ];
    }

    /**
     * Get custom message for validation rules.
     */
    public function messages(): array
    {
        return [
            'capacity.integer' => 'Kapasitas harus berupa angka',
            'capacity.min'     => 'Kapasitas minimal 1 unit',
        ];
    }
}
