<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemasokModel extends Model
{
    protected $table = 'dat_pemasok';
    protected $primaryKey = 'id_pemasok';
    public $timestamps = true;

    protected $fillable = [
        'kode_pemasok',
        'nama_pemasok',
        'alamat',
        'no_hp',
        'email',
        'npwp',
        'saldo_utang',
    ];

    protected $casts = [
        'saldo_utang' => 'float',
    ];

    
    public function barang()
    {
        return $this->hasMany(DatBarangModel::class, 'kode_pemasok', 'id_pemasok');
    }
}
