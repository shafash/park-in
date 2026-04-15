@extends('layouts.app')
@section('title','Rekap Transaksi')
@section('page-title','Rekap Transaksi')
@section('page-sub', $subLabel)

@section('topbar-right')
{{-- TABS PERIODE --}}
<div style="display:flex;background:var(--s2);border:1px solid var(--b2);border-radius:9px;overflow:hidden">
  @foreach(['harian'=>'Harian','bulanan'=>'Bulanan','tahunan'=>'Tahunan','custom'=>'Custom'] as $k=>$v)
    <a href="{{ route('owner.rekap.index',['filter'=>$k]) }}"
       style="padding:9px 16px;font-size:13px;font-weight:600;color:{{ $filter===$k?'#111':'var(--gray)' }};background:{{ $filter===$k?'var(--pur)':'transparent' }};text-decoration:none;transition:all .15s;white-space:nowrap">
      {{ $v }}
    </a>
  @endforeach
</div>
<a href="{{ request()->fullUrlWithQuery(['export'=>1]) }}" class="btn btn-out btn-sm">&#8595; Export</a>
@endsection

@section('content')

{{-- CUSTOM DATE RANGE --}}
@if($filter === 'custom')
<div class="panel" style="margin-bottom:20px">
  <form method="GET" action="{{ route('owner.rekap.index') }}"
        style="padding:14px 22px;display:flex;gap:12px;align-items:center;flex-wrap:wrap">
    <input type="hidden" name="filter" value="custom">
    <span class="t-gray" style="font-size:13px">Dari:</span>
    <input type="date" name="dari" value="{{ $df->format('Y-m-d') }}"
           style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 12px;color:var(--wht);font-size:13px;outline:none">
    <span class="t-gray" style="font-size:13px">Sampai:</span>
    <input type="date" name="sampai" value="{{ $dt->format('Y-m-d') }}"
           style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 12px;color:var(--wht);font-size:13px;outline:none">
    <button type="submit" class="btn btn-grn btn-sm">Tampilkan</button>
  </form>
</div>
@endif

{{-- STATS --}}
<div class="stats">
  <div class="sc" style="--acc:var(--grn)">
    <div class="sc-lbl">Total Pendapatan</div>
    <div class="sc-val" style="font-size:22px">Rp. {{ number_format($totalRev,0,',','.') }}</div>
    <div class="sc-sub">Terdaftar di sistem</div>
  </div>
  <div class="sc" style="--acc:var(--pur)">
    <div class="sc-lbl">Total Kendaraan</div>
    <div class="sc-val">{{ $totalKend }}</div>
    <div class="sc-sub">Lokasi parkir</div>
  </div>
  <div class="sc" style="--acc:var(--ora)">
    <div class="sc-lbl">Rata-Rata / Transaksi</div>
    <div class="sc-val" style="font-size:22px">Rp. {{ number_format($avgBiaya,0,',','.') }}</div>
    <div class="sc-sub">Tipe kendaraan</div>
  </div>
  <div class="sc" style="--acc:var(--blu)">
    <div class="sc-lbl">Lokasi Teratas</div>
    <div class="sc-val" style="font-size:16px;color:var(--blu)">{{ $topArea }}</div>
    <div class="sc-sub">Aktivitas tercatat</div>
  </div>
</div>

{{-- REKAP PANEL --}}
<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--pur)">
      @include('layouts._icon',['name'=>'chart']) Data Rekap Transaksi
    </div>
  </div>

  {{-- FILTER --}}
  <form method="GET" action="{{ route('owner.rekap.index') }}"
        style="padding:14px 22px;display:flex;gap:14px;flex-wrap:wrap;border-bottom:1px solid var(--bdr);align-items:flex-end">
    <input type="hidden" name="filter"  value="{{ $filter }}">
    <input type="hidden" name="dari"    value="{{ $df->format('Y-m-d') }}">
    <input type="hidden" name="sampai"  value="{{ $dt->format('Y-m-d') }}">

    <div style="display:flex;flex-direction:column;gap:5px">
      <span style="font-size:11px;color:var(--gray)">Area</span>
      <select name="area" onchange="this.form.submit()"
              style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 13px;color:var(--wht);font-size:13px;outline:none;min-width:160px">
        <option value="0">Semua Area</option>
        @foreach($areaList as $a)
          <option value="{{ $a->id_area }}" {{ $fa==$a->id_area?'selected':'' }}>{{ $a->nama_area }}</option>
        @endforeach
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:5px">
      <span style="font-size:11px;color:var(--gray)">Jenis Kendaraan</span>
      <select name="jenis" onchange="this.form.submit()"
              style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 13px;color:var(--wht);font-size:13px;outline:none;min-width:150px">
        <option value="">Semua Jenis</option>
        <option value="motor"   {{ $fj==='motor'?'selected':'' }}>Motor</option>
        <option value="mobil"   {{ $fj==='mobil'?'selected':'' }}>Mobil</option>
        <option value="lainnya" {{ $fj==='lainnya'?'selected':'' }}>Lainnya</option>
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:5px">
      <span style="font-size:11px;color:var(--gray)">Urutkan</span>
      <select name="sort" onchange="this.form.submit()"
              style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 13px;color:var(--wht);font-size:13px;outline:none;min-width:130px">
        <option value="waktu_masuk" {{ $fsort==='waktu_masuk'?'selected':'' }}>Terbaru</option>
        <option value="biaya_total" {{ $fsort==='biaya_total'?'selected':'' }}>Total Biaya</option>
      </select>
    </div>

    <div style="display:flex;align-items:flex-end">
      <a href="{{ route('owner.rekap.index',['filter'=>$filter]) }}" class="btn btn-out btn-sm">Reset Filter</a>
    </div>
  </form>

  {{-- CHART --}}
  <div class="chart-area">
    <div class="chart-lbl">Grafik Pendapatan Harian</div>
    <div class="bars">
      @foreach($chart as $c)
      @php $pct = $chartMax > 0 ? round($c['val']/$chartMax*100) : 2; @endphp
      <div class="bc"><div class="bf" style="height:{{ $pct }}%"></div></div>
      @endforeach
    </div>
    <div style="display:flex">
      @foreach($chart as $c)
        <div class="bd" style="flex:1">{{ $c['day'] }}</div>
      @endforeach
    </div>
  </div>

  {{-- AREA SUMMARY CARDS --}}
  @php $AC = ['var(--grn)','var(--pur)','var(--blu)','var(--ora)']; @endphp
  <div class="area-cards">
    @forelse($perArea->take(3) as $i => $pa)
    <div class="ac">
      <div class="ac-name">{{ strtoupper($pa->area->nama_area ?? '—') }}</div>
      <div class="ac-rev" style="color:{{ $AC[$i] ?? 'var(--grn)' }}">
        Rp. {{ number_format($pa->tot, 0, ',', '.') }}
      </div>
      <div class="ac-cnt">{{ $pa->jml }} kendaraan</div>
    </div>
    @empty
    <div class="ac"><div class="ac-name" style="color:var(--gray)">Tidak ada data</div></div>
    @endforelse
  </div>

  {{-- TABEL DETAIL --}}
  <table class="tbl">
    <thead>
      <tr>
        <th>Id Transaksi</th><th>Tanggal</th><th>Plat Nomor</th>
        <th>Jenis</th><th>Area</th><th>Masuk</th><th>Keluar</th>
        <th>Durasi</th><th>Total</th>
      </tr>
    </thead>
    <tbody>
    @forelse($rekap as $t)
    @php
      $jc = match($t->kendaraan->jenis_kendaraan??'') { 'motor'=>'p-grn','mobil'=>'p-grn','lainnya'=>'p-ora', default=>'p-grn' };
      $jl = match($t->kendaraan->jenis_kendaraan??'') { 'lainnya'=>'Truk', default=>ucfirst($t->kendaraan->jenis_kendaraan??'') };
    @endphp
    <tr>
      <td class="t-gray" style="font-size:12px">TRX - {{ str_pad($t->id_parkir,4,'0',STR_PAD_LEFT) }}</td>
      <td style="font-size:12px">{{ $t->waktu_masuk->format('d M Y') }}</td>
      <td class="fw7">{{ $t->kendaraan->plat_nomor ?? '—' }}</td>
      <td><span class="pill {{ $jc }}">{{ $jl }}</span></td>
      <td class="t-gray" style="font-size:12px">{{ $t->area->nama_area ?? '—' }}</td>
      <td style="font-size:12px">{{ $t->waktu_masuk->format('H:i') }}</td>
      <td style="font-size:12px">{{ $t->waktu_keluar ? $t->waktu_keluar->format('H:i') : '—' }}</td>
      <td style="font-size:12px">{{ $t->durasiLabel }}</td>
      <td class="fw7 t-grn">{{ $t->biayaRupiah }}</td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;color:var(--gray);padding:30px">Tidak ada data untuk periode ini.</td></tr>
    @endforelse
    </tbody>
  </table>

  {{-- PAGINATION --}}
  <div class="pager">
    <span class="pager-info">Menampilkan {{ $rekap->firstItem() ?? 0 }} - {{ $rekap->lastItem() ?? 0 }} dari {{ $rekap->total() }} kendaraan</span>
    <div class="pager-btns">
      @if($rekap->onFirstPage()) <span class="pb dis">&#8249;</span> @else <a href="{{ $rekap->previousPageUrl() }}" class="pb">&#8249;</a> @endif
      @foreach($rekap->getUrlRange(max(1,$rekap->currentPage()-2), min($rekap->lastPage(),$rekap->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}" class="pb {{ $page === $rekap->currentPage() ? 'act' : '' }}">{{ $page }}</a>
      @endforeach
      @if($rekap->hasMorePages()) <a href="{{ $rekap->nextPageUrl() }}" class="pb">&#8250;</a> @else <span class="pb dis">&#8250;</span> @endif
    </div>
  </div>
</div>
@endsection
