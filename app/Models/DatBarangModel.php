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
        'kode_pemasok',  
        'nama_barang',
        'satuan_ukur',
        'hpp',
        'harga_satuan',
        'harga_jual',
        'stok_awal',
        'stok_akhir',
    ];

    protected $casts = [
        'hpp'          => 'float',
        'harga_satuan' => 'float',
        'harga_jual'   => 'float',
        'stok_awal'    => 'float',
        'stok_akhir'   => 'float',
    ];

 
    public function pemasok()
    {
        return $this->belongsTo(PemasokModel::class, 'kode_pemasok', 'id_pemasok');
    }
}
