<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    protected $table      = 'tb_user';
    protected $primaryKey = 'id_user';
    public    $timestamps = false;

    protected $fillable = [
        'nama_lengkap', 'username', 'password', 'role', 'status_aktif', 'id_area',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // Relasi ke area parkir (untuk petugas)
    public function area(): BelongsTo
    {
        return $this->belongsTo(TbAreaParkir::class, 'id_area');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(TbTransaksi::class, 'id_user');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TbLogAktivitas::class, 'id_user');
    }

    // Inisial nama untuk avatar
    public function getInisialAttribute(): string
    {
        $parts = explode(' ', trim($this->nama_lengkap));
        $init  = strtoupper(substr($parts[0], 0, 1));
        if (isset($parts[1])) $init .= strtoupper(substr($parts[1], 0, 1));
        return $init;
    }
}
