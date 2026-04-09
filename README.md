# Group1 MDE - Laravel Application

Aplikasi web berbasis Laravel 12 untuk proyek Model-Driven Engineering (MDE).

## 📋 Persyaratan Sistem

Sebelum memulai, pastikan teman Anda memiliki:

- **PHP** 8.2 atau lebih tinggi ([Download PHP](https://www.php.net/downloads))
- **Composer** ([Download Composer](https://getcomposer.org/download/))
- **Node.js** 18+ dan **NPM** ([Download Node.js](https://nodejs.org/))
- **SQLite** (biasanya sudah built-in di PHP)

### Verifikasi Instalasi

```bash
php --version
composer --version
node --version
npm --version
```

## 🚀 Instalasi & Setup (Langkah Demi Langkah)

### 1. Clone Repository

```bash
git clone https://github.com/your-username/Group1_MDE.git
cd Group1_MDE
```

### 2. Install Dependency PHP

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
# Copy file .env dari template
cp .env.example .env

# Atau di Windows:
copy .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

**Verifikasi**: Buka file `.env` dan pastikan `APP_KEY` sudah terisi dengan nilai `base64:...`

### 5. Setup Database

Laravel project ini menggunakan SQLite. Jalankan perintah:

```bash
php artisan migrate
```

Perintah ini akan:
- Membuat file `database/database.sqlite`
- Membuat semua tabel database (users, cache, jobs, sessions, dll)

### 6. Install Frontend Dependencies

```bash
npm install
```

### 7. Build Frontend Assets (Optional)

Jika ingin menggunakan asset yang sudah di-build:

```bash
npm run build
```

Atau untuk development dengan hot reload:

```bash
npm run dev
```
*(Jalankan di terminal terpisah)*

## 🎯 Menjalankan Aplikasi

### Start Development Server

```bash
php artisan serve
```

Output akan menunjukkan:
```
INFO  Server running on [http://127.0.0.1:8000].
```

Buka browser ke http://127.0.0.1:8000

### Menjalankan Frontend Development (Opsional)

Di terminal lain, jalankan:

```bash
npm run dev
```

Ini akan menjalankan Vite untuk hot reload asset CSS/JS.

## 📁 Struktur Proyek

```
Group1_MDE/
├── app/              # Aplikasi PHP
│   ├── Http/
│   ├── Models/
│   └── Providers/
├── config/           # File konfigurasi
├── database/         # Database & migrations
│   ├── database.sqlite    # SQLite database (auto-generated)
│   ├── migrations/
│   └── seeders/
├── routes/           # Route definition
├── resources/        # Frontend assets
│   ├── css/
│   ├── js/
│   └── views/        # Blade templates
├── public/           # Public files & entry point
├── storage/          # Cache, sessions, logs
├── tests/            # Unit & feature tests
├── .env              # Environment variables (auto-generated)
└── composer.json     # PHP dependencies
```

## 🔧 Troubleshooting

### Error: "No application encryption key has been specified"

**Solusi:**
```bash
php artisan key:generate
```

Pastikan `APP_KEY` di `.env` terisi.

### Error: "Database file at path [...] does not exist"

**Solusi:**
```bash
php artisan migrate
```

### Error: "Connection refused" atau port 8000 sudah ter-pakai

Gunakan port berbeda:
```bash
php artisan serve --port=8001
```

### Composer install lambat atau error

Jika menggunakan Windows dan terjadi error network:
- Gunakan VPN atau cek koneksi internet
- Atau clear cache: `composer clear-cache` dan coba lagi

### Node modules issue

Jika ada error dengan npm, coba:
```bash
rm package-lock.json
npm install
```

## 📝 Development Commands

```bash
# Generate migration baru
php artisan make:migration create_table_name

# Generate model baru
php artisan make:model ModelName

# Generate controller baru
php artisan make:controller ControllerName

# Jalankan tests
php artisan test

# Lint PHP code
./vendor/bin/pint

# Clear all cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## ✅ Checklist Sebelum Mulai Development

- [ ] PHP 8.2+ terinstall
- [ ] Composer terinstall
- [ ] Node.js 18+ terinstall
- [ ] Repository sudah di-clone
- [ ] `composer install` sudah dijalankan
- [ ] `.env` sudah dibuat
- [ ] `php artisan key:generate` sudah dijalankan
- [ ] `php artisan migrate` sudah dijalankan
- [ ] `npm install` sudah dijalankan
- [ ] Server bisa dijalankan dengan `php artisan serve` tanpa error

## 📚 Dokumentasi Menggunakan

- [Laravel Docs](https://laravel.com/docs/12.x)
- [Vite Docs](https://vitejs.dev/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)

## 🤝 Kontribusi

1. Buat branch fitur: `git checkout -b feature/nama-fitur`
2. Commit changes: `git commit -m 'Add nama-fitur'`
3. Push ke branch: `git push origin feature/nama-fitur`
4. Buat Pull Request

## 📄 License

MIT

---

**Pertanyaan?** Hubungi tim development atau buka issue di repository ini.
