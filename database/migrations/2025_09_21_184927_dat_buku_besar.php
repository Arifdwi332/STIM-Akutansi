<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('dat_buku_besar', function (Blueprint $table) {
            $table->bigIncrements('id_bukbes');              
            $table->unsignedBigInteger('id_akun');          
            $table->string('periode', 20);                   
            $table->string('ttl_debit', 50)->default(0); 
            $table->string('ttl_kredit', 50)->default(0);
            $table->string('saldo_akhir', 50)->default(0); 
            $table->timestamps();

           
            $table->foreign('id_akun')->references('id')->on('mst_akun')->onDelete('cascade');
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('dat_buku_besar');
    }
};
