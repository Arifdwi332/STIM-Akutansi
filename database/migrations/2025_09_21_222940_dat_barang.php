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

            $table->string('nama_barang', 150);
            $table->string('kategori', 100)->nullable();
            $table->string('satuan_ukur', 50)->nullable(); 
            $table->decimal('harga_jual', 18, 2)->default(0);
            $table->decimal('hpp', 18, 2)->default(0);

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
