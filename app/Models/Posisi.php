<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Posisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'nama_posisi',
        'kode_posisi',
    ];

    protected static function booted()
    {
        static::creating(function ($posisi) {
            if (!$posisi->uuid) {
                $posisi->uuid = (string) Str::uuid();
            }
        });
    }

    public function karyawans(): HasMany
    {
        return $this->hasMany(
            Karyawan::class,
            'uuid_posisi',
            'uuid'
        );
    }
}