<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_akun', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK sub akun

            // relasi ke akun induk
            $table->unsignedBigInteger('mst_akun_id');
            $table->foreign('mst_akun_id')
                  ->references('id')
                  ->on('mst_akun')
                  ->onDelete('cascade'); // hapus sub jika akun induk dihapus

            // info sub akun
            $table->string('kode_sub', 50)->unique();      // kode sub akun
            $table->string('nama_sub', 150);               // nama sub akun
            $table->string('saldo_awal', 50)->default('0');
            $table->string('saldo_berjalan', 50)->default('0');
            $table->boolean('status_aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_akun');
    }
};
