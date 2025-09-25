<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_header_jurnal', function (Blueprint $table) {
            // nama default index: {table}_{column}_unique
            $table->dropUnique('dat_header_jurnal_no_referensi_unique');
            // alternatif (beberapa versi laravel mendukung array):
            // $table->dropUnique(['no_referensi']);
        });
    }

    public function down(): void
    {
        Schema::table('dat_header_jurnal', function (Blueprint $table) {
            $table->unique('no_referensi');
        });
    }
};
