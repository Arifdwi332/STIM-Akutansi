<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatHeaderJurnal extends Model
{
    protected $table = 'dat_header_jurnal';
    protected $primaryKey = 'id_jurnal';
    public $timestamps = true;

    protected $fillable = [
        'tgl_transaksi', 'no_referensi', 'keterangan', 'modul_sumber','created_by',
    ];

    public function details()
    {
        return $this->hasMany(DatDetailJurnal::class, 'id_jurnal', 'id_jurnal');
    }
}
