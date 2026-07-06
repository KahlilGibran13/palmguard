<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penyakit', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penyakit'); 
            // $table->text('deskripsi'); 
            $table->text('warna_badge'); 
            $table->string('status'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyakit');
        // Schema::table('penyakit', function (Blueprint $table) {
        //     $table->dropColumn([
        //         'nama_penyakit', 'deskripsi'
        //     ]);
        // });
    }
};
