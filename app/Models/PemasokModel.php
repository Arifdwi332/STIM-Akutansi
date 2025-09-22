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
        'nama_pemasok',
        'alamat',
        'no_hp',
        'email',
        'npwp',
        'saldo_utang',
    ];
}
