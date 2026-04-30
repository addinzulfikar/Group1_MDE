# Postman Collection - Sistem Pengiriman Paket

📮 **Dokumentasi lengkap untuk testing API Sistem Pengiriman Paket**

---

## 📋 Daftar Isi

1. [Setup Postman](#setup-postman)
2. [Ikhtisar API](#ikhtisar-api)
3. [Authentication Flow](#authentication-flow)
4. [Module 1: Warehouse & Package](#module-1-warehouse--package)
5. [Module 2: Tracking System](#module-2-tracking-system)
6. [Module 3: Customer Auth & Profile](#module-3-customer-auth--profile)
7. [Module 4: Fleet & Hub](#module-4-fleet--hub)
8. [Testing Scenarios](#testing-scenarios)

---

## 🚀 Setup Postman

### 1. Import Collection & Environment

```bash
# Import collection
File → Import → Sistem-Pengiriman-Paket-API.postman_collection.json

# Import environment
File → Import → Sistem-Pengiriman-Paket-ENV.postman_environment.json
```

### 2. Select Environment

```
Top Right Corner: Select "Sistem Pengiriman Paket - Environment"
```

### 3. Configure Base URL (Optional)

Jika menggunakan URL berbeda:

```
Environment → Edit → base_url → IP/Domain Anda
```

**Default:** `http://localhost`

### 4. Set API Version (Optional)

```
Environment → api_version: "v1" (atau sesuaikan)
```

---

## 📡 Ikhtisar API

### Architecture

```
┌─────────────────────────────────────────────────────────┐
│           SISTEM PENGIRIMAN PAKET (4 MODUL)             │
└─────────────────────────────────────────────────────────┘

┌──────────────────┐       ┌──────────────────┐
│  M1: WAREHOUSE   │       │   M2: TRACKING   │
│  & PACKAGE       │◄─────►│   & LOGISTICS    │
└──────────────────┘       └──────────────────┘
        │                         │
        │                         │
        ▼                         ▼
┌──────────────────┐       ┌──────────────────┐
│  M3: CUSTOMER    │       │  M4: FLEET &     │
│  & AUTH          │◄─────►│  HUB MANAGEMENT  │
└──────────────────┘       └──────────────────┘

API Routes:
/api/v1/auth/          → M3: Authentication
/api/v1/tracking/      → M2: Tracking (public)
/api/v1/customer/      → M2/M3: Private customer endpoints
/api/v1/package/       → M1: Package management
/api/v1/warehouse/     → M1: Warehouse management
/api/v1/fleet/         → M4: Fleet management
/api/v1/hub/           → M4: Hub management
```

### API Documentation

| Method | Endpoint | Auth | Module | Purpose |
|--------|----------|------|--------|---------|
| **POST** | `/auth/register` | ❌ | M3 | Register customer |
| **POST** | `/auth/login` | ❌ | M3 | Login customer |
| **POST** | `/auth/logout` | ✅ | M3 | Logout customer |
| **GET** | `/tracking/` | ❌ | M2 | Get all shipments (public) |
| **GET** | `/tracking/search` | ❌ | M2 | Search shipments |
| **GET** | `/tracking/{tracking_number}` | ❌ | M2 | Get tracking detail |
| **GET** | `/tracking/{tracking_number}/history` | ❌ | M2 | Get tracking history |
| **POST** | `/tracking` | ✅ | M2 | Create shipment |
| **POST** | `/shipment/from-package/{id}` | ✅ | M2 | Create shipment from package |
| **GET** | `/customer/shipments` | ✅ | M2/M3 | Get my shipments |
| **GET** | `/customer/shipments/{tracking}` | ✅ | M2/M3 | Get my shipment detail |
| **PATCH** | `/tracking/{tracking_number}/status` | ✅ | M2 | Update shipment status |
| **GET** | `/package/` | ❌ | M1 | List packages |
| **POST** | `/package/register` | ❌ | M1 | Register package |
| **GET** | `/package/{id}` | ❌ | M1 | Get package detail |
| **GET** | `/package/{id}/dimension` | ❌ | M1 | Get package dimension |
| **GET** | `/fleet/` | ❌ | M4 | List fleets |
| **POST** | `/fleet/` | ❌ | M4 | Create fleet |
| **GET** | `/fleet/{id}` | ❌ | M4 | Get fleet detail |
| **PUT** | `/fleet/{id}/status` | ❌ | M4 | Update fleet status |
| **GET** | `/hub/` | ❌ | M4 | List hubs |
| **GET** | `/hub/{id}/capacity` | ❌ | M4 | Check hub capacity |

---

## 🔐 Authentication Flow

### Step 1: Register Customer

**POST** `/api/v1/auth/register`

```json
{
  "name": "John Customer",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890",
  "address": "Jl. Merdeka No. 123, Jakarta"
}
```

**Response (201 Created):**

```json
{
  "status": "success",
  "message": "Registrasi berhasil!",
  "data": {
    "token": "5|abcdefghijklmnopqrstuvwxyz...",
    "user": {
      "id": 1,
      "name": "John Customer",
      "email": "john@example.com",
      "phone": "081234567890",
      "address": "Jl. Merdeka No. 123, Jakarta"
    }
  }
}
```

**Token otomatis disimpan** ke variabel `auth_token` via script Postman.

### Step 2: Login Customer

**POST** `/api/v1/auth/login`

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200 OK):**

```json
{
  "status": "success",
  "message": "Login berhasil!",
  "data": {
    "token": "5|abcdefghijklmnopqrstuvwxyz...",
    "user": {
      "id": 1,
      "name": "John Customer",
      "email": "john@example.com"
    }
  }
}
```

**Token otomatis disimpan** ke variabel `auth_token`.

### Step 3: Use Token in Requests

Semua request protected endpoints akan otomatis menggunakan token:

```
Authorization: Bearer {{auth_token}}
```

### Step 4: Logout

**POST** `/api/v1/auth/logout`

Headers:
```
Authorization: Bearer {{auth_token}}
```

---

## 📦 Module 1: Warehouse & Package

### A. Get Packages

**GET** `/api/v1/package?page=1`

```
Params:
- page: 1
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "tracking_number": "PKG2026040001",
        "sender_name": "PT. Barang Jaya",
        "receiver_name": "Toko Elektronik",
        "origin": "Jakarta",
        "destination": "Surabaya",
        "weight": 10.5,
        "warehouse_id": 1,
        "package_status": "in_warehouse"
      }
    ]
  }
}
```

### B. Register Package

**POST** `/api/v1/package/register`

```json
{
  "tracking_number": "PKG2026050001",
  "sender_name": "PT. Supplier",
  "receiver_name": "Toko Retail",
  "origin": "Medan",
  "destination": "Surabaya",
  "weight": 15.5,
  "length": 30,
  "width": 25,
  "height": 20,
  "warehouse_id": 1,
  "package_status": "in_warehouse"
}
```

**Response (201 Created):**

```json
{
  "status": "success",
  "message": "Paket berhasil terdaftar!",
  "data": {
    "id": 50,
    "tracking_number": "PKG2026050001",
    "sender_name": "PT. Supplier",
    "receiver_name": "Toko Retail",
    "weight": 15.5,
    "warehouse_id": 1
  }
}
```

### C. Get Package Detail

**GET** `/api/v1/package/1`

### D. Get Package Dimension Category

**GET** `/api/v1/package/1/dimension`

---

## 📍 Module 2: Tracking System

### A. Public Endpoints

#### 1. Get All Shipments (Public)

**GET** `/api/v1/tracking?status=pending&page=1`

Params:
- `status`: pending, in_transit, delivered (optional)
- `page`: pagination (default: 1)

#### 2. Search Shipments

**GET** `/api/v1/tracking/search?q=TRK00000001`

Params:
- `q`: keyword pencarian (tracking number, sender, receiver)

#### 3. Get Shipment by Tracking Number

**GET** `/api/v1/tracking/TRK00000001AB4A`

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "tracking_number": "TRK00000001AB4A",
    "customer_id": 44,
    "package_id": 22,
    "status": "pending",
    "origin_hub_id": 3,
    "destination_hub_id": 7,
    "current_hub_id": 3,
    "created_at": "2026-04-30T10:00:00Z",
    "customer": {
      "id": 44,
      "name": "Test User 43",
      "email": "test43@example.com"
    },
    "package": {
      "id": 22,
      "tracking_number": "PKG123",
      "sender_name": "Klikpak",
      "receiver_name": "John Doe",
      "weight": 28.1,
      "origin": "Medan",
      "destination": "Surabaya"
    }
  }
}
```

#### 4. Get Tracking History

**GET** `/api/v1/tracking/TRK00000001AB4A/history`

**Response:**

```json
{
  "status": "success",
  "tracking_number": "TRK00000001AB4A",
  "current_status": "pending",
  "history": [
    {
      "id": 1,
      "shipment_id": 1,
      "status": "pending",
      "from_hub_id": 3,
      "notes": "Paket telah terdaftar",
      "created_at": "2026-04-30T10:00:00Z"
    }
  ]
}
```

### B. Protected Endpoints (Require Auth)

#### 1. Create Shipment from Package

**POST** `/api/v1/shipment/from-package/{package_id}`

Headers:
```
Authorization: Bearer {{auth_token}}
```

Body:

```json
{
  "destination_hub_id": 8
}
```

**Response (201 Created):**

```json
{
  "status": "success",
  "message": "Pengiriman berhasil dibuat dari paket!",
  "data": {
    "id": 500,
    "tracking_number": "TRK1234567890AB",
    "customer_id": 1,
    "package_id": 22,
    "origin_hub_id": 3,
    "destination_hub_id": 8,
    "current_hub_id": 3,
    "status": "pending",
    "package": {
      "sender_name": "Klikpak",
      "receiver_name": "John Doe",
      "weight": 28.1
    }
  }
}
```

#### 2. Create Shipment Manually

**POST** `/api/v1/tracking`

Headers:
```
Authorization: Bearer {{auth_token}}
```

Body:

```json
{
  "package_id": 22,
  "destination_hub_id": 7
}
```

#### 3. Get My Shipments

**GET** `/api/v1/customer/shipments?status=pending&page=1`

Headers:
```
Authorization: Bearer {{auth_token}}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "tracking_number": "TRK123456",
        "status": "pending",
        "customer": {...},
        "package": {...}
      }
    ],
    "total": 15
  }
}
```

#### 4. Get My Shipment Detail

**GET** `/api/v1/customer/shipments/TRK00000001AB4A`

Headers:
```
Authorization: Bearer {{auth_token}}
```

#### 5. Update Shipment Status

**PATCH** `/api/v1/tracking/TRK00000001AB4A/status`

Headers:
```
Authorization: Bearer {{auth_token}}
```

Body:

```json
{
  "status": "in_transit",
  "current_hub_id": 7,
  "fleet_id": 1,
  "notes": "Paket sedang dalam perjalanan"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Status paket berhasil diperbarui!",
  "data": {
    "id": 1,
    "tracking_number": "TRK00000001AB4A",
    "status": "in_transit",
    "current_hub_id": 7,
    "fleet_id": 1
  }
}
```

---

## 👤 Module 3: Customer Auth & Profile

### A. Get Shipping Profile

**GET** `/api/v1/customer/shipping-profile`

Headers:
```
Authorization: Bearer {{auth_token}}
```

### B. Update Shipping Profile

**PUT** `/api/v1/customer/shipping-profile`

Headers:
```
Authorization: Bearer {{auth_token}}
```

Body:

```json
{
  "phone": "082345678901",
  "address": "Jl. Sudirman No. 456, Jakarta Pusat",
  "city": "Jakarta",
  "province": "DKI Jakarta",
  "postal_code": "12345"
}
```

### C. Calculate Shipping Cost

**POST** `/api/v1/customer/shipping-cost/calculate`

Headers:
```
Authorization: Bearer {{auth_token}}
```

Body:

```json
{
  "weight": 10,
  "from_hub_id": 1,
  "to_hub_id": 3,
  "service_type": "standard"
}
```

---

## 🚛 Module 4: Fleet & Hub

### A. Fleet Management

#### 1. Get All Fleets

**GET** `/api/v1/fleet?page=1`

#### 2. Create Fleet

**POST** `/api/v1/fleet`

Body:

```json
{
  "name": "Kurir Motor 01",
  "license_plate": "B 1234 ABC",
  "type": "motorcycle",
  "current_hub_id": 1,
  "capacity": 50
}
```

#### 3. Get Fleet Detail

**GET** `/api/v1/fleet/1`

#### 4. Update Fleet Status

**PUT** `/api/v1/fleet/1/status`

Body:

```json
{
  "status": "active"
}
```

Status: active, inactive, maintenance

#### 5. Relocate Fleet

**PUT** `/api/v1/fleet/1/relocate`

Body:

```json
{
  "hub_id": 3
}
```

#### 6. Get Transit Duration

**GET** `/api/v1/fleet/1/duration?from_hub_id=1&to_hub_id=3`

### B. Hub Management

#### 1. Get All Hubs

**GET** `/api/v1/hub?page=1`

#### 2. Check Hub Capacity

**GET** `/api/v1/hub/1/capacity`

**Response:**

```json
{
  "status": "success",
  "data": {
    "hub_id": 1,
    "hub_name": "Jakarta Depot",
    "total_capacity": 5000,
    "current_load": 2500,
    "available_capacity": 2500,
    "utilization_percentage": 50
  }
}
```

---

## 🧪 Testing Scenarios

### Scenario 1: Complete Workflow (Register → Create → Track → Update)

**Step 1**: Register customer
```
POST /api/v1/auth/register
```

**Step 2**: Create shipment from package
```
POST /api/v1/shipment/from-package/1
Body: { "destination_hub_id": 8 }
```

**Step 3**: Get customer shipments
```
GET /api/v1/customer/shipments
```

**Step 4**: Update shipment status
```
PATCH /api/v1/tracking/{tracking_number}/status
Body: { "status": "in_transit", "current_hub_id": 7 }
```

**Step 5**: Track shipment publicly
```
GET /api/v1/tracking/{tracking_number}
```

**Step 6**: Get tracking history
```
GET /api/v1/tracking/{tracking_number}/history
```

### Scenario 2: Package Management

**Step 1**: Register package
```
POST /api/v1/package/register
```

**Step 2**: Get package detail
```
GET /api/v1/package/{id}
```

**Step 3**: Get dimension category
```
GET /api/v1/package/{id}/dimension
```

### Scenario 3: Fleet Management

**Step 1**: Create fleet
```
POST /api/v1/fleet
```

**Step 2**: Update fleet status
```
PUT /api/v1/fleet/{id}/status
```

**Step 3**: Relocate fleet
```
PUT /api/v1/fleet/{id}/relocate
```

---

## 🔍 Tips & Tricks

### 1. Save Response Values

Postman dapat otomatis menyimpan nilai dari response:

```javascript
// In Tests tab
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set('tracking_number', jsonData.data.tracking_number);
}
```

### 2. Use Variables in Requests

```
GET /api/v1/tracking/{{tracking_number}}
```

### 3. Chain Requests

1. Setup request A untuk menyimpan data
2. Gunakan data tersebut di request B
3. Otomatis via pre-request scripts atau test scripts

### 4. Test Response

```javascript
pm.test("Status is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has tracking number", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data).to.have.property('tracking_number');
});
```

---

## 📊 Integration Architecture

```
Client (Postman)
    ↓
┌────────────────────────────────┐
│   API Gateway (Laravel)        │
│   /api/v1/...                  │
└────────────────────────────────┘
    ↓
┌────────────────────────────────┐
│   Auth Middleware (Sanctum)    │
│   auth:sanctum                 │
└────────────────────────────────┘
    ↓
┌──────────────────────────────────────────┐
│         Route Prefix Groups              │
├──────────────┬──────────────┬────────────┤
│   M1: Pkg    │  M2: Track   │ M3: Auth   │
│   M4: Fleet  │  -Customer   │ -Profile   │
│   -Hub       │  -Public     │            │
└──────────────┴──────────────┴────────────┘
    ↓
┌────────────────────────────────┐
│        Controllers             │
│  - TrackingController          │
│  - PackageController           │
│  - FleetController             │
│  - AuthController              │
└────────────────────────────────┘
    ↓
┌────────────────────────────────┐
│    Repository Pattern          │
│  - ShipmentRepository          │
│  - TrackingRepository          │
│  - PackageRepository           │
└────────────────────────────────┘
    ↓
┌────────────────────────────────┐
│    Database (MySQL 8.4)        │
│  - shipments (M2)              │
│  - packages (M1)               │
│  - users (M3)                  │
│  - fleets (M4)                 │
│  - hubs (M4)                   │
└────────────────────────────────┘
```

---

## 🐛 Troubleshooting

### Error: 401 Unauthorized

**Penyebab**: Token tidak valid atau expired

**Solusi**:
1. Login ulang
2. Pastikan token disimpan di `auth_token`
3. Pastikan header: `Authorization: Bearer {{auth_token}}`

### Error: 404 Not Found

**Penyebab**: Resource tidak ditemukan

**Solusi**:
1. Cek ID/tracking number yang digunakan
2. Data harus ada di database
3. Gunakan existing data dari collection examples

### Error: 422 Unprocessable Entity

**Penyebab**: Validation error

**Solusi**:
1. Cek request body format
2. Pastikan required fields terisi
3. Lihat error message di response

### Error: 500 Internal Server Error

**Penyebab**: Server error

**Solusi**:
1. Cek Laravel logs: `docker logs laravel.test`
2. Pastikan database terkoneksi
3. Restart container jika diperlukan

---

## 📝 Notes

- **Base URL**: Sesuaikan dengan environment (localhost, production, staging)
- **Auth Token**: Disimpan otomatis setelah login/register
- **PAGINAtion**: Default 15 items per page
- **Timezone**: UTC (lihat di created_at, updated_at)
- **Validation**: Semua endpoint sudah validated

---

**Last Updated**: 2026-04-30  
**Version**: 1.0  
**Status**: ✅ Complete & Tested
