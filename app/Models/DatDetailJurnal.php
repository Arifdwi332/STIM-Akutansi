<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatDetailJurnal extends Model
{
    protected $table = 'dat_detail_jurnal';
    protected $primaryKey = 'id_detail';
    public $timestamps = true;

    protected $fillable = [
        'id_jurnal','id_akun','jml_debit','jml_kredit','id_proyek','kode_pajak'
    ];

    public function header()
    {
        return $this->belongsTo(DatHeaderJurnal::class, 'id_jurnal', 'id_jurnal');
    }

    public function akun()
    {
        return $this->belongsTo(\App\Models\MstAkunModel::class, 'id_akun', 'id');
    }
}
