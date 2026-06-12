<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_uuid',
        'departemen_uuid',
        'avatar',
        'nip',
        'qr_code',
        'nama_depan',
        'nama_belakang',
        'email',
        'jabatan',
        'no_hp',
        'jenis_kelamin',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_uuid', 'uuid');
    }
}
