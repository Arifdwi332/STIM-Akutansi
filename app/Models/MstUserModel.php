<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstUserModel extends Model
{
    use HasFactory;

    protected $table = 'mst_user';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_pemilik',
        'nama_umkm',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
