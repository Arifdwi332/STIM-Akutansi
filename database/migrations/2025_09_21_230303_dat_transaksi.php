<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');

            $table->unsignedBigInteger('id_kontak')->nullable(); 
            $table->foreignId('id_barang')
                  ->nullable()
                  ->constrained('dat_barang', 'id_barang')
                  ->restrictOnDelete();
            $table->unsignedBigInteger('id_pajak')->nullable();

            $table->string('jenis_transaksi', 50);        
            $table->date('tgl');
            $table->decimal('jml_barang', 18, 2)->default(1);
            $table->string('satuan', 50)->nullable();      
            $table->string('metode_pembayaran', 50)->nullable(); 

            // Nilai uang
            $table->decimal('hpp', 18, 2)->default(0);    
            $table->decimal('pajak', 18, 2)->default(0);   
            $table->decimal('total', 18, 2)->default(0);   
            $table->index('tgl');
            $table->index('jenis_transaksi');
            $table->index('id_kontak');
            $table->index('id_pajak');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_transaksi');
    }
};
