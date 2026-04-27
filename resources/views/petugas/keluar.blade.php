@extends('layouts.app')
@section('title','Proses Keluar')
@section('page-title','Proses Kendaraan Keluar')
@section('page-sub','Hitung biaya dan catat waktu keluar')

@section('topbar-right')
<a href="{{ route('petugas.transaksi.masuk') }}" class="btn btn-grn">+ &nbsp;Catat Masuk</a>
@endsection

@section('content')
<div class="stats">
  <div class="sc" style="--acc:var(--grn)"><div class="sc-lbl">Masuk Hari Ini</div><div class="sc-val">{{ $masuk }}</div><div class="sc-sub">Total kendaraan masuk</div></div>
  <div class="sc" style="--acc:var(--blu)"><div class="sc-lbl">Keluar Hari Ini</div><div class="sc-val">{{ $keluar }}</div><div class="sc-sub">Total kendaraan keluar</div></div>
  <div class="sc" style="--acc:var(--ora)"><div class="sc-lbl">Di Area Sekarang</div><div class="sc-val">{{ $diarea }}</div><div class="sc-sub">Masih di parkiran</div></div>
  <div class="sc" style="--acc:var(--red)"><div class="sc-lbl">Struk Dicetak</div><div class="sc-val">{{ $struk }}</div><div class="sc-sub">Hari ini</div></div>
</div>

<div class="panel">
  <div class="ph"><div class="pt">Detail Kendaraan</div></div>
  <div class="pb-body">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Plat Nomor</div>
        <div style="font-size:26px;font-weight:800;letter-spacing:3px;margin-top:6px">{{ $trx->kendaraan->plat_nomor }}</div>
      </div>
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Area Parkir</div>
        <div style="font-size:16px;font-weight:700;margin-top:6px">{{ $trx->area->nama_area }}</div>
      </div>
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Waktu Masuk</div>
        <div style="font-size:18px;font-weight:700;font-family:monospace;margin-top:6px">{{ $trx->waktu_masuk->format('H:i') }} WIB</div>
      </div>
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Estimasi Biaya</div>
        <div style="font-size:24px;font-weight:800;color:var(--grn);margin-top:6px">
            Rp. {{ number_format($estBiaya, 0, ',', '.') }}
          </div>
          <div style="font-size:11px;color:var(--gray2);margin-top:6px">
            <div>Tarif awal: Rp. {{ number_format($trx->tarif->tarif_awal ?? 0,0,',','.') }}</div>
            <div>Tarif per jam: Rp. {{ number_format($trx->tarif->tarif_per_jam ?? 0,0,',','.') }}</div>
            <div>Batas durasi sebelum denda: {{ $trx->tarif->batas_durasi_jam ?? 0 }}j</div>
            <div>Denda per jam (jika melebihi): Rp. {{ number_format($trx->tarif->denda_per_jam ?? 0,0,',','.') }}</div>
            <div style="margin-top:6px">Estimasi durasi: <strong>{{ $durEst }} jam</strong></div>
          </div>
      </div>
    </div>

    <form method="POST" action="{{ route('petugas.transaksi.keluar.store', $trx->id_parkir) }}">
      @csrf
      <button type="submit" class="btn btn-grn" style="width:100%;justify-content:center;padding:13px;font-size:14px"
        onclick="return confirm('Proses kendaraan keluar sekarang?')">
        Proses Keluar &amp; Hitung Biaya Final
      </button>
    </form>
  </div>
</div>

<div style="margin-top:12px">
  <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-out">← Kembali ke Transaksi</a>
</div>
@endsection
