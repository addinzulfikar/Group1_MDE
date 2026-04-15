<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
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
            'tracking_number' => 'nullable|string|unique:packages,tracking_number,' . $this->package->id . '|max:50',
            'sender_name' => 'nullable|string|max:100',
            'receiver_name' => 'nullable|string|max:100',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0.1',
            'width' => 'nullable|numeric|min:0.1',
            'height' => 'nullable|numeric|min:0.1',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'package_status' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom message for validation rules.
     */
    public function messages(): array
    {
        return [
            'tracking_number.unique' => 'Nomor tracking sudah terdaftar',
            'weight.numeric' => 'Berat harus berupa angka',
            'weight.min' => 'Berat minimal 0.1 kg',
            'length.numeric' => 'Panjang harus berupa angka',
            'length.min' => 'Panjang minimal 0.1 cm',
            'width.numeric' => 'Lebar harus berupa angka',
            'width.min' => 'Lebar minimal 0.1 cm',
            'height.numeric' => 'Tinggi harus berupa angka',
            'height.min' => 'Tinggi minimal 0.1 cm',
            'warehouse_id.exists' => 'Gudang tidak ditemukan',
        ];
    }
}
