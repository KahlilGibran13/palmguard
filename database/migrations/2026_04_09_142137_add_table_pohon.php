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
        Schema::create('pohon', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pohon'); 
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pohon');
        // Schema::table('pohon', function (Blueprint $table) {
        //     $table->dropColumn([
        //         'nama_pohon', 'latitude', 'longitude', 'status'
        //     ]);
        // });
    }
};
