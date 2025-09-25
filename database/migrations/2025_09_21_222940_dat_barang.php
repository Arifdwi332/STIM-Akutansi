<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_barang', function (Blueprint $table) {
            $table->id('id_barang');

            // Relasi ke pemasok
             $table->string('kode_pemasok', 50)->unique();

            // Data barang
            $table->string('nama_barang', 150);
            $table->string('satuan_ukur', 50);

            // Harga
            $table->decimal('hpp', 18, 2)->default(0);           // harga beli
            $table->decimal('harga_satuan', 18, 2)->default(0);  // harga pokok satuan
            $table->decimal('harga_jual', 18, 2)->default(0);    // harga jual

            // Stok
            $table->decimal('stok_awal', 18, 2)->default(0);
            $table->decimal('stok_akhir', 18, 2)->default(0);

            $table->timestamps();

         
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_barang');
    }
};
