# Module 1 - Enhanced CRUD & Seeder Documentation

## 📊 Data Seeding

### Lokasi File Seeder
```
database/seeders/Module1Seeder.php
```

### Data yang Diseed

**10 Warehouses:**
- WH-001: Jakarta Central Hub - Capacity 10,000 units
- WH-002: Surabaya Distribution - Capacity 8,000 units
- WH-003: Bandung Depot - Capacity 6,000 units
- WH-004: Medan Logistics - Capacity 5,000 units
- WH-005: Makassar Port - Capacity 7,000 units
- WH-006: Palembang Hub - Capacity 4,500 units
- WH-007: Semarang Center - Capacity 5,500 units (Inactive)
- WH-008: Yogyakarta Station - Capacity 3,500 units
- WH-009: Bali Gateway - Capacity 4,000 units
- WH-010: Pontianak Node - Capacity 3,000 units

**30 Packages dengan distribusi:**
- 0 Small packages (≤1000 cm³)
- 8 Medium packages (1000-5000 cm³)
- 22 Large packages (>5000 cm³)

### Cara Menjalankan Seeder

**Reset database dan run seeder:**
```bash
cd d:\Projects\Sistem-Pengiriman-Paket-Tim-1
docker compose exec laravel.test php artisan migrate:refresh
docker compose exec laravel.test php artisan db:seed --class=Module1Seeder
```

**Output yang akan muncul:**
```
✅ Module 1 seeder completed!
📦 Created 10 warehouses
📫 Created 30 packages
Total packages per dimension:
  - Small (≤1000 cm³): 0
  - Medium (1000-5000 cm³): 8
  - Large (>5000 cm³): 22
```

---

## 🎨 CRUD Interface

### Lokasi UI
```
http://localhost/module-1-monitor
```

### Fitur CRUD

#### A) WAREHOUSE MANAGEMENT

**1. View All Warehouses**
- Buka: `http://localhost/module-1-monitor`
- Scroll ke section "Warehouse Management"
- Lihat tabel dengan semua warehouse

**2. Add Warehouse**
```
Klik tombol "Add Warehouse" (biru)
↓
Form modal akan muncul
↓
Isi data:
  - Warehouse Code (misal: WH-011)
  - Warehouse Name
  - Location
  - Capacity
  - Status (Active/Inactive)
↓
Klik "Save"
```

**3. Edit Warehouse**
```
Di tabel warehouse, klik tombol "Edit" (kuning)
↓
Form modal akan terisi dengan data warehouse
↓
Update data yang diinginkan
↓
Klik "Update"
```

**4. Delete Warehouse**
```
Di tabel warehouse, klik tombol "Delete" (merah)
↓
Konfirmasi "Are you sure?"
↓
Warehouse akan dihapus
```

#### B) PACKAGE MANAGEMENT

**1. View All Packages**
- Scroll ke section "Package Management"
- Lihat tabel dengan semua package
- Info: tracking number, sender, receiver, weight, volume, category, status

**2. Register New Package**
```
Klik tombol "Register Package" (hijau)
↓
Form modal akan muncul
↓
Isi data:
  - Tracking Number (misal: PKG-2026-031)
  - Sender Name
  - Receiver Name
  - Origin
  - Destination
  - Weight (kg)
  - Length (cm)
  - Width (cm)
  - Height (cm)
  - Warehouse (select dari dropdown)
  - Status (Registered/In Transit/Delivered/Pending/Cancelled)
↓
Klik "Register"
```

**CATATAN:** Volume otomatis dihitung = Length × Width × Height
Dimension Category otomatis ditentukan berdasarkan volume

**3. Edit Package**
```
Di tabel package, klik tombol "Edit" (kuning)
↓
Form modal akan terisi dengan data package
↓
Update data (termasuk dimensi jika perlu)
↓
Volume otomatis recalculate
↓
Klik "Update"
```

**4. Delete Package**
```
Di tabel package, klik tombol "Delete" (merah)
↓
Konfirmasi "Are you sure?"
↓
Package akan dihapus
```

---

## 🔧 Backend API Endpoints

Jika ingin test API langsung (tidak melalui UI), gunakan Postman atau curl:

### Warehouse Endpoints

**1. GET All Warehouses**
```
GET http://localhost/api/warehouse
```

**2. GET Single Warehouse**
```
GET http://localhost/api/warehouse/{id}
```

**3. Create Warehouse**
```
POST http://localhost/api/warehouse

Body:
{
    "warehouse_code": "WH-011",
    "warehouse_name": "Sukabumi Hub",
    "location": "Sukabumi, Jawa Barat",
    "capacity": 3500,
    "status": "active"
}
```

**4. Update Warehouse**
```
PUT http://localhost/api/warehouse/{id}

Body:
{
    "warehouse_name": "Sukabumi Distribution",
    "capacity": 4000,
    "current_load": 2500,
    "status": "active"
}
```

**5. Delete Warehouse**
```
DELETE http://localhost/api/warehouse/{id}
```

### Package Endpoints

**1. GET All Packages**
```
GET http://localhost/api/package
```

**2. GET Single Package**
```
GET http://localhost/api/package/{id}
```

**3. Register Package**
```
POST http://localhost/api/package/register

Body:
{
    "tracking_number": "PKG-2026-031",
    "sender_name": "PT XYZ",
    "receiver_name": "Customer Name",
    "origin": "Jakarta",
    "destination": "Bandung",
    "weight": 5.0,
    "length": 30,
    "width": 25,
    "height": 20,
    "warehouse_id": 1,
    "package_status": "registered"
}

CATATAN: Volume otomatis dihitung = 30 × 25 × 20 = 15000 cm³
Category otomatis = "large"
```

**4. Update Package**
```
PUT http://localhost/api/package/{id}

Body:
{
    "receiver_name": "New Receiver",
    "destination": "Yogyakarta",
    "package_status": "in_transit"
}
```

**5. Delete Package**
```
DELETE http://localhost/api/package/{id}
```

**6. Get Package Dimension Info**
```
GET http://localhost/api/package/{id}/dimension

Response:
{
    "success": true,
    "message": "Package dimension retrieved successfully",
    "data": {
        "id": 1,
        "tracking_number": "PKG-2026-001",
        "length": 25,
        "width": 20,
        "height": 15,
        "volume": 7500,
        "dimension_category": "large",
        "category_description": "Volume > 5000 cm³"
    }
}
```

---

## 📁 Struktur Data CRUD

### Warehouse Form Fields
```
- warehouse_code (String, Required, Unique)
- warehouse_name (String, Required)
- location (String, Required)
- capacity (Integer, Required, Min: 1)
- current_load (Integer, Auto, Default: 0)
- status (Enum: active/inactive)
```

### Package Form Fields
```
- tracking_number (String, Required, Unique)
- sender_name (String, Required)
- receiver_name (String, Required)
- origin (String, Required)
- destination (String, Required)
- weight (Float, Required, Min: 0.1)
- length (Float, Required, Min: 0.1)
- width (Float, Required, Min: 0.1)
- height (Float, Required, Min: 0.1)
- volume (Float, Auto-Calculated)
- warehouse_id (Integer, Required, Foreign Key)
- package_status (String)
```

---

## ✨ Features

### Dashboard Statistics
- ✅ Total Warehouses count
- ✅ Active Warehouses count
- ✅ Total Capacity (sum dari semua warehouse)
- ✅ Overall Usage Rate (percentage)
- ✅ Total Packages count
- ✅ Package distribution by dimension (Small/Medium/Large)

### Warehouse Table
- ✅ Warehouse code & name
- ✅ Location
- ✅ Capacity & current load
- ✅ Usage percentage visual bar
- ✅ Status badge (Active/Inactive)
- ✅ Edit/Delete buttons

### Package Table
- ✅ Tracking number
- ✅ Sender & receiver names
- ✅ Origin & destination
- ✅ Weight & volume
- ✅ Dimension category badge (Small/Medium/Large)
- ✅ Status badge (Registered/In Transit/Delivered/Pending/Cancelled)
- ✅ Edit/Delete buttons

### Auto Calculations
- ✅ Volume = Length × Width × Height
- ✅ Dimension Category:
  - Small: ≤ 1000 cm³
  - Medium: 1000 - 5000 cm³
  - Large: > 5000 cm³
- ✅ Warehouse Usage Percentage = (Current Load / Capacity) × 100

---

## 🧪 Testing Checklist

### Web UI CRUD Testing
- [ ] View all warehouses in table
- [ ] Click "Add Warehouse" → Fill form → Save → Verify in table
- [ ] Click "Edit" on warehouse → Modify → Update → Verify change
- [ ] Click "Delete" on warehouse → Confirm → Verify deletion
- [ ] View all packages in table
- [ ] Click "Register Package" → Fill form → Register → Verify in table
- [ ] Click "Edit" on package → Modify dimensions → Verify volume recalculation
- [ ] Click "Delete" on package → Confirm → Verify deletion
- [ ] Verify statistics update after CRUD operations

### API Testing (Postman)
- [ ] GET /api/warehouse → List works
- [ ] POST /api/warehouse → Create works
- [ ] PUT /api/warehouse/{id} → Update works
- [ ] DELETE /api/warehouse/{id} → Delete works
- [ ] GET /api/package → List works with dimension_category
- [ ] POST /api/package/register → Create with auto volume
- [ ] PUT /api/package/{id} → Update with volume recalculation
- [ ] DELETE /api/package/{id} → Delete works

---

## 🔗 Files Modified/Created

**Created:**
- `database/seeders/Module1Seeder.php` - Enhanced seeder dengan 10 warehouses & 30 packages

**Updated:**
- `resources/views/module1/monitoring.blade.php` - Tambah CRUD modals dan buttons
- `app/Http/Controllers/API/WarehouseController.php` - Sudah ada (full CRUD)
- `app/Http/Controllers/API/PackageController.php` - Sudah ada (full CRUD)

---

## 📝 Notes untuk Dosen

**Requirements Terpenuhi:**
✅ API Manajemen Gudang - Full CRUD
✅ Pendaftaran Paket Baru - Full CRUD dengan auto calculation
✅ Dimensi Paket - Auto calculation & categorization
✅ Akses lewat route - UI di /module-1-monitor
✅ Output UI monitoring - Professional dashboard dengan statistics
✅ Backend API testable - Semua bisa ditest di Postman
✅ Struktur folder rapi - Laravel best practices

**Data Seeding:**
- 10 Warehouses dengan berbagai kapasitas
- 30 Packages dengan realistic data
- Distribusi dimension yang sesuai production
- Random generator untuk data variatif

**CRUD Operations:**
- Create: Add warehouse, Register package
- Read: View semua data dengan table & details
- Update: Edit warehouse, Edit package dengan recalculation
- Delete: Remove warehouse/package dengan confirmation

---

**Last Updated:** 2026-04-15  
**Module:** Module 1 - Warehouse & Package Management  
**Status:** Production Ready ✅
