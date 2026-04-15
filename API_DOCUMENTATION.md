# API Documentation - Sistem Pengiriman Paket (Module 3)

## Overview

Dokumen ini mencakup seluruh API yang saat ini tersedia pada project.
Semua endpoint API berada di prefix berikut:

- Base path: /api/v1

Contoh base URL:

- Docker: http://127.0.0.1:8000/api/v1

## Authentication Model

Autentikasi menggunakan Bearer Token kustom (bukan Laravel Sanctum/Passport).

Flow:

1. Register atau login untuk mendapatkan token.
2. Kirim token di header Authorization untuk endpoint yang diproteksi.

Header untuk endpoint protected:

Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json

## Endpoint Summary

Public endpoints:

1. POST /auth/register
2. POST /auth/login

Protected endpoints (customer.auth middleware):

1. POST /auth/logout
2. GET /customer/shipping-profile
3. PUT /customer/shipping-profile
4. POST /customer/shipping-cost/calculate

## 1) Register Customer

- Method: POST
- URL: /api/v1/auth/register
- Auth: No

Request body:

```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "phone": "081234567890",
  "password": "rahasia123",
  "password_confirmation": "rahasia123",
  "address": "Bandung",
  "device_name": "web-client"
}
```

Validation rules:

- name: required, string, max 100
- email: required, email, max 150, unique users.email
- phone: required, string, max 30
- password: required, string, min 8, confirmed
- address: nullable, string, max 255
- device_name: nullable, string, max 100

Success response:

- Status: 201

```json
{
  "message": "Registrasi pelanggan berhasil.",
  "data": {
    "token": "<plain_token>",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phone": "081234567890",
      "address": "Bandung"
    }
  }
}
```

Common error response:

- Status: 422 (validation error)

## 2) Login Customer

- Method: POST
- URL: /api/v1/auth/login
- Auth: No

Request body:

```json
{
  "email": "budi@example.com",
  "password": "rahasia123",
  "device_name": "web-client"
}
```

Validation rules:

- email: required, email
- password: required, string
- device_name: nullable, string, max 100

Success response:

- Status: 200

```json
{
  "message": "Login berhasil.",
  "data": {
    "token": "<plain_token>",
    "token_type": "Bearer"
  }
}
```

Common error responses:

- Status: 422

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "Email atau password tidak valid."
    ]
  }
}
```

## 3) Logout Customer

- Method: POST
- URL: /api/v1/auth/logout
- Auth: Yes (Bearer token)

Success response:

- Status: 200

```json
{
  "message": "Logout berhasil."
}
```

Common error responses:

- Status: 401

```json
{
  "message": "Token autentikasi tidak ditemukan."
}
```

atau

```json
{
  "message": "Token tidak valid atau sudah tidak aktif."
}
```

## 4) Get Customer Shipping Profile

- Method: GET
- URL: /api/v1/customer/shipping-profile
- Auth: Yes (Bearer token)

Success response:

- Status: 200

```json
{
  "message": "Profil pengiriman pelanggan.",
  "data": {
    "id": 1,
    "user_id": 1,
    "sender_name": "Budi Santoso",
    "sender_phone": "081234567890",
    "default_pickup_address": "Jl. Asia Afrika No. 10",
    "default_origin_city": "Bandung",
    "default_origin_postal_code": "40111",
    "preferred_service_type": "regular",
    "preferred_package_type": "box",
    "notes": "Pickup jam 09:00 - 12:00",
    "created_at": "2026-04-15T00:00:00.000000Z",
    "updated_at": "2026-04-15T00:00:00.000000Z"
  }
}
```

Catatan:

- Jika profile belum ada, data dapat bernilai null.

## 5) Create/Update Customer Shipping Profile

- Method: PUT
- URL: /api/v1/customer/shipping-profile
- Auth: Yes (Bearer token)

Request body:

```json
{
  "sender_name": "Budi Santoso",
  "sender_phone": "081234567890",
  "default_pickup_address": "Jl. Asia Afrika No. 10",
  "default_origin_city": "Bandung",
  "default_origin_postal_code": "40111",
  "preferred_service_type": "regular",
  "preferred_package_type": "box",
  "notes": "Pickup jam 09:00 - 12:00"
}
```

Validation rules:

- sender_name: required, string, max 100
- sender_phone: required, string, max 30
- default_pickup_address: required, string, max 255
- default_origin_city: required, string, max 100
- default_origin_postal_code: required, string, max 12
- preferred_service_type: required, in regular|express|same_day
- preferred_package_type: nullable, string, max 50
- notes: nullable, string, max 500

Success response:

- Status: 200

```json
{
  "message": "Profil pengiriman berhasil disimpan.",
  "data": {
    "id": 1,
    "user_id": 1,
    "sender_name": "Budi Santoso",
    "sender_phone": "081234567890",
    "default_pickup_address": "Jl. Asia Afrika No. 10",
    "default_origin_city": "Bandung",
    "default_origin_postal_code": "40111",
    "preferred_service_type": "regular",
    "preferred_package_type": "box",
    "notes": "Pickup jam 09:00 - 12:00",
    "created_at": "2026-04-15T00:00:00.000000Z",
    "updated_at": "2026-04-15T00:00:00.000000Z"
  }
}
```

## 6) Dynamic Shipping Cost Calculator

- Method: POST
- URL: /api/v1/customer/shipping-cost/calculate
- Auth: Yes (Bearer token)

Request body:

```json
{
  "weight_kg": 3.5,
  "distance_km": 120,
  "service_type": "express",
  "is_fragile": true,
  "use_insurance": true,
  "declared_value": 500000
}
```

Validation rules:

- weight_kg: required, numeric, min 0.1
- distance_km: required, numeric, min 1
- service_type: required, in regular|express|same_day
- is_fragile: nullable, boolean
- use_insurance: nullable, boolean
- declared_value: nullable, numeric, min 0

Success response:

- Status: 200

```json
{
  "message": "Perhitungan ongkir berhasil.",
  "data": {
    "service_type": "express",
    "weight_kg": 3.5,
    "distance_km": 120,
    "cost_breakdown": {
      "base_cost": 12000,
      "distance_cost": 120000,
      "weight_cost": 14000,
      "fuel_surcharge": 11680,
      "fragile_surcharge": 3000,
      "insurance_cost": 3750
    },
    "total_cost": 164430,
    "currency": "IDR",
    "estimated_sla_days": 2
  }
}
```

Common error responses:

- Status: 401 (missing/invalid token)
- Status: 422 (validation error or rule not found)

Contoh rule not found:

```json
{
  "message": "Aturan ongkir untuk kombinasi layanan dan jarak belum tersedia."
}
```

## Middleware Behavior (customer.auth)

Untuk semua endpoint protected:

1. Jika token tidak dikirim:

```json
{
  "message": "Token autentikasi tidak ditemukan."
}
```

2. Jika token invalid, expired, revoked, atau user bukan customer:

```json
{
  "message": "Token tidak valid atau sudah tidak aktif."
}
```

## Quick cURL Examples

Register:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Budi Santoso",
    "email":"budi@example.com",
    "phone":"081234567890",
    "password":"rahasia123",
    "password_confirmation":"rahasia123",
    "address":"Bandung",
    "device_name":"web-client"
  }'
```

Login:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email":"budi@example.com",
    "password":"rahasia123",
    "device_name":"web-client"
  }'
```

Hitung ongkir (ganti TOKEN):

```bash
curl -X POST http://127.0.0.1:8000/api/v1/customer/shipping-cost/calculate \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "weight_kg":3.5,
    "distance_km":120,
    "service_type":"express",
    "is_fragile":true,
    "use_insurance":true,
    "declared_value":500000
  }'
```

## Source of Truth

Endpoint definitions:

- routes/api.php

Controller implementations:

- app/Http/Controllers/Api/CustomerAuthController.php
- app/Http/Controllers/Api/ShippingProfileController.php
- app/Http/Controllers/Api/ShippingCalculatorController.php

Auth middleware:

- app/Http/Middleware/AuthenticateCustomerToken.php
