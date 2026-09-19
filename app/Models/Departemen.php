<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'kode_departemen',
        'departemen',
    ];

    public function divisis()
    {
        return $this->hasMany(
            Divisi::class,
            'uuid_departemen',
            'uuid'
        );
    }

    public function karyawans()
    {
        return $this->hasMany(
            Karyawan::class,
            'departemen_uuid',
            'uuid'
        );
    }
}