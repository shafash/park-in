<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbTransaksi extends Model
{
    protected $table      = 'tb_transaksi';
    protected $primaryKey = 'id_parkir';
    public    $timestamps = false;

    protected $fillable = [
        'id_kendaraan', 'waktu_masuk', 'waktu_keluar',
        'id_tarif', 'durasi_jam', 'biaya_total', 'status',
        'id_user', 'id_area',
    ];

    protected function casts(): array
    {
        return [
            'waktu_masuk'  => 'datetime',
            'waktu_keluar' => 'datetime',
        ];
    }

    public function kendaraan()
    {
        return $this->belongsTo(TbKendaraan::class, 'id_kendaraan');
    }

    public function tarif()
    {
        return $this->belongsTo(TbTarif::class, 'id_tarif');
    }

    public function area()
    {
        return $this->belongsTo(TbAreaParkir::class, 'id_area');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getTidAttribute(): string
    {
        return 'TRX-' . str_pad($this->id_parkir, 2, '0', STR_PAD_LEFT);
    }

    public function getBiayaRupiahAttribute(): string
    {
        return 'Rp. ' . number_format($this->biaya_total, 0, ',', '.');
    }

    public function getDurasiLabelAttribute(): string
    {
        return $this->durasi_jam ? $this->durasi_jam . 'j 00m' : '—';
    }
}
