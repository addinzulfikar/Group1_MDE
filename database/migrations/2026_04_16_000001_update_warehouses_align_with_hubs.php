<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menyesuaikan tabel warehouses agar selaras dengan struktur tabel hubs (Modul 4):
     * 1. Hapus kolom warehouse_code
     * 2. Ubah enum status dari (active, inactive) menjadi (available, full, overload)
     * 3. current_load dihitung ulang berdasarkan jumlah paket (package count)
     */
    public function up(): void
    {
        // Hapus warehouse_code jika masih ada (mungkin sudah terhapus oleh run sebelumnya)
        if (Schema::hasColumn('warehouses', 'warehouse_code')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropColumn('warehouse_code');
            });
        }

        // Langkah 1: Perluas enum agar mencakup SEMUA nilai lama dan baru
        // sehingga data existing tidak ditolak saat konversi
        DB::statement("ALTER TABLE warehouses MODIFY COLUMN status ENUM('active','inactive','available','full','overload') DEFAULT 'available'");

        // Langkah 2: Konversi nilai lama ke nilai baru yang sesuai
        DB::statement("UPDATE warehouses SET status = 'available' WHERE status IN ('active', 'inactive')");

        // Langkah 3: Persempit enum ke nilai final (available, full, overload)
        DB::statement("ALTER TABLE warehouses MODIFY COLUMN status ENUM('available','full','overload') DEFAULT 'available'");

        // Langkah 4: Recalculate current_load dari jumlah paket per warehouse
        if (Schema::hasTable('packages')) {
            $warehouses = DB::table('warehouses')->get();
            foreach ($warehouses as $warehouse) {
                $packageCount = DB::table('packages')
                    ->where('warehouse_id', $warehouse->id)
                    ->count();

                $percentage = $warehouse->capacity > 0
                    ? ($packageCount / $warehouse->capacity) * 100
                    : 0;

                $status = 'available';
                if ($percentage >= 100) {
                    $status = 'overload';
                } elseif ($percentage >= 90) {
                    $status = 'full';
                }

                DB::table('warehouses')
                    ->where('id', $warehouse->id)
                    ->update([
                        'current_load' => $packageCount,
                        'status'       => $status,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum status ke active/inactive
        DB::statement("ALTER TABLE warehouses MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");

        Schema::table('warehouses', function (Blueprint $table) {
            // Re-tambahkan warehouse_code
            $table->string('warehouse_code', 50)->unique()->after('id')->nullable();
        });
    }
};
