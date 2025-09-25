<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_pemasok', function (Blueprint $table) {
            // Primary key
            $table->id('id_pemasok');

            // Kode unik pemasok (misal: SUP001, SUP002)
            $table->string('kode_pemasok', 50)->unique();

            // Informasi pemasok
            $table->string('nama_pemasok', 150);
            $table->text('alamat')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('npwp', 50)->nullable();

            // Saldo utang awal
            $table->decimal('saldo_utang', 18, 2)->default(0);

            // Index tambahan
            $table->index('nama_pemasok');
            $table->index('no_hp');

            $table->timestamps();
        });
    }

   
};
