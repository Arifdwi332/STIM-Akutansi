<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('dat_relasi_akun', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('id_relasi');
        $t->foreign('id_relasi')
            ->references('id')
            ->on('mst_relasi_akun')
            ->onDelete('cascade');

        $t->unsignedBigInteger('id_akun');
        $t->foreign('id_akun')
            ->references('id')
            ->on('mst_akun')
            ->onDelete('restrict');

        $t->string('posisi', 20);        // "debit" / "kredit"
        $t->unsignedInteger('urutan')->default(1);
        $t->string('faktor', 20)->default('1');
        $t->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
