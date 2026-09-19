<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('divisis', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->uuid('uuid_departemen');
            $table->string('nama_divisi', 100);
            $table->timestamps();

            $table->foreign('uuid_departemen')->references('uuid')->on('departemens')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisis');
    }
};
