# 📋 TUTORIAL KALO IMAGE BELUM ADA DAN BARU PERTAMA KALI PULL
## (Download + Build dari awal)

# Step 1: Clone
git clone ...
cd Sistem-Pengiriman-Paket-Tim-1

# Step 2: .env
cp .env.example .env (pake yang di group wa)

# Step 3: Docker Build Images
docker-compose up -d --build

# Step 4: Composer Install
docker-compose exec laravel.test composer install

# Step 5: Database Migration & Seed
docker-compose exec laravel.test php artisan migrate:fresh --seed

# Step 6: Assets
docker-compose exec laravel.test npm install
docker-compose exec laravel.test npm run build

# Step 7: Ready! ✅

---

# 📋 TUTORIAL KALO IMAGE UDAH ADA DAN DOCKER SUDAH KOMPLIT
## (Quick start - images sudah ter-cache)
# Step 1: Clone
git clone ...
cd Sistem-Pengiriman-Paket-Tim-1

# Step 2: .env
cp .env.example .env (pake yang di group wa)

# Step 3: Docker (Quick start)
docker-compose up -d

# Step 4: Database
## Option A: Fresh start (reset database)
docker-compose exec laravel.test php artisan migrate:fresh --seed

## Option B: Normal migrate (keep existing data)
docker-compose exec laravel.test php artisan migrate
docker-compose exec laravel.test php artisan db:seed

# Step 5: Assets
docker-compose exec laravel.test npm install
docker-compose exec laravel.test npm run build

# Step 6: Ready! ✅

---

# 🔧 TROUBLESHOOTING & DEBUGGING

## Images seem stuck or error saat build?
```bash
# Full cleanup & rebuild dari awal
docker-compose down -v
docker system prune -a
docker-compose up -d --build
```

## Database error (duplicate entry, column not found)?
```bash
# Fresh database dengan clean migrations
docker-compose exec laravel.test php artisan migrate:fresh --seed
```

## Rebuild tanpa download ulang (faster)?
```bash
# Rebuild + no cache (recompile)
docker-compose up -d --build --no-cache
```

## Check docker images & clean unused?
```bash
# Lihat semua images yang tersimpan
docker images

# Hapus images yang unused (nggak perlu)
docker image prune -a
```

**Note:** Docker adalah lokal per developer. Cleanup di mesin kamu tidak mempengaruhi team lain!