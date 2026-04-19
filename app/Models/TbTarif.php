<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TbTarif extends Model
{
    protected $table      = 'tb_tarif';
    protected $primaryKey = 'id_tarif';
    public    $timestamps = false;

    protected $fillable = ['jenis_kendaraan', 'tarif_per_jam', 'denda_per_jam'];

    public function transaksis(): HasMany
    {
        return $this->hasMany(TbTransaksi::class, 'id_tarif');
    }

    // Format rupiah
    public function getRupiahAttribute(): string
    {
        return 'Rp. ' . number_format($this->tarif_per_jam, 0, ',', '.');
    }
}
