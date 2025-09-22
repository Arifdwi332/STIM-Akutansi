<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_no_transaksi_to_dat_transaksi.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->string('no_transaksi', 50)->nullable()->after('jenis_transaksi');
            $table->index('no_transaksi');
        });
    }

    public function down(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->dropIndex(['no_transaksi']);
            $table->dropColumn('no_transaksi');
        });
    }
};
