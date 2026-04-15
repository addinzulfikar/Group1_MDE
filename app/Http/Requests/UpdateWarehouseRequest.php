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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_code' => 'nullable|string|unique:warehouses,warehouse_code,' . $this->route('id') . '|max:50',
            'warehouse_name' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'current_load' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    /**
     * Get custom message for validation rules.
     */
    public function messages(): array
    {
        return [
            'warehouse_code.unique' => 'Kode gudang sudah terdaftar',
            'capacity.integer' => 'Kapasitas harus berupa angka',
            'capacity.min' => 'Kapasitas minimal 1 unit',
            'current_load.integer' => 'Beban saat ini harus berupa angka',
            'current_load.min' => 'Beban saat ini tidak bisa negatif',
        ];
    }
}
