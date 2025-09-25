<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_detail_jurnal', function (Blueprint $table) {
            $table->string('jenis_laporan', 50)->nullable()->after('kode_pajak');
        });
    }

    public function down(): void
    {
        Schema::table('dat_detail_jurnal', function (Blueprint $table) {
            $table->dropColumn('jenis_laporan');
        });
    }
};
