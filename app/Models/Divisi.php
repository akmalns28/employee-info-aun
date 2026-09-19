<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'uuid_departemen',
        'nama_divisi',
    ];

    public function departemen()
    {
        return $this->belongsTo(
            Departemen::class,
            'uuid_departemen',
            'uuid'
        );
    }
}