<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatAkunModel extends Model
{
    use HasFactory;

    protected $table = 'dat_akun';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'mst_akun_id',
        'kode_sub',
        'nama_sub',
        'saldo_awal',
        'saldo_berjalan',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function induk()
    {
        return $this->belongsTo(MstAkunModel::class, 'mst_akun_id');
    }
}
