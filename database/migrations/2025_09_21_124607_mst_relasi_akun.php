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
        Schema::create('mst_relasi_akun', function (Blueprint $t) {
        $t->id();
        $t->string('jenis_transaksi', 100);    // pembelian, penjualan, bayar_utang, dsb
        $t->string('tipe_bayar', 50)->nullable(); // tunai / kredit / kosong
        $t->string('nama_relasi', 150)->nullable(); // nama aturan/relasi
        $t->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
