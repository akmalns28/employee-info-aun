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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->nullable();
            $table->uuid('divisi_uuid')->nullable();
            $table->uuid('uuid_posisi')->nullable();
            $table->text('qr_code')->nullable();
            $table->text('avatar')->nullable();
            $table->string('nip')->unique()->nullable();
            $table->string('nama_depan');
            $table->string('nama_belakang')->nullable();
            $table->string('jabatan')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('email')->unique();
            $table->string('no_hp')->unique()->nullable();
            $table->boolean('status')->default(1);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('divisi_uuid')->references('uuid')->on('divisis')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('uuid_posisi')->references('uuid')->on('posisis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
