# Step 1: Clone
git clone ...
cd Sistem-Pengiriman-Paket-Tim-1

# Step 2: .env
cp .env.example .env (pake yang di group wa)

# Step 3: Docker
docker-compose up -d

# Step 4 & 5: Database
docker-compose exec laravel.test composer install
docker-compose exec laravel.test php artisan migrate
docker-compose exec laravel.test php artisan db:seed

# Step 6: Assets
docker-compose exec laravel.test npm install
docker-compose exec laravel.test npm run build

# Step 7: Ready! ✅