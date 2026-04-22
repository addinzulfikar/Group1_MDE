<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'tracking_number' => 'required|string|unique:packages,tracking_number|max:50',
            'sender_name' => 'required|string|max:100',
            'receiver_name' => 'required|string|max:100',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'required|numeric|min:0.1',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'package_status' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom message for validation rules.
     */
    public function messages(): array
    {
        return [
            'tracking_number.required' => 'Nomor tracking harus diisi',
            'tracking_number.unique' => 'Nomor tracking sudah terdaftar',
            'sender_name.required' => 'Nama pengirim harus diisi',
            'receiver_name.required' => 'Nama penerima harus diisi',
            'origin.required' => 'Asal pengiriman harus diisi',
            'destination.required' => 'Tujuan pengiriman harus diisi',
            'weight.required' => 'Berat paket harus diisi',
            'weight.numeric' => 'Berat harus berupa angka',
            'weight.min' => 'Berat minimal 0.1 kg',
            'length.required' => 'Panjang paket harus diisi',
            'length.numeric' => 'Panjang harus berupa angka',
            'length.min' => 'Panjang minimal 0.1 cm',
            'width.required' => 'Lebar paket harus diisi',
            'width.numeric' => 'Lebar harus berupa angka',
            'width.min' => 'Lebar minimal 0.1 cm',
            'height.required' => 'Tinggi paket harus diisi',
            'height.numeric' => 'Tinggi harus berupa angka',
            'height.min' => 'Tinggi minimal 0.1 cm',
            'warehouse_id.required' => 'ID gudang harus diisi',
            'warehouse_id.exists' => 'Gudang tidak ditemukan',
        ];
    }
}
