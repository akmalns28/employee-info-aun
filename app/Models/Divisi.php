<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model
{
    use HasUuid;

    protected $fillable = ['uuid', 'uuid_departemen', 'nama_divisi'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'uuid_departemen', 'uuid');
    }

    public function karyawans(): HasMany
    {
        return $this->hasMany(
            Karyawan::class,
            'divisi_uuid',
            'uuid'
        );
    }
}
