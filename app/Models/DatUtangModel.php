<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatUtangModel extends Model
{
    protected $table      = 'dat_utang';
    protected $primaryKey = 'id_utang';
    public $timestamps    = false; 

    protected $fillable = [
        'kode_pemasok',
        'no_transaksi',
        'nominal',
        'status',
        'created_by',
        'tanggal',
    ];

    protected $casts = [
        'nominal'    => 'float',
        'status'     => 'integer',
        'created_by' => 'integer',
        'tanggal'    => 'date:Y-m-d',
    ];

    // accessor label status
    public function getStatusLabelAttribute(): string
    {
        return (int) $this->status === 1 ? 'Lunas' : 'Belum Lunas';
    }
}
