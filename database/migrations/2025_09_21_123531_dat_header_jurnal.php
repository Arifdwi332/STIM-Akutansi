<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_header_jurnal', function (Blueprint $table) {
            $table->bigIncrements('id_jurnal');
            $table->date('tgl_transaksi');
            $table->string('no_referensi', 100)->unique();
            $table->text('keterangan')->nullable();
            $table->string('modul_sumber', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_header_jurnal');
    }
};
