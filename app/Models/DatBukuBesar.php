<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatBukuBesar extends Model
{
    protected $table = 'dat_buku_besar';
    protected $primaryKey = 'id_bukbes';
    public $timestamps = true;

    protected $fillable = [
        'id_akun','periode','ttl_debit','ttl_kredit','saldo_akhir','created_by',
    ];

    public function akun()
    {
        return $this->belongsTo(\App\Models\MstAkunModel::class, 'id_akun', 'id');
    }
}
