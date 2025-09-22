<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBarangModel extends Model

{
    protected $table = 'dat_barang';
    protected $primaryKey = 'id_barang';
    public $timestamps = true;

    protected $fillable = [
        'nama_barang','kategori','satuan_ukur',
        'harga_jual','hpp','stok_awal','stok_akhir',
    ];

    protected $casts = [
        'harga_jual' => 'float',
        'hpp'        => 'float',
        'stok_awal'  => 'float',
        'stok_akhir' => 'float',
    ];
}