<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_user', function (Blueprint $table) {
            $table->bigIncrements('user_id'); // PK
            $table->string('nama_pemilik', 100);
            $table->string('nama_umkm', 150);
            $table->string('email')->unique();
            $table->string('password'); // disimpan dalam bentuk hash
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_user');
    }
};
