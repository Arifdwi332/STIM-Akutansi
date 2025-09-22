<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_pelanggan', function (Blueprint $table) {
            // Primary key
            $table->id('id_pelanggan');

            // Fields
            $table->string('nama_pelanggan', 150);
            $table->text('alamat')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('npwp', 50)->nullable();

            // Saldo piutang
            $table->decimal('saldo_piutang', 18, 2)->default(0);

            // Indexes (opsional, bisa dihapus bila tidak perlu)
            $table->index('nama_pelanggan');
            $table->index('no_hp');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_pelanggan');
    }
};
