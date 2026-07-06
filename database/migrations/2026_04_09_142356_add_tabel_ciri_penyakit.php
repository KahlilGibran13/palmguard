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
        Schema::create('ciri_penyakit', function (Blueprint $table) {
            $table->id();
            
            /* FK */
            $table->foreignId('id_penyakit')
              ->nullable() 
              ->constrained('penyakit')
              ->onDelete('set null');
              
            $table->text('ciri')->nullable();

            // $table->string('warna'); 
            // $table->string('bercak'); 
            // $table->string('ujung_daun_tombak'); 
            // $table->string('daun_menguning'); 
            // $table->string('daun_mengering'); 
            // $table->string('batang_busuk'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciri_penyakit');
        // Schema::table('ciri_penyakit', function (Blueprint $table) {
        //     $table->dropForeign(['id_penyakit']); 
        //     $table->dropColumn('id_penyakit');
            
        //     $table->dropColumn([
        //         'warna', 'bercak', 'ujung_daun_tombak', 'daun_menguning', 'daun_mengering', 'batang_busuk'
        //     ]);
        // });
    }
};