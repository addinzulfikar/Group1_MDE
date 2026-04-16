# Sistem Pengiriman Paket - Modul 3

Implementasi Modul 3 (Tracking System) dengan fokus:

1. Autentikasi pelanggan berbasis token.
2. Kalkulator ongkir dinamis berbasis aturan tarif.
3. Manajemen profil pengiriman pelanggan.
4. Pemisahan akses data menggunakan Repository Pattern.

## Stack

1. Laravel 13
2. PHP 8.3
3. MySQL 8.4 (untuk Docker)

## Menjalankan Dengan Docker

1. Salin file environment:

```bash
cp .env.example .env
```

2. Atur konfigurasi DB di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=spp_db
DB_USERNAME=spp_user
DB_PASSWORD=spp_pass
```

3. Jalankan container:

```bash
docker compose up -d --build
```

4. Jalankan migrasi dan seeder:

```bash
docker compose exec laravel.test php artisan migrate --seed
```

Catatan:

- `php artisan migrate` hanya membuat tabel, tidak menjalankan seeder.
- Untuk reset + isi ulang data dummy gunakan:

```bash
docker compose exec laravel.test php artisan migrate:fresh --seed
```

5. Akses aplikasi:

```text
http://localhost:8000
```

## Menjalankan Lokal Tanpa Docker

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Endpoint API Modul 3

Base URL: `/api/v1`

1. `POST /auth/register`
2. `POST /auth/login`
3. `POST /auth/logout` (butuh Bearer Token)
4. `GET /customer/shipping-profile` (butuh Bearer Token)
5. `PUT /customer/shipping-profile` (butuh Bearer Token)
6. `POST /customer/shipping-cost/calculate` (butuh Bearer Token)

## Contoh Request

### Register

```json
{
	"name": "Budi Santoso",
	"email": "budi@example.com",
	"phone": "081234567890",
	"password": "rahasia123",
	"password_confirmation": "rahasia123",
	"address": "Bandung",
	"device_name": "android-budi"
}
```

### Kalkulator Ongkir

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

## Catatan Pengujian

Feature test khusus Modul 3 tersedia di `tests/Feature/Module3TrackingSystemTest.php`.
