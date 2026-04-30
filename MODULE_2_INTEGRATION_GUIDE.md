# 🚀 MODULE 2 INTEGRATION GUIDE

**Last Updated:** 30 April 2026

After revision, Module 2 now properly integrates with M1, M3, and M4. Follow these steps to deploy and test.

---

## 1️⃣ Run Migration

```bash
# Run all pending migrations
php artisan migrate

# Or run fresh with seeder (WARNING: clears all data!)
php artisan migrate:fresh --seed
```

**Expected Output:**
```
Migrating: 2026_04_30_000000_add_module_dependencies_to_shipments_table
Migrated:  2026_04_30_000000_add_module_dependencies_to_shipments_table (0.12 seconds)
```

---

## 2️⃣ Verify Database Schema

```bash
php artisan tinker

# Check Shipment table structure
>>> Schema::getColumns('shipments')
>>> Schema::getIndexes('shipments')

# Verify relationships
>>> Shipment::find(1)->load('customer', 'package', 'fleet', 'currentHub')->toArray()
```

---

## 3️⃣ Test API Endpoints

### **A) Authentication (M3)**

#### Register Customer
```bash
POST http://localhost:8000/api/v1/auth/register
Content-Type: application/json

{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "phone": "081234567890",
  "password": "password123",
  "password_confirmation": "password123",
  "address": "Bandung",
  "device_name": "postman"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      ...
    },
    "token": "abc123def456..."
  }
}
```

#### Login Customer
```bash
POST http://localhost:8000/api/v1/auth/login
Content-Type: application/json

{
  "email": "budi@example.com",
  "password": "password123",
  "device_name": "postman"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "abc123def456..."
  }
}
```

**Save token for next requests! ↓**
```
Authorization: Bearer abc123def456...
```

---

### **B) Create Shipment from Package (M1 Integration)**

#### Prerequisites:
- Must have active warehouse with packages
- Must be logged in (have Bearer token)

#### Request:
```bash
POST http://localhost:8000/api/v1/shipment/from-package/1
Content-Type: application/json
Authorization: Bearer {TOKEN}

{
  "destination_hub_id": 3
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Pengiriman berhasil dibuat dari paket!",
  "data": {
    "id": 1,
    "tracking_number": "TRK1234567890",
    "customer_id": 5,
    "package_id": 1,
    "sender_name": "John Doe",
    "origin_hub_id": 2,
    "destination_hub_id": 3,
    "current_hub_id": 2,
    "fleet_id": null,
    "status": "pending",
    "package": {
      "id": 1,
      "tracking_number": "...",
      "warehouse_id": 5,
      ...
    },
    "trackingHistories": [
      {
        "id": 1,
        "status": "pending",
        "from_hub_id": 2,
        "notes": "Paket dari gudang telah terdaftar untuk pengiriman",
        ...
      }
    ]
  }
}
```

---

### **C) Create Shipment Manually (M3 Integration)**

```bash
POST http://localhost:8000/api/v1/tracking
Content-Type: application/json
Authorization: Bearer {TOKEN}

{
  "sender_name": "John Doe",
  "sender_phone": "081234567890",
  "sender_address": "Bandung",
  "receiver_name": "Jane Smith",
  "receiver_phone": "082345678901",
  "receiver_address": "Jakarta",
  "weight": 2.5,
  "length": 30,
  "width": 20,
  "height": 15,
  "origin_hub_id": 1,
  "destination_hub_id": 2
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Paket berhasil didaftarkan!",
  "data": {
    "id": 2,
    "tracking_number": "TRK9876543210",
    "customer_id": 5,
    "status": "pending",
    "current_hub_id": 1,
    ...
  }
}
```

---

### **D) View Customer's Shipments (M3 Protected)**

```bash
GET http://localhost:8000/api/v1/customer/shipments
Authorization: Bearer {TOKEN}
```

**Query Parameters:**
```
?status=pending
?status=delivered
?page=1
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
        "tracking_number": "TRK1234567890",
        "customer_id": 5,
        "status": "pending",
        "current_hub_id": 2,
        "fleet_id": null,
        "customer": {...},
        "fleet": null,
        "currentHub": {
          "id": 2,
          "name": "Hub Jakarta",
          "capacity": 10000,
          ...
        },
        "trackingHistories": [...]
      }
    ],
    "total": 5,
    "per_page": 15,
    "last_page": 1
  }
}
```

---

### **E) View Specific Shipment (M3 Protected)**

```bash
GET http://localhost:8000/api/v1/customer/shipments/TRK1234567890
Authorization: Bearer {TOKEN}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "tracking_number": "TRK1234567890",
    "customer_id": 5,
    "package_id": 1,
    "status": "pending",
    "current_hub_id": 2,
    "fleet_id": null,
    "originHub": {...},
    "destinationHub": {...},
    "currentHub": {...},
    "package": {...},
    "fleet": null,
    "trackingHistories": [...]
  }
}
```

---

### **F) Update Shipment Status with Fleet (M4 Integration)**

#### Scenario: System assigns fleet and tracking updates

```bash
PATCH http://localhost:8000/api/v1/tracking/TRK1234567890/status
Content-Type: application/json

{
  "status": "picked_up",
  "fleet_id": 50,
  "current_hub_id": 2,
  "notes": "Fleet 50 picked up paket from warehouse"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Status paket berhasil diperbarui!",
  "data": {
    "id": 1,
    "tracking_number": "TRK1234567890",
    "status": "picked_up",
    "fleet_id": 50,
    "current_hub_id": 2,
    "fleet": {
      "id": 50,
      "plate_number": "B123CD",
      "type": "van",
      "capacity": 5000,
      ...
    },
    "trackingHistories": [
      {
        "id": 2,
        "status": "picked_up",
        "from_hub_id": 2,
        "notes": "Fleet 50 picked up paket from warehouse",
        "recorded_at": "2026-04-30 15:45:00"
      }
    ]
  }
}
```

#### Fleet in transit
```bash
PATCH http://localhost:8000/api/v1/tracking/TRK1234567890/status
Content-Type: application/json

{
  "status": "in_transit",
  "current_hub_id": 2,
  "notes": "Fleet in transit to hub Surabaya"
}
```

#### Fleet arrives at hub
```bash
PATCH http://localhost:8000/api/v1/tracking/TRK1234567890/status
Content-Type: application/json

{
  "status": "in_hub",
  "current_hub_id": 3,
  "notes": "Paket arrived at Surabaya hub"
}
```

#### Delivered
```bash
PATCH http://localhost:8000/api/v1/tracking/TRK1234567890/status
Content-Type: application/json

{
  "status": "delivered",
  "current_hub_id": 3,
  "notes": "Paket delivered to customer"
}
```

---

### **G) Public Tracking Search (No Auth Required)**

#### Search by tracking number
```bash
GET http://localhost:8000/api/v1/tracking/TRK1234567890
```

#### View tracking history
```bash
GET http://localhost:8000/api/v1/tracking/TRK1234567890/history
```

#### Search tracking
```bash
GET http://localhost:8000/api/v1/tracking/search?q=TRK1234567890
```

---

## 4️⃣ Verify Database Relationships

```bash
php artisan tinker

# Check shipment with all relationships
>>> $shipment = Shipment::with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub', 'trackingHistories'])->first()

# Check customer owns shipment
>>> $customer = User::find(1)
>>> $customer->shipments()->count()  // Should show customer's shipments

# Check package has shipment
>>> $package = Package::find(1)
>>> $package->shipment  // Should show associated shipment

# Check fleet has shipments
>>> $fleet = Fleet::find(1)
>>> $fleet->shipments()->count()  // Should show assigned shipments
```

---

## 5️⃣ Common Issues & Solutions

### Issue: "Unauthorized" when accessing protected endpoints

**Solution:**
- Check Bearer token is valid (`/auth/login`)
- Verify token format: `Authorization: Bearer {token}`
- Check token hasn't expired

### Issue: "Paket tidak ditemukan" when creating from package

**Solution:**
- Verify package_id exists
- Check package is in valid warehouse with hub_id
- Try: `GET /api/v1/package/{id}` first

### Issue: Foreign key constraint error

**Solution:**
- Run migration: `php artisan migrate`
- Check all hub_id, package_id, customer_id, fleet_id exist
- May need seeder: `php artisan db:seed`

### Issue: "Pengiriman tidak ditemukan atau bukan milik Anda"

**Solution:**
- Verify customer is logged in with correct token
- Shipment belongs to different customer
- Try: `GET /api/v1/tracking/{tracking_number}` (public endpoint)

---

## 6️⃣ Testing Checklist

- [ ] Register new customer (M3)
- [ ] Login customer (M3)
- [ ] Create package in warehouse (M1)
- [ ] Create shipment from package (M1+M3)
- [ ] View customer's shipments (M3 protected)
- [ ] View specific shipment (M3 protected)
- [ ] Update shipment status with fleet (M4)
- [ ] Track fleet movement through hubs (M4)
- [ ] Verify customer can only see own shipments
- [ ] Public tracking still works (no auth)

---

## 7️⃣ Database Backup

Before running migration on production:

```bash
# Backup database
mysqldump -u root -p spp_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Or if using Docker
docker compose exec db mysqldump -u spp_user -pspp_pass spp_db > backup.sql
```

---

**Module 2 Integration Deployment Guide Complete! ✅**
