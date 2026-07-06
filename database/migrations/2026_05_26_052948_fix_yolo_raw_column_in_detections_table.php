<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detection', function (Blueprint $table) {
            $table->longText('yolo_raw')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('detection', function (Blueprint $table) {
            $table->text('yolo_raw')->nullable();
        });
    }
};