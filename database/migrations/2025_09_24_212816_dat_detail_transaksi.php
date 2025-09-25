<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');

            $table->string('no_transaksi', 50);
            $table->string('kode_akun', 30);
            $table->string('nama_akun', 100);
            $table->decimal('jml_debit', 18, 2)->default(0);
            $table->decimal('jml_kredit', 18, 2)->default(0);
            $table->string('jenis_laporan', 50);

            $table->timestamps();

            $table->index('kode_akun');
            $table->index(['no_transaksi', 'kode_akun']);

            // FK ke dat_transaksi.no_transaksi
            $table->foreign('no_transaksi')
                  ->references('no_transaksi')
                  ->on('dat_transaksi')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dat_detail_transaksi', function (Blueprint $table) {
            $table->dropForeign(['no_transaksi']);
        });

        Schema::dropIfExists('dat_detail_transaksi');
    }
};
