<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah kolom role agar bisa menyimpan 'manager'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'operator', 'manager') NOT NULL DEFAULT 'operator'");
        
        echo "✅ Kolom role berhasil diupdate menjadi ENUM('admin', 'operator', 'manager')\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke versi sebelumnya (tanpa manager)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'operator') NOT NULL DEFAULT 'operator'");
        
        echo "✅ Kolom role dikembalikan ke ENUM('admin', 'operator')\n";
    }
};