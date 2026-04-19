<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TbAreaParkir extends Model
{
    protected $table      = 'tb_area_parkir';
    protected $primaryKey = 'id_area';
    public    $timestamps = false;

    protected $fillable = ['nama_area', 'alamat', 'kapasitas', 'terisi', 'status'];

    public function transaksis(): HasMany
    {
        return $this->hasMany(TbTransaksi::class, 'id_area');
    }

    public function getOkupansiAttribute(): int
    {
        return $this->kapasitas > 0
            ? (int) round($this->terisi / $this->kapasitas * 100)
            : 0;
    }

    public function getOkupansiColorAttribute(): string
    {
        $pct = $this->okupansi;
        if ($pct >= 90) return 'var(--red)';
        if ($pct >= 70) return 'var(--ora)';
        return 'var(--grn)';
    }

    public function getSisaAttribute(): int
    {
        return $this->kapasitas - $this->terisi;
    }
}
