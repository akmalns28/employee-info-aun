<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Karyawan extends Model
{
    use HasUuid;

    protected $fillable = ['uuid', 'user_uuid', 'uuid_posisi', 'divisi_uuid', 'avatar', 'nip', 'qr_code', 'nama_depan', 'nama_belakang', 'email', 'no_hp', 'jenis_kelamin', 'tempat_lahir', 'tgl_lahir', 'alamat', 'status'];

    protected function namaLengkap(): Attribute
    {
        return Attribute::make(get: fn() => trim(($this->nama_depan ?? '') . ' ' . ($this->nama_belakang ?? '')));
    }

    protected function slugNama(): Attribute
    {
        return Attribute::make(get: fn() => Str::slug($this->nama_lengkap));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_uuid', 'uuid');
    }

    public function posisi(): BelongsTo
    {
        return $this->belongsTo(Posisi::class, 'uuid_posisi', 'uuid');
    }
}
