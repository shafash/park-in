@extends('layouts.app')
@section('title','Transaksi Parkir')
@section('page-title','Transaksi Parkir')
@section('page-sub','Data masuk & keluar kendaraan hari ini')

@section('topbar-right')
<a href="{{ route('petugas.transaksi.masuk') }}" class="btn btn-grn">+ &nbsp;Catat Masuk</a>
@endsection

@section('content')
{{-- STATS --}}
<div class="stats">
  <div class="sc" style="--acc:var(--grn)"><div class="sc-lbl">Masuk Hari Ini</div><div class="sc-val">{{ $masuk }}</div><div class="sc-sub">Total kendaraan masuk</div></div>
  <div class="sc" style="--acc:var(--blu)"><div class="sc-lbl">Keluar Hari Ini</div><div class="sc-val">{{ $keluar }}</div><div class="sc-sub">Total kendaraan keluar</div></div>
  <div class="sc" style="--acc:var(--ora)"><div class="sc-lbl">Di Area Sekarang</div><div class="sc-val">{{ $diarea }}</div><div class="sc-sub">Masih di parkiran</div></div>
  <div class="sc" style="--acc:var(--red)"><div class="sc-lbl">Struk Dicetak</div><div class="sc-val">{{ $struk }}</div><div class="sc-sub">Hari ini</div></div>
</div>

<div class="panel">
  <div class="ph" style="gap:8px">
    <div class="pt" style="color:var(--grn)">
      @include('layouts._icon',['name'=>'trx']) Data Transaksi
    </div>
    <form method="GET" class="sbar" style="flex:1;justify-content:flex-end">
      <input type="text" name="q" value="{{ $q }}" placeholder="Cari Plat Nomor...." style="width:180px">
      <select name="status" onchange="this.form.submit()" style="min-width:140px">
        <option value="">Semua Status</option>
        <option value="masuk"  {{ $status==='masuk'?'selected':'' }}>Masuk</option>
        <option value="keluar" {{ $status==='keluar'?'selected':'' }}>Keluar</option>
      </select>
      <select name="jenis" onchange="this.form.submit()" style="min-width:130px">
        <option value="">Semua Jenis</option>
        @if(isset($jenisList) && count($jenisList))
          @foreach($jenisList as $j)
            @php
              $label = $j === 'lainnya' ? 'Truk' : ucfirst($j);
            @endphp
            <option value="{{ $j }}" {{ $jenis === $j ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        @else
          <option value="motor"   {{ $jenis==='motor'?'selected':'' }}>Motor</option>
          <option value="mobil"   {{ $jenis==='mobil'?'selected':'' }}>Mobil</option>
          <option value="lainnya" {{ $jenis==='lainnya'?'selected':'' }}>Truk</option>
        @endif
      </select>
      <select name="sort" onchange="this.form.submit()" style="min-width:120px">
        <option value="waktu_masuk"  {{ $sort==='waktu_masuk'?'selected':'' }}>Terbaru</option>
        <option value="biaya_total"  {{ $sort==='biaya_total'?'selected':'' }}>Total</option>
      </select>
    </form>
  </div>

  <table class="tbl">
    <thead>
      <tr>
        <th>ID Transaksi</th><th>Plat Nomor</th><th>Jenis</th>
        <th>Waktu Masuk</th><th>Waktu Keluar</th><th>Durasi</th>
        <th>Total</th><th>Status</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    @forelse($transaksis as $t)
    @php
      $kj = $t->kendaraan->jenis_kendaraan ?? '';
      $jc = ($jenisColors[$kj] ?? 'p-blu');
      $jl = $kj === 'lainnya' ? 'Truk' : ($kj ? ucfirst($kj) : '');
      $sc = $t->status === 'masuk' ? 'p-grn' : 'p-blu';
    @endphp
    <tr>
      <td class="t-gray" style="font-size:12px">{{ $t->tid }}</td>
      <td class="fw7">{{ $t->kendaraan->plat_nomor ?? '—' }}</td>
      <td><span class="pill {{ $jc }}">{{ $jl }}</span></td>
      <td style="font-size:13px">{{ $t->waktu_masuk->format('H:i') }} WIB</td>
      <td style="font-size:13px;color:var(--gray)">{{ $t->waktu_keluar ? $t->waktu_keluar->format('H:i').' WIB' : '—' }}</td>
      <td style="font-size:13px">{{ $t->durasiLabel }}</td>
      <td class="fw7 t-grn">{{ $t->biaya_total > 0 ? $t->biayaRupiah : '—' }}</td>
      <td><span class="pill {{ $sc }}"><span class="p-dot"></span> {{ ucfirst($t->status) }}</span></td>
      <td>
        <div class="tbl-acts">
          @if($t->status === 'masuk')
            <button type="button"
                  data-modal="keluar"
                  data-id="{{ $t->id_parkir }}"
                  data-plat="{{ $t->kendaraan->plat_nomor ?? '—' }}"
                  data-jenis="{{ $t->kendaraan->jenisLabel ?? '—' }}"
                  data-jenis-pill="{{ $t->kendaraan->jenisPill ?? 'p-grn' }}"
                  data-masuk="{{ $t->waktu_masuk->format('H:i') }} WIB"
                  data-durasi="{{ $t->durasiLabel }}"
                  data-est="{{ $t->biaya_total > 0 ? $t->biayaRupiah : 'Dihitung saat keluar' }}"
                  class="btn btn-red btn-xs">
            Keluar
          </button>
          @else
            <a href="{{ route('petugas.struk.show', $t->id_parkir) }}" class="btn btn-blu btn-xs">Struk</a>
          @endif
        </div>
      </td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;color:var(--gray);padding:30px">Tidak ada transaksi.</td></tr>
    @endforelse
    </tbody>
  </table>

  <div class="pager">
    <span class="pager-info">Menampilkan {{ $transaksis->firstItem() ?? 0 }} - {{ $transaksis->lastItem() ?? 0 }} dari {{ $transaksis->total() }} transaksi</span>
    <div class="pager-btns">
      @if($transaksis->onFirstPage()) <span class="pb dis">&#8249;</span> @else <a href="{{ $transaksis->previousPageUrl() }}" class="pb">&#8249;</a> @endif
      @foreach($transaksis->getUrlRange(max(1,$transaksis->currentPage()-2), min($transaksis->lastPage(),$transaksis->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}" class="pb {{ $page === $transaksis->currentPage() ? 'act' : '' }}">{{ $page }}</a>
      @endforeach
      @if($transaksis->hasMorePages()) <a href="{{ $transaksis->nextPageUrl() }}" class="pb">&#8250;</a> @else <span class="pb dis">&#8250;</span> @endif
    </div>
  </div>
</div>
@endsection
