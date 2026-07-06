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
        Schema::create('kondisi_kebun', function (Blueprint $table) {
            $table->id();

            /* FK */
            $table->foreignId('id_pohon')
              ->nullable() 
              ->constrained('pohon')
              ->onDelete('set null');

            $table->string('suhu'); 
            $table->string('kelembapan'); 
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisi_kebun');
        // Schema::table('kondisi_kebun', function (Blueprint $table) {
        //     $table->dropForeign(['id_pohon']); 
        //     $table->dropColumn('id_pohon');

        //     $table->dropColumn([
        //         'suhu', 'kelembapan'
        //     ]);
        // });
    }
};
