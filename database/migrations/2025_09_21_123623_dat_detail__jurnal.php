<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_detail_jurnal', function (Blueprint $table) {
            $table->bigIncrements('id_detail');

            $table->unsignedBigInteger('id_jurnal');
            $table->foreign('id_jurnal')
                  ->references('id_jurnal')
                  ->on('dat_header_jurnal')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('id_akun');
            $table->foreign('id_akun')
                  ->references('id')
                  ->on('mst_akun')
                  ->onDelete('restrict');

            $table->string('jml_debit', 50)->default('0');
            $table->string('jml_kredit', 50)->default('0');
            $table->unsignedBigInteger('id_proyek')->nullable(); 
            $table->string('kode_pajak', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_detail_jurnal');
    }
};
