<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TbKendaraan extends Model
{
    protected $table      = 'tb_kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public    $timestamps = false;

    protected $fillable = [
        'plat_nomor', 'jenis_kendaraan', 'merek', 'warna', 'pemilik', 'foto', 'id_user', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(TbTransaksi::class, 'id_kendaraan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('uploads/kendaraan/' . $this->foto))) {
            return asset('uploads/kendaraan/' . $this->foto);
        }
        // Default placeholder by jenis
        return match($this->jenis_kendaraan) {
            'motor'   => asset('img/motor.png'),
            'mobil'   => asset('img/mobil.png'),
            default   => asset('img/truk.png'),
        };
    }

    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis_kendaraan) {
            'motor'   => 'Motor',
            'mobil'   => 'Mobil',
            'lainnya' => 'Truk',
            default   => ucfirst($this->jenis_kendaraan),
        };
    }

    public function getJenisPillAttribute(): string
    {
        return match($this->jenis_kendaraan) {
            'motor'   => 'p-grn',
            'mobil'   => 'p-blu',
            'lainnya' => 'p-ora',
            default   => 'p-blu',
        };
    }
}
