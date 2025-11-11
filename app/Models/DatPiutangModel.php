<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatPiutangModel extends Model
{
    protected $table = 'dat_piutang';
    protected $primaryKey = 'id_piutang';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'no_transaksi',
        'nominal',
        'status',
        'created_by',
        'tanggal',
    ];

    protected $casts = [
        'id_pelanggan' => 'integer',
        'nominal'      => 'float',
        'status'       => 'integer',
        'created_by'   => 'integer',
        'tanggal'      => 'date:Y-m-d',
    ];
}
