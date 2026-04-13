# 🚀 Group1 MDE - Sistem Pengiriman Paket (Logistik)

Aplikasi web berbasis **Laravel 12** untuk proyek **Model-Driven Engineering (MDE)**.
Sistem ini mensimulasikan operasional logistik end-to-end dengan modul warehouse, tracking, authentication, dan fleet management.

> ⚠️ **PENTING**: Proyek ini **WAJIB** berjalan di dalam **Docker Container**.
> Jangan jalankan `php artisan serve` langsung!

---

## 📋 Persyaratan Sistem

Sebelum memulai, pastikan Anda memiliki:

1. **Docker Desktop** ([Download](https://www.docker.com/products/docker-desktop))
   - Windows / Mac / Linux
   - Versi terbaru recommended

2. **Git** ([Download](https://git-scm.com/))

3. **Text Editor / IDE**
   - VS Code

### Verifikasi Instalasi Docker

```bash
docker --version
docker compose --version
docker ps
```

Output contoh:
```
Docker version 29.3.1, build c2be9cc
Docker Compose version v5.1.1
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
(kosong jika belum launch container)
```

---

## 🎯 Quick Start (5 Menit)

### Untuk Linux / Mac (Bash)

```bash
# 1. Clone repository
git clone https://github.com/your-username/Group1_MDE.git
cd Group1_MDE

# 2. Copy environment file
cp .env.example .env

# 3. Install dependencies
composer install

# 4. Start Docker containers
./vendor/bin/sail up -d

# 5. Setup database
./vendor/bin/sail artisan migrate

# 6. Verifikasi
./vendor/bin/sail tinker
# Di dalam tinker:
DB::table('users')->count()
exit()

# Aplikasi ready!
# Akses: http://localhost
```

### Untuk Windows PowerShell ⚠️ PENTING!

**JANGAN gunakan `./vendor/bin/sail` di PowerShell!** Gunakan `docker compose` langsung:

```powershell
# 1. Clone repository
git clone https://github.com/your-username/Group1_MDE.git
cd Group1_MDE

# 2. Copy environment file
copy .env.example .env

# 3. Install dependencies (perlu composer installed)
composer install

# 4. Start Docker containers (GUNA INI, BUKAN ./vendor/bin/sail)
docker compose up -d

# 5. Setup database
docker compose exec laravel.test php artisan migrate

# 6. Akses aplikasi
# Buka: http://localhost
```

**Alternative untuk Windows (jika ada error):**
```powershell
# Build image terlebih dahulu
docker compose build

# Kemudian start
docker compose up -d

# Check status
docker ps
```

---

## 🐳 Instalasi & Setup Lengkap (Step by Step)

### Step 1: Clone Repository

```bash
git clone https://github.com/your-username/Group1_MDE.git
cd Group1_MDE
```

### Step 2: Copy Environment File

```bash
# Linux/Mac
cp .env.example .env

# Windows PowerShell
copy .env.example .env
```

### Step 3: Install Composer Dependencies

```bash
composer install
```

### Step 4: Start Docker Containers

**Linux/Mac (Bash):**
```bash
./vendor/bin/sail up -d
```

**Windows PowerShell:**
```powershell
docker compose build   # Build image dulu (pertama kali saja)
docker compose up -d   # Start containers
```

Verifikasi container running:
```bash
# Linux/Mac
./vendor/bin/sail ps

# Windows PowerShell
docker ps
```

Expected output:
```
CONTAINER ID   IMAGE          STATUS
2ef91195c7b9   sail-8.5/app   Up 2 minutes
a9ecc49f5ff5   mysql:8.4      Up 2 minutes (healthy)
```

### Step 5: Database Setup

**Linux/Mac (Bash):**
```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Optional: seed database
./vendor/bin/sail artisan db:seed
```

**Windows PowerShell:**
```powershell
# Run migrations
docker compose exec laravel.test php artisan migrate

# Optional: seed database
docker compose exec laravel.test php artisan db:seed
```

### Step 6: Akses Aplikasi

Buka browser:
```
http://localhost
```

Harus menampilkan Laravel welcome page.

---

## 💻 Development Workflow

### Menjalankan Command Laravel

**Linux/Mac (Bash) - Gunakan `./vendor/bin/sail`:**
```bash
# ✅ BENAR
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan make:model Product
./vendor/bin/sail artisan make:controller ProductController
./vendor/bin/sail artisan make:migration create_products_table

# ❌ SALAH - jangan langsung di OS
php artisan tinker
php artisan migrate
php artisan serve
```

**Windows PowerShell - Gunakan `docker compose exec`:**
```powershell
# ✅ BENAR
docker compose exec laravel.test php artisan tinker
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan make:model Product
docker compose exec laravel.test php artisan make:controller ProductController
docker compose exec laravel.test php artisan make:migration create_products_table

# ❌ SALAH - jangan langsung di OS
php artisan tinker
php artisan migrate
php artisan serve
```

### Akses Database dengan DBeaver

1. Buka DBeaver
2. **Database** → **New Database Connection**
3. Pilih **MySQL**
4. Isi konfigurasi:
   - **Server Host**: `localhost` atau `127.0.0.1`
   - **Port**: `3306`
   - **Database**: `group1`
   - **Username**: `sail`
   - **Password**: `password`
5. **Test Connection** → **Finish**

### Menjalankan Tests

**Linux/Mac:**
```bash
# Run all tests
./vendor/bin/sail artisan test

# Run specific test file
./vendor/bin/sail artisan test --filter=WarehouseTest

# Run dengan coverage
./vendor/bin/sail artisan test --coverage
```

**Windows PowerShell:**
```powershell
# Run all tests
docker compose exec laravel.test php artisan test

# Run specific test file
docker compose exec laravel.test php artisan test --filter=WarehouseTest

# Run dengan coverage
docker compose exec laravel.test php artisan test --coverage
```

### Menjalankan PHP Interactive Shell (Tinker)

**Linux/Mac:**
```bash
./vendor/bin/sail tinker

# Di dalam shell:
> $user = User::first()
> $user->email
> exit()
```

**Windows PowerShell:**
```powershell
docker compose exec laravel.test php artisan tinker

# Di dalam shell:
> $user = User::first()
> $user->email
> exit()
```

### Install Package Baru

**Linux/Mac:**
```bash
# Composer (PHP package)
./vendor/bin/sail composer require laravel/sanctum

# NPM (JavaScript package)
./vendor/bin/sail npm install axios

# Generate docs
./vendor/bin/sail composer docs
```

**Windows PowerShell:**
```powershell
# Composer (PHP package)
docker compose exec laravel.test composer require laravel/sanctum

# NPM (JavaScript package)
docker compose exec laravel.test npm install axios

# Generate docs
docker compose exec laravel.test composer docs
```

---

## 📁 Struktur Proyek

```
Group1_MDE/
├── app/                          # Aplikasi PHP
│   ├── Http/
│   │   ├── Controllers/          # API Controllers
│   │   │   ├── WarehouseController.php    (Dev 1)
│   │   │   ├── TrackingController.php     (Dev 2)
│   │   │   ├── AuthController.php         (Dev 3)
│   │   │   └── FleetController.php        (Dev 4)
│   │   └── ...
│   ├── Models/                   # Database Models
│   │   ├── Package.php           (Dev 1)
│   │   ├── Tracking.php          (Dev 2)
│   │   ├── User.php              (Dev 3)
│   │   ├── Fleet.php             (Dev 4)
│   │   └── Hub.php               (Dev 4)
│   ├── Repositories/             # Repository Pattern
│   │   ├── PackageRepository.php (Dev 1)
│   │   ├── TrackingRepository.php (Dev 2)
│   │   ├── UserRepository.php    (Dev 3)
│   │   ├── FleetRepository.php   (Dev 4)
│   │   └── HubRepository.php     (Dev 4)
│
├── database/
│   ├── migrations/               # Database Schema
│   │   ├── *_create_packages_table.php
│   │   ├── *_create_tracking_table.php
│   │   └── ...
│   ├── seeders/                  # Data Dummy (25K+ rows)
│   │   ├── PackageSeeder.php
│   │   ├── TrackingSeeder.php
│   │   └── ...
│
├── routes/
│   ├── api/
│   │   ├── warehouse.php         # Dev 1 routes
│   │   ├── tracking.php          # Dev 2 routes
│   │   ├── auth.php              # Dev 3 routes
│   │   └── fleet.php             # Dev 4 routes
│
├── tests/
│   ├── Feature/
│   │   ├── WarehouseTest.php
│   │   ├── TrackingTest.php
│   │   └── ...
│
├── docker-compose.yaml           # ⭐ Docker configuration
├── Dockerfile                    # ⭐ Container definition
├── .env.example                  # ⭐ Environment template
├── .gitignore                    # ⭐ Git ignore rules
└── README.md                     # ⭐ You are here!
```

---

## 🐳 Docker Commands Penting

### Container Management

**Untuk Linux/Mac (Bash):**
```bash
# Start containers (background)
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Restart containers
./vendor/bin/sail restart

# View container status
./vendor/bin/sail ps

# View logs
./vendor/bin/sail logs
./vendor/bin/sail logs mysql      # Hanya MySQL logs
./vendor/bin/sail logs -f         # Follow logs (real-time)
```

**Untuk Windows PowerShell:**
```powershell
# Start containers (background)
docker compose up -d

# Stop containers
docker compose down

# Restart containers
docker compose restart

# View container status
docker ps

# View logs
docker compose logs laravel.test
docker compose logs mysql          # Hanya MySQL logs
docker compose logs -f             # Follow logs (real-time)
```

### Database Management

**Untuk Linux/Mac (Bash):**
```bash
# Connect ke MySQL
./vendor/bin/sail mysql -u sail -ppassword group1

# Backup database
./vendor/bin/sail exec -T mysql mysqldump -u root -ppassword \
  --no-tablespaces group1 > backup.sql

# Restore database
./vendor/bin/sail exec -T mysql mysql -u root -ppassword group1 < backup.sql
```

**Untuk Windows PowerShell:**
```powershell
# Connect ke MySQL
docker compose exec mysql mysql -u sail -ppassword group1

# Backup database
docker compose exec -T mysql mysqldump -u root -ppassword --no-tablespaces group1 > backup.sql

# Restore database
docker compose exec -T mysql mysql -u root -ppassword group1 < backup.sql
```

### Artisan Commands (dalam Docker)

**Untuk Linux/Mac (Bash):**
```bash
# ✅ Benar - semua pakai ./vendor/bin/sail
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migration:fresh
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan test
./vendor/bin/sail composer require package-name
./vendor/bin/sail npm install package-name
```

**Untuk Windows PowerShell:**
```powershell
# ✅ Benar - gunakan docker compose exec
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan migration:fresh
docker compose exec laravel.test php artisan db:seed
docker compose exec laravel.test php artisan tinker
docker compose exec laravel.test php artisan test
docker compose exec laravel.test composer require package-name
docker compose exec laravel.test npm install package-name
```

---

## 👥 Modul & Tanggung Jawab Tim

| No | Modul | Deskripsi | Developer |
|----|----|-----------|-----------|
| 1 | **Warehouse & Sorting** | API Manajemen Gudang, pendaftaran paket baru, dimensi paket | Dev 1 |
| 2 | **Tracking System (Core)** | API Update Lokasi, riwayat status kronologis, pencarian resi | Dev 2 |
| 3 | **Auth & Pricing** | Autentikasi Pelanggan, kalkulator ongkir dinamis, profil pengiriman | Dev 3 |
| 4 | **Fleet & Hub** | API Manajemen Armada, monitoring kapasitas gudang, laporan durasi transit | Dev 4 |

### Data Requirements

- ✅ Tracking log: **minimal 25.000 rows** (seeder)
- ✅ Fleet log: **minimal 5.000 rows** (seeder)

---

## 📋 Workflow Tim Development

### Daily Workflow

**Linux/Mac (Bash):**
```bash
# Pagi: Pull latest changes
git pull origin develop

# Sinkronisasi Docker
./vendor/bin/sail down && ./vendor/bin/sail up -d
./vendor/bin/sail migrate

# Development
# Edit: app/Models, app/Repositories, app/Http/Controllers
# Run tests: ./vendor/bin/sail artisan test

# Sore: Commit & Push
git add .
git commit -m "Feat: implement warehouse API"
git push origin feature/warehouse-api
```

**Windows PowerShell:**
```powershell
# Pagi: Pull latest changes
git pull origin develop

# Sinkronisasi Docker
docker compose down
docker compose up -d
docker compose exec laravel.test php artisan migrate

# Development
# Edit: app/Models, app/Repositories, app/Http/Controllers
# Run tests: docker compose exec laravel.test php artisan test

# Sore: Commit & Push
git add .
git commit -m "Feat: implement warehouse API"
git push origin feature/warehouse-api
```

### Branch Strategy

```
main              (Production Ready - hanya dosen)
  ↑
develop           (Integration branch)
  ↑
feature/*         (Individual development)
├─ feature/warehouse
├─ feature/tracking  
├─ feature/auth
└─ feature/fleet
```

**Rules:**
1. Selalu branch dari `develop`
2. Push ke feature branch milik Anda
3. Merge via Pull Request (PR review)
4. After testing → merge ke `develop`
5. Dosen merge `develop` ke `main`

---

## 🪟 Windows PowerShell - KHUSUS! ⚠️

### Masalah: `./vendor/bin/sail` tidak dikenal di PowerShell

**JANGAN gunakan Bash syntax di PowerShell!** 

❌ **SALAH:**
```powershell
./vendor/bin/sail up -d              # Error!
./vendor/bin/sail artisan migrate    # Error!
```

✅ **BENAR - Gunakan `docker compose` atau `php vendor\bin\sail`:**

```powershell
# Option 1: Docker Compose langsung (Recommended)
docker compose up -d
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan tinker
docker compose ps
docker compose logs laravel.test

# Option 2: Menggunakan PHP (jika vendor sudah ada)
php vendor\bin\sail up -d
php vendor\bin\sail artisan migrate
```

### Command Reference untuk Windows PowerShell

| Task | Command |
|------|---------|
| Start containers | `docker compose up -d` |
| Stop containers | `docker compose down` |
| View logs | `docker compose logs laravel.test -f` |
| Run migration | `docker compose exec laravel.test php artisan migrate` |
| Access Tinker | `docker compose exec laravel.test php artisan tinker` |
| Run tests | `docker compose exec laravel.test php artisan test` |
| Database connection | `docker compose exec mysql mysql -u sail -ppassword group1` |

### PowerShell Common Issues & Solutions

#### Issue 1: Vendor directory tidak ada

```powershell
# Pastikan install composer dependencies terlebih dahulu
composer install

# Baru jalankan Docker
docker compose up -d
```

#### Issue 2: Cache error (RedisException)

Edit `.env` dan ubah:
```
CACHE_STORE=file       # Dari redis ke file
```

Kemudian restart:
```powershell
docker compose restart
```

#### Issue 3: Localhost loading terus (hangs)

```powershell
# 1. Jalankan migration
docker compose exec laravel.test php artisan migrate

# 2. Clear cache
docker compose exec laravel.test php artisan cache:clear
docker compose exec laravel.test php artisan config:clear

# 3. Restart containers
docker compose restart

# 4. Akses http://localhost di browser (tunggu 5-10 detik)
```

#### Issue 4: Port 80 sudah terpakai

Edit `.env`:
```
APP_PORT=8000
```

Kemudian restart dan akses `http://localhost:8000`:
```powershell
docker compose restart
```

---

## 🔧 Troubleshooting Docker

### Problem: Container tidak start

**Linux/Mac:**
```bash
# Cek error
./vendor/bin/sail logs

# Try rebuild
./vendor/bin/sail build
./vendor/bin/sail up -d

# Nuclear option (hati-hati! hapus semua data)
docker compose down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

**Windows PowerShell:**
```powershell
# Cek error
docker compose logs laravel.test

# Try rebuild
docker compose build
docker compose up -d

# Nuclear option (hati-hati! hapus semua data)
docker compose down -v
docker compose build
docker compose up -d
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan db:seed
```

### Problem: Port sudah digunakan

```bash
# Edit .env
# Ubah port:
APP_PORT=8080    # dari 80 menjadi 8080
FORWARD_DB_PORT=3307

# Restart
./vendor/bin/sail restart    # Linux/Mac
# atau
docker compose restart       # PowerShell
```

### Problem: Database connection error

**Linux/Mac:**
```bash
# Verifikasi database
./vendor/bin/sail exec -T mysql mysql -u root -ppassword -e "SHOW DATABASES;"

# Grant permissions
./vendor/bin/sail exec -T mysql mysql -u root -ppassword \
  -e "GRANT ALL PRIVILEGES ON group1.* TO 'sail'@'%';"

./vendor/bin/sail exec -T mysql mysql -u root -ppassword \
  -e "FLUSH PRIVILEGES;"

# Run migration
./vendor/bin/sail artisan migrate
```

**Windows PowerShell:**
```powershell
# Verifikasi database
docker compose exec -T mysql mysql -u root -ppassword -e "SHOW DATABASES;"

# Grant permissions
docker compose exec -T mysql mysql -u root -ppassword -e "GRANT ALL PRIVILEGES ON group1.* TO 'sail'@'%';"

docker compose exec -T mysql mysql -u root -ppassword -e "FLUSH PRIVILEGES;"

# Run migration
docker compose exec laravel.test php artisan migrate
```

### Problem: "Cannot GET /"

Container tidak ready. Tunggu 10-15 detik, cek:
```bash
./vendor/bin/sail ps
# STATUS harus "healthy" atau "Up"

./vendor/bin/sail logs | tail -50
```

---

## ✅ Checklist Setup Awal (Tim Lead)

- [ ] Repository dibuat
- [ ] Docker files ada: `docker-compose.yaml`, `Dockerfile`
- [ ] `.env.example` sudah setup dengan benar
- [ ] `.gitignore` include: `.env`, `vendor`, `node_modules`, `storage/logs`
- [ ] Invite semua team member ke repo
- [ ] Test: `./vendor/bin/sail up -d` berhasil
- [ ] Test: `./vendor/bin/sail artisan migrate` berhasil
- [ ] Share README ke team

---

## ✅ Checklist Setup (Setiap Developer Baru)

- [ ] Docker Desktop terinstall
- [ ] Repo di-clone
- [ ] `cp .env.example .env`
- [ ] `./vendor/bin/sail up -d` berhasil
- [ ] `./vendor/bin/sail artisan migrate` berhasil
- [ ] Akses `http://localhost` menampilkan Laravel page
- [ ] `./vendor/bin/sail tinker` bisa dijalankan
- [ ] Ready to develop!

---

## 🚀 Pre-Submission Checklist (Sebelum Transfer ke Dosen)

```bash
# 1. Cleanup git history
git log --oneline | head -20
# Harus clean, tidak ada "oops", "fix", "try lagi"

# 2. Verifikasi .gitignore
cat .gitignore | grep -E "^\.env$|^vendor/|^node_modules/"
# Harus ada

# 3. Test fresh clone (simulasi dosen)
cd /tmp
git clone <repo-url>
cd Group1_MDE
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker
DB::table('users')->count()  # Harus bisa
exit()

# 4. Jika OK, siap transfer ke dosen!
```

---

## 📚 Resources

- 📖 [Laravel Docs](https://laravel.com/docs/12.x)
- 🐳 [Docker Docs](https://docs.docker.com/)
- 🌐 [Laravel Sail Docs](https://laravel.com/docs/12.x/sail)
- 📝 [Git Workflow](https://git-scm.com/book/en/v2/Git-Branching-Branching-Workflows)

---

## 🤝 Team Communication

- **Daily Standup**: Report progress di setiap modul
- **Weekly Integration**: Test semua modul bersamaan
- **Issue Tracking**: Gunakan GitHub Issues untuk bugs/features
- **Code Review**: Semua PR harus di-review minimal 1 orang

---

## 📄 License

MIT

---

**Need Help?**
- Read this README carefully first
- Check troubleshooting section
- Ask team members
- Contact instructor if stuck

**Happy Coding! 🚀**
