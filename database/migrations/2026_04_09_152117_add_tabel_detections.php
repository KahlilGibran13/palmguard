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
        Schema::create('detection', function (Blueprint $table) {
          $table->id();
          
            /* FK */
            $table->foreignId('id_pohon')
              ->nullable() 
              ->constrained('pohon')
              ->onDelete('set null');
              
            /* FK */
            $table->foreignId('id_penyakit')
              ->nullable()
              ->constrained('penyakit')
              ->onDelete('set null');

            $table->string('filename');           // nama file asli
            $table->string('image_path');         // path file di storage
            $table->string('disease_name');       // nama penyakit hasil AI
            $table->string('status');             // sehat / sakit / waspada
            $table->string('description')->nullable(); // keterangan singkat
            $table->decimal('confidence', 5, 2);
            $table->text('bounding_box')->nullable();
            $table->text('yolo_raw')->nullable();
            $table->string('file_size')->nullable();
            $table->string('source')->default('upload');
            
            // ─── Lokasi Foto Daun ───
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // $table->string('foto_lokasi_nama')->nullable()->after('foto_longitude'); // Nama lokasi foto

            // ─── Info Kebun ───
            // $table->string('kebun_suhu')->nullable()->after('kebun_longitude');       // contoh: "28°C"
            // $table->string('kebun_kelembapan')->nullable()->after('kebun_suhu');      // contoh: "75%"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection');
        // Schema::table('detection', function (Blueprint $table) {
        //     $table->dropForeign(['id_pohon']); 
        //     $table->dropColumn('id_pohon');

        //     $table->dropForeign(['id_penyakit']); 
        //     $table->dropColumn('id_penyakit');

        //     $table->dropColumn([
        //         'filename', 'image_path', 'disease_name', 'status', 'description', 'confidence', 
        //         'bounding_box', 'yolo_raw', 'file_size', 'source', 'latitude', 'longitude'
        //     ]);
        // });
    }
};
