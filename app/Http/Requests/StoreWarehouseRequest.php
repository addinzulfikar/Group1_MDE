<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
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
            'warehouse_code' => 'required|string|unique:warehouses,warehouse_code|max:50',
            'warehouse_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    /**
     * Get custom message for validation rules.
     */
    public function messages(): array
    {
        return [
            'warehouse_code.required' => 'Kode gudang harus diisi',
            'warehouse_code.unique' => 'Kode gudang sudah terdaftar',
            'warehouse_name.required' => 'Nama gudang harus diisi',
            'location.required' => 'Lokasi gudang harus diisi',
            'capacity.required' => 'Kapasitas gudang harus diisi',
            'capacity.integer' => 'Kapasitas harus berupa angka',
            'capacity.min' => 'Kapasitas minimal 1 unit',
        ];
    }
}
