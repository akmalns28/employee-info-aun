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
}
