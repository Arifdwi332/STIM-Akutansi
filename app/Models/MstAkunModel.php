<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstAkunModel extends Model
{
    use HasFactory;

    // Nama tabel yang dipakai
    protected $table = 'mst_akun';

    // Primary key tabel
    protected $primaryKey = 'id';

    // Aktifkan timestamps (created_at & updated_at)
    public $timestamps = true;

    // Kolom yang boleh diisi secara mass-assignment
    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kategori_akun',
        'saldo_awal',
        'saldo_berjalan',
        'status_aktif',
        'created_by',
    ];

    // Casting kolom tertentu
    protected $casts = [
        'status_aktif' => 'boolean',
    ];
      public function subAkuns()
    {
        return $this->hasMany(DatAkunModel::class, 'mst_akun_id');
    }
}

