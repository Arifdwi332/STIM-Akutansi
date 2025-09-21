<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_akun', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('kode_akun', 50)->unique();
            $table->string('nama_akun', 150);
            $table->string('kategori_akun', 100);
            $table->string('saldo_awal', 50)->default('0');
            $table->string('saldo_berjalan', 50)->default('0');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_akun');
    }
};
