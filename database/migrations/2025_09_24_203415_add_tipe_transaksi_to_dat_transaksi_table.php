<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->string('tipe_pembayaran', 30)->nullable()->after('no_transaksi');
            // $table->index('tipe_transaksi'); // opsional
        });
    }

    public function down(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            // $table->dropIndex(['tipe_transaksi']); // jika diindex
            $table->dropColumn('tipe_pembayaran');
        });
    }
};
