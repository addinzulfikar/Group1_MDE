# 🚀 Setup untuk Developer Baru

Panduan lengkap untuk developer baru agar bisa clone project, setup environment, dan mulai develop tanpa conflict.

---

## 📋 Prerequisites

Pastikan sudah install:
- **Git** → [https://git-scm.com/download/win](https://git-scm.com/download/win)
- **Docker Desktop** → [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)
- **VS Code** (optional) → [https://code.visualstudio.com/](https://code.visualstudio.com/)

---

## 🔧 Setup Step-by-Step

### Step 1: Clone Repository
```bash
git clone https://github.com/[your-org]/Sistem-Pengiriman-Paket-Tim-1.git
cd Sistem-Pengiriman-Paket-Tim-1
```

### Step 2: Copy Environment File
```bash
# Copy .env dari template
cp .env.example .env

# Atau buat baru dengan config dasar Laravel
```

### Step 3: Build & Start Docker (Laravel Sail)
```bash
# Build image dan start container
docker-compose up

# Atau di background
docker-compose up -d
```

**Tunggu hingga selesai build (pertama kali bisa 5-10 menit)**

### Step 4: Generate Application Key (Optional)
```bash
# Hanya perlu jika APP_KEY di .env kosong/belum ada (ada di grup wa)
# Cek di .env: APP_KEY=base64:xxxxx
# Jika sudah ada, skip langkah ini!

# Jika perlu generate:
docker-compose exec laravel.test php artisan key:generate
```

### Step 5: Database Migration & Seeding
```bash
# Jalankan migration
docker-compose exec laravel.test php artisan migrate

# Jalankan seeding (termasuk semua module)
docker-compose exec laravel.test php artisan db:seed
```

### Step 6: Build Frontend Assets
```bash
# Install npm dependencies (sudah auto di container tapi bisa juga manual)
docker-compose exec laravel.test npm install

# Build assets
docker-compose exec laravel.test npm run build
```

### Step 7: Verify Setup
- API berjalan di: **http://localhost**
- Frontend Vite dev server di: **http://localhost:5173** (jika jalankan dev)

---

## 📦 Docker Commands yang Sering Digunakan

```bash
# Start container
docker-compose up -d

# Stop container
docker-compose down

# View logs
docker-compose logs -f

# Akses bash di container
docker-compose exec laravel.test bash

# Jalankan artisan command
docker-compose exec laravel.test php artisan [command]

# Jalankan npm command
docker-compose exec laravel.test npm [command]

# Rebuild image (jika ada perubahan Dockerfile)
docker-compose build
docker-compose up
```

---

## 🌳 Git Workflow untuk Mencegah Conflict

### 1️⃣ Sebelum Mulai Develop

```bash
# Update branch main/develop
git checkout develop
git pull origin develop

# Buat branch fitur baru dari develop
git checkout -b feature/nama-fitur

# Contoh:
git checkout -b feature/tambah-user-validation
git checkout -b fix/shipment-status-bug
```

### 2️⃣ Commit Frequently & Atomically

```bash
# Jangan cumulative commit besar - commit sering
git add file-yang-berubah.php
git commit -m "feat: add user validation rules"

# Jangan lakukan perubahan di banyak file sekaligus
# Pisahkan per fitur/bug
```

### 3️⃣ Mapping Fitur per Developer

**PENTING:** Koordinasi dengan tim agar tidak ada 2 dev yang ubah file yang sama!

| Developer | Module | File Focus |
|-----------|--------|-----------|
| Dev A | Module 1 | Warehouse, Package | 
| Dev B | Module 2 | Shipment, Tracking |
| Dev C | Module 4 | Fleet, Hub, FleetLog |

**Jika perlu ubah file shared:**
- Hubungi developer lain dulu
- Koordinasikan perubahan
- Atau buat separate branch kemudian merge dengan hati-hati

### 4️⃣ Sebelum Push

```bash
# Update dari develop terakhir
git fetch origin
git rebase origin/develop

# Jika ada conflict, resolve di VS Code
# Terus rebase
git rebase --continue

# Test lagi sebelum push
docker-compose exec laravel.test php artisan test

# Push ke remote
git push origin feature/nama-fitur
```

### 5️⃣ Create Pull Request (PR)

- Push ke GitHub
- Buat PR dengan **deskripsi jelas**
- Request review dari 1-2 developer
- Tunggu approval sebelum merge
- Merge ke `develop` (jangan langsung ke `main`)

---

## 🏗️ Struktur Project

```
📦 Sistem-Pengiriman-Paket-Tim-1
├── 📁 app/
│   ├── Models/          ← Eloquent Models
│   ├── Http/
│   │   ├── Controllers/ ← API Controllers
│   │   └── Requests/    ← Form Validation
│   ├── Repositories/    ← Business Logic
│   └── Providers/       ← Service Providers
├── 📁 database/
│   ├── migrations/      ← Database Schema
│   ├── factories/       ← Fake Data Generators
│   └── seeders/         ← Database Seeders (✅ DIPISAH per module!)
├── 📁 routes/
│   └── api.php          ← API Routes
├── 📁 tests/            ← Unit & Feature Tests
├── 📁 resources/
│   └── js/              ← Vue/React Components
├── compose.yaml         ← Docker Compose
└── phpunit.xml          ← Test Config
```

---

## 📝 Membuat Fitur Baru - Step by Step

### Contoh: Membuat fitur "Update Fleet Status"

#### 1. Buat Migration (jika perlu schema baru)
```bash
docker-compose exec laravel.test php artisan make:migration add_status_history_to_fleets_table --table=fleets
```

#### 2. Edit Migration File
```bash
# Edit file yang dibuat di database/migrations/
# Update schema sesuai kebutuhan
```

#### 3. Buat atau Update Model
```bash
docker-compose exec laravel.test php artisan make:model FleetHistory -m
# -m untuk auto-create migration
```

#### 4. Buat Controller
```bash
docker-compose exec laravel.test php artisan make:controller Api/FleetStatusController
```

#### 5. Buat Request Validation Class
```bash
docker-compose exec laravel.test php artisan make:request UpdateFleetStatusRequest
```

#### 6. Buat Repository (jika belum)
```bash
# Edit/buat di app/Repositories/Contracts/FleetRepositoryInterface.php
# Edit/buat di app/Repositories/Eloquent/FleetRepository.php
```

#### 7. Tambah Route
```php
// routes/api.php
Route::patch('/fleets/{id}/status', [FleetStatusController::class, 'update']);
```

#### 8. Jalankan Migration
```bash
docker-compose exec laravel.test php artisan migrate
```

#### 9. Test Fitur
```bash
# Jalankan unit/feature tests
docker-compose exec laravel.test php artisan test

# Atau gunakan Postman/Thunder Client untuk test API
```

#### 10. Commit & Push
```bash
git add .
git commit -m "feat: add fleet status update endpoint"
git push origin feature/fleet-status-update
```

---

## ⚠️ Yang Harus DIHINDARI

### ❌ JANGAN:

1. **Langsung edit file shared tanpa koordinasi**
   - ✅ DO: Hubungi dev lain, koordinasikan
   - ❌ DON'T: Edit `DatabaseSeeder.php` bersamaan

2. **Commit ke `main` branch**
   - ✅ DO: Develop di branch fitur, PR ke `develop`
   - ❌ DON'T: Push langsung ke `main`

3. **Push force (`git push -f`)**
   - ✅ DO: Resolve conflict dengan rebase/merge
   - ❌ DON'T: Force push (bisa rusak history tim)

4. **Commit `vendor/` dan `node_modules/`**
   - ✅ DO: Sudah di `.gitignore`
   - ❌ DON'T: Force add ini ke git

5. **Develop tanpa test**
   - ✅ DO: Jalankan test sebelum PR
   - ❌ DON'T: PR kode yang belum ditest

---

## 🐛 Troubleshooting

### Docker tidak bisa start
```bash
# Cek apakah Docker Desktop sudah running
# Restart Docker Desktop

# Jika masih error, cek logs
docker-compose logs

# Clean up dan rebuild
docker-compose down
docker-compose build --no-cache
docker-compose up
```

### Database error setelah setup
```bash
# Clear database dan re-seed
docker-compose exec laravel.test php artisan migrate:fresh
docker-compose exec laravel.test php artisan db:seed
```

### Port 80 sudah terpakai
```bash
# Edit .env
APP_PORT=8000

# Restart container
docker-compose down
docker-compose up -d
# Akses di http://localhost:8000
```

### Git conflict saat rebase
```bash
# VS Code akan highlight conflict
# Edit file yang conflict, pilih mana yang dipakai

# Setelah resolve semua
git add .
git rebase --continue
```

---

## 📞 Bantuan & Komunikasi

Jika ada pertanyaan atau butuh bantuan:
- Hubungi team lead / senior developer
- Cek dokumentasi di project
- Tanyakan di channel #development di Slack/Discord

---

## ✅ Checklist Setup Berhasil

- [ ] Docker container berjalan (`docker-compose ps`)
- [ ] Database termigrasi (`php artisan migrate`)
- [ ] Data seeding berhasil (`php artisan db:seed`)
- [ ] API bisa diakses (http://localhost)
- [ ] Tidak ada error di logs (`docker-compose logs`)
- [ ] Git branch feature sudah dibuat dan siap develop

---

**🎉 Selamat! Setup selesai, silakan mulai develop!**

Jika ada yang tidak jelas, jangan ragu bertanya ke tim. Komunikasi = mencegah conflict! 💪
