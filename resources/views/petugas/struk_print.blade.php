<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk Parkir — {{ $trx->tid }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Courier New',monospace;font-size:12px;background:#fff;color:#111;display:flex;justify-content:center;padding:20px}
.struk{width:280px;padding:20px}
.title{text-align:center;font-size:16px;font-weight:700;border-bottom:2px dashed #bbb;padding-bottom:10px;margin-bottom:12px}
.row{display:flex;justify-content:space-between;gap:8px;line-height:1.4}
.total{border-top:2px dashed #bbb;padding-top:10px;margin-top:8px;display:flex;justify-content:space-between;font-weight:700;font-size:14px}
.foot{text-align:center;margin-top:12px;font-size:10px;color:#777;border-top:1px dashed #bbb;padding-top:10px;line-height:1.8}
@media print{body{padding:0}@page{margin:0}}
</style>
</head>
<body>
<div class="struk">
  <div class="title">Park In<br><span style="font-size:10px;font-weight:400">Struk Parkir Resmi</span></div>
  <div class="row"><span>No. Transaksi</span><span>{{ $trx->tid }}</span></div>
  <div class="row"><span>Tanggal</span><span>{{ $trx->waktu_masuk->format('d/m/Y') }}</span></div>
  <div style="border-top:1px dashed #bbb;margin:6px 0"></div>
  <div class="row"><span>Plat Nomor</span><span style="font-weight:700">{{ $trx->kendaraan->plat_nomor }}</span></div>
  <div class="row"><span>Jenis</span><span>{{ ucfirst($trx->kendaraan->jenis_kendaraan) }}</span></div>
  <div class="row"><span>Warna</span><span>{{ $trx->kendaraan->warna ?: '-' }}</span></div>
  <div class="row"><span>Pemilik</span><span>{{ $trx->kendaraan->pemilik ?: '-' }}</span></div>
  <div style="border-top:1px dashed #bbb;margin:6px 0"></div>
  <div class="row"><span>Area Parkir</span><span>{{ $trx->area->nama_area }}</span></div>
  <div class="row"><span>Waktu Masuk</span><span>{{ $trx->waktu_masuk->format('H:i') }} WIB</span></div>
  <div class="row"><span>Waktu Keluar</span><span>{{ $trx->waktu_keluar->format('H:i') }} WIB</span></div>
  <div class="row"><span>Durasi</span><span>{{ $trx->durasi_jam }} jam</span></div>
  <div class="row"><span>Tarif/Jam</span><span>{{ $trx->tarif->rupiah }}</span></div>
  <div class="total"><span>TOTAL BAYAR</span><span>{{ $trx->biayaRupiah }}</span></div>
  <div class="foot">
    Petugas: {{ $trx->user->nama_lengkap }}<br>
    Dicetak: {{ now()->format('d/m/Y H:i:s') }}<br><br>
    Terima kasih telah menggunakan<br>
    <strong>Park In</strong> — Parkir Lebih Mudah<br>
    Hati-hati di jalan!
  </div>
</div>
<script>window.onload = () => window.print();</script>
</body>
</html>
