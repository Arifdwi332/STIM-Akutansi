<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->decimal('subtotal', 18, 2)
                  ->default(0)
                  ->after('metode_pembayaran'); // diletakkan sebelum hpp
        });
    }

    public function down(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->dropColumn('subtotal');
        });
    }
};
