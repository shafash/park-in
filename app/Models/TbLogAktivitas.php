<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbLogAktivitas extends Model
{
    protected $table      = 'tb_log_aktivitas';
    protected $primaryKey = 'id_log';
    public    $timestamps = false;

    protected $fillable = ['id_user', 'aktivitas', 'waktu_aktivitas'];

    protected function casts(): array
    {
        return ['waktu_aktivitas' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /** Helper: catat log aktivitas */
    public static function catat(int $idUser, string $aktivitas): void
    {
        static::create([
            'id_user'         => $idUser,
            'aktivitas'       => $aktivitas,
            'waktu_aktivitas' => now(),
        ]);
    }

    public function getDotColorAttribute(): string
    {
        return match($this->user->role ?? '') {
            'admin'   => 'var(--blu)',
            'petugas' => 'var(--grn)',
            'owner'   => 'var(--pur)',
            default   => 'var(--gray)',
        };
    }
}
