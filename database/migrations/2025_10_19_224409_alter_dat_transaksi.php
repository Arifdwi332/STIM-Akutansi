<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->decimal('biaya_lain', 18, 2)
                  ->default(0)
                  ->after('hpp');
            $table->decimal('diskon', 18, 2)
                  ->default(0)
                  ->after('biaya_lain');
        });

        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->decimal('pajak', 18, 2)
                  ->default(0)
                  ->after('diskon')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('dat_transaksi', function (Blueprint $table) {
            $table->dropColumn(['biaya_lain', 'diskon']);
        });
    }
};
