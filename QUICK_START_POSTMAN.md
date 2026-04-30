# Quick Start Guide - Postman Testing

## 📥 Import Files

```bash
# 1. Import Collection
File → Import → "Sistem-Pengiriman-Paket-API.postman_collection.json"

# 2. Import Environment
File → Import → "Sistem-Pengiriman-Paket-ENV.postman_environment.json"

# 3. Select Environment (Top Right)
Environment: "Sistem Pengiriman Paket - Environment"
```

---

## ⚡ Quick Test (5 menit)

### 1. Login (Get Token)
```
[MODUL 3: Authentication] → Login Customer

Body:
{
  "email": "test43@example.com",
  "password": "password"
}

✅ Token otomatis disimpan ke {{auth_token}}
```

### 2. View Shipments (Public)
```
[MODUL 2: Tracking System - Public] → Get Shipment by Tracking Number

URL: .../tracking/TRK00000001AB4A

✅ Lihat data paket (tidak perlu login)
```

### 3. Get My Shipments (Protected)
```
[MODUL 2: Tracking System - Protected] → Get My Shipments

Headers: Authorization: Bearer {{auth_token}} ✓ Otomatis

✅ Hanya lihat paket milik saya (M3 integration)
```

### 4. Create Shipment (Protected)
```
[MODUL 2: Tracking System - Protected] → Create Shipment from Package

Body:
{
  "destination_hub_id": 8
}

✅ Shipment baru created dengan package data
```

### 5. Update Status (Protected)
```
[MODUL 2: Tracking System - Protected] → Update Shipment Status

Body:
{
  "status": "in_transit",
  "current_hub_id": 7,
  "fleet_id": 1,
  "notes": "Paket sedang dalam perjalanan"
}

✅ Status updated + tracking history recorded
```

---

## 📊 API Routes Quick Reference

### Public Endpoints (❌ No Auth)
```
GET     /api/v1/tracking/
GET     /api/v1/tracking/search?q=...
GET     /api/v1/tracking/{tracking}
GET     /api/v1/tracking/{tracking}/history
GET     /api/v1/package/
GET     /api/v1/warehouse/
GET     /api/v1/fleet/
GET     /api/v1/hub/
```

### Protected Endpoints (✅ Require {{auth_token}})
```
POST    /api/v1/auth/logout
POST    /api/v1/tracking
POST    /api/v1/shipment/from-package/{pkg_id}
GET     /api/v1/customer/shipments
GET     /api/v1/customer/shipments/{tracking}
PATCH   /api/v1/tracking/{tracking}/status
GET     /api/v1/customer/shipping-profile
PUT     /api/v1/customer/shipping-profile
```

---

## 🔐 Authentication Flow

### Register (Optional)
```
POST /api/v1/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "081234567890"
}
→ Returns token ✓
```

### Login (Required)
```
POST /api/v1/auth/login
{
  "email": "test43@example.com",
  "password": "password"
}
→ Token saved to {{auth_token}} ✓
```

### Use Token
```
Automatically added to all protected requests:
Authorization: Bearer {{auth_token}}
```

---

## 📍 Module Integration Map

### M1: Warehouse & Package
- GET `/api/v1/warehouse/` - List warehouses
- GET `/api/v1/package/` - List packages
- POST `/api/v1/package/register` - Register package

### M2: Tracking System
- GET `/api/v1/tracking/` - All shipments (public)
- POST `/api/v1/tracking` - Create shipment (protected)
- POST `/api/v1/shipment/from-package/{id}` - Create from package
- GET `/api/v1/customer/shipments` - My shipments (protected)
- PATCH `/api/v1/tracking/{tracking}/status` - Update status

### M3: Customer Auth
- POST `/api/v1/auth/register` - Register
- POST `/api/v1/auth/login` - Login
- POST `/api/v1/auth/logout` - Logout (protected)
- GET `/api/v1/customer/shipping-profile` - My profile (protected)

### M4: Fleet & Hub
- GET `/api/v1/fleet/` - List fleets
- POST `/api/v1/fleet/` - Create fleet
- GET `/api/v1/hub/` - List hubs
- GET `/api/v1/hub/{id}/capacity` - Check capacity

---

## 🧪 Test Scenarios

### Scenario A: Complete Workflow
```
1. Login Customer
   [MODUL 3] → Login Customer

2. View All Shipments
   [MODUL 2: Public] → Get Shipment by Tracking Number

3. View My Shipments (only personal)
   [MODUL 2: Protected] → Get My Shipments

4. Create New Shipment
   [MODUL 2: Protected] → Create Shipment from Package
   
5. Update Status
   [MODUL 2: Protected] → Update Shipment Status

6. Track Package (public, no auth needed)
   [MODUL 2: Public] → Get Shipment by Tracking Number
```

### Scenario B: Package Management
```
1. Register Package
   [MODUL 1] → Register New Package

2. View Package
   [MODUL 1] → Get Package Detail

3. Check Dimension
   [MODUL 1] → Get Package Dimension
```

### Scenario C: Fleet Control
```
1. Create Fleet
   [MODUL 4] → Create Fleet

2. Update Status
   [MODUL 4] → Update Fleet Status

3. Relocate
   [MODUL 4] → Relocate Fleet
```

---

## 🔍 Data Format Examples

### Request: Create Shipment
```json
{
  "package_id": 22,
  "destination_hub_id": 7
}
```

### Response: Shipment Created
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "tracking_number": "TRK00000001AB4A",
    "customer_id": 44,
    "package_id": 22,
    "origin_hub_id": 3,
    "destination_hub_id": 7,
    "current_hub_id": 3,
    "fleet_id": null,
    "status": "pending",
    "customer": {
      "id": 44,
      "name": "Test User 43",
      "email": "test43@example.com"
    },
    "package": {
      "id": 22,
      "sender_name": "Klikpak",
      "receiver_name": "John Doe",
      "weight": 28.1,
      "origin": "Medan",
      "destination": "Surabaya"
    }
  }
}
```

---

## ⚙️ Environment Variables

Auto-set after login:
```
{{auth_token}}        - Bearer token (from login)
{{customer_id}}       - Customer ID
{{base_url}}          - http://localhost
{{tracking_number}}   - TRK00000001AB4A (example)
{{package_id}}        - 1 (example)
{{hub_id}}            - 1 (example)
{{fleet_id}}          - 1 (example)
```

---

## 🐛 Common Issues

### "401 Unauthorized"
**Fix**: Login first, token will auto-save

### "404 Not Found"
**Fix**: Resource doesn't exist, use valid IDs

### "422 Validation Error"
**Fix**: Check request body format, see error message

### "500 Server Error"
**Fix**: Check Docker logs or restart server

---

## 📚 Full Documentation

See [POSTMAN_DOCUMENTATION.md](POSTMAN_DOCUMENTATION.md) for complete API reference

---

## ✅ Ready to Test!

1. ✓ Collection imported
2. ✓ Environment selected
3. ✓ Login to get token
4. ✓ Run tests from folders

**Happy Testing! 🚀**
