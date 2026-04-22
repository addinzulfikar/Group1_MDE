<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan hub_id ke warehouses agar Modul 1 (Warehouse) terhubung
     * dengan Modul 4 (Hub/Fleet). Setiap gudang bisa berasosiasi dengan
     * satu hub logistik sehingga kapasitas gudang dan hub tersinkronisasi.
     */
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->foreignId('hub_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('hubs')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign(['hub_id']);
            $table->dropColumn('hub_id');
        });
    }
};
