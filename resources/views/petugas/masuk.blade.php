@extends('layouts.app')
@section('title','Catat Masuk')
@section('page-title','Catat Kendaraan Masuk')
@section('page-sub', isset($area) ? 'Area tugasmu: ' . $area->nama_area : 'Pilih area parkir')

@section('topbar-right')
<a href="{{ route('petugas.transaksi.index') }}" class="btn btn-out btn-sm">
  ← Transaksi
</a>
@endsection

@section('content')

{{-- STATS --}}
<div class="stats">
  <div class="sc" style="--acc:var(--grn)">
    <div class="sc-lbl">Masuk Hari Ini</div>
    <div class="sc-val">{{ $masuk }}</div>
    <div class="sc-sub">Total kendaraan masuk</div>
  </div>
  <div class="sc" style="--acc:var(--blu)">
    <div class="sc-lbl">Keluar Hari Ini</div>
    <div class="sc-val">{{ $keluar }}</div>
    <div class="sc-sub">Total kendaraan keluar</div>
  </div>
  <div class="sc" style="--acc:var(--ora)">
    <div class="sc-lbl">Di Area Sekarang</div>
    <div class="sc-val">{{ $diarea }}</div>
    <div class="sc-sub">Masih di parkiran</div>
  </div>
  <div class="sc" style="--acc:var(--red)">
    <div class="sc-lbl">Struk Dicetak</div>
    <div class="sc-val">{{ $struk }}</div>
    <div class="sc-sub">Hari ini</div>
  </div>
</div>

{{-- TWO COLUMN --}}
<div style="display:grid;grid-template-columns:400px 1fr;gap:20px">

  {{-- ─────────────────────── FORM KIRI ─────────────────────── --}}
  <div class="panel">
    <div class="ph">
      <div class="pt" style="color:var(--grn)">
        @include('layouts._icon',['name'=>'plus']) Form Kendaraan Masuk
      </div>
    </div>
    <div class="pb-body">
      <form method="POST" action="{{ route('petugas.transaksi.masuk.store') }}" id="form-masuk">
        @csrf

        {{-- Plat + Autocomplete --}}
        <div class="fg" style="position:relative">
          <label>Plat Nomor
            <span style="color:var(--gray2);font-size:11px;font-weight:400">(ketik untuk cari di database)</span>
          </label>
          <input type="text" name="plat_nomor" id="inp_plat"
                 value="{{ old('plat_nomor') }}"
                 placeholder="Ketik plat, contoh: B 1234 ABC"
                 required autocomplete="off"
                 style="text-transform:uppercase;font-size:16px;font-weight:700;letter-spacing:2px">

          {{-- Dropdown autocomplete --}}
          <div id="plat_dd" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:99;
               background:var(--surf);border:1px solid var(--b2);border-radius:0 0 10px 10px;
               max-height:220px;overflow-y:auto;box-shadow:0 10px 30px rgba(0,0,0,.6)">
          </div>
        </div>

        {{-- Info kendaraan box (tampil setelah pilih dari dropdown) --}}
        <div id="kend_info_box" style="display:none;background:#0d1f0d;border:1.5px solid rgba(137,233,0,.35);
             border-radius:10px;padding:12px 14px;margin-bottom:14px">
          <div style="font-size:9px;font-weight:700;color:var(--grn);text-transform:uppercase;
               letter-spacing:1px;margin-bottom:8px;display:flex;align-items:center;gap:5px">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Data kendaraan ditemukan
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <div>
              <div style="font-size:9px;color:#4a4a4a;text-transform:uppercase;letter-spacing:.5px">Merek / Model</div>
              <div id="ki_merek" style="font-size:13px;font-weight:700;margin-top:2px">—</div>
            </div>
            <div>
              <div style="font-size:9px;color:#4a4a4a;text-transform:uppercase;letter-spacing:.5px">Jenis</div>
              <div id="ki_jenis" style="font-size:13px;font-weight:700;margin-top:2px">—</div>
            </div>
            <div>
              <div style="font-size:9px;color:#4a4a4a;text-transform:uppercase;letter-spacing:.5px">Warna</div>
              <div id="ki_warna" style="font-size:13px;margin-top:2px">—</div>
            </div>
            <div>
              <div style="font-size:9px;color:#4a4a4a;text-transform:uppercase;letter-spacing:.5px">Pemilik</div>
              <div id="ki_pemilik" style="font-size:13px;margin-top:2px">—</div>
            </div>
          </div>
          <div id="ki_foto_wrap" style="display:none;margin-top:8px">
            <img id="ki_foto" src="" alt="foto kendaraan"
                 style="height:64px;border-radius:7px;border:1px solid var(--b2);object-fit:cover">
          </div>
        </div>

        {{-- Jenis / Tarif --}}
        <div class="fg">
          <label>Jenis / Tarif Kendaraan</label>
          <select name="id_tarif" id="sel_tarif" required>
            <option value="">-- Pilih Jenis --</option>
            @foreach($tarifs as $t)
              <option value="{{ $t->id_tarif }}"
                      data-jenis="{{ $t->jenis_kendaraan }}"
                      {{ old('id_tarif') == $t->id_tarif ? 'selected' : '' }}>
                {{ $t->jenis_kendaraan === 'lainnya' ? 'Truk' : ucfirst($t->jenis_kendaraan) }}
                — {{ $t->rupiah }}/jam
              </option>
            @endforeach
          </select>
        </div>

        {{-- Area Parkir --}}
        <div class="fg">
          <label>
            Area Parkir
            @if(isset($area))
              <span style="font-size:9px;font-weight:700;padding:2px 7px;border-radius:6px;
                   background:rgba(137,233,0,.12);color:var(--grn);margin-left:6px">Otomatis</span>
            @endif
          </label>

          @if(isset($area))
            {{-- Petugas punya area tetap: tampilkan sebagai card, tidak bisa diubah --}}
            <input type="hidden" name="id_area" value="{{ $area->id_area }}">
            <div style="background:var(--s2);border:1.5px solid rgba(137,233,0,.4);border-radius:9px;
                 padding:11px 14px;display:flex;align-items:center;justify-content:space-between">
              <div>
                <div style="font-size:14px;font-weight:700">{{ $area->nama_area }}</div>
                <div style="font-size:10px;color:var(--gray2);margin-top:2px">
                  {{ $area->alamat }}
                  · <span style="color:var(--grn);font-weight:600">{{ $area->sisa }} slot tersedia</span>
                </div>
              </div>
              <div style="width:8px;height:8px;border-radius:50%;background:var(--grn);flex-shrink:0"></div>
            </div>
          @else
            {{-- Petugas tidak punya area tetap: bisa pilih --}}
            <select name="id_area" required>
              <option value="">-- Pilih Area --</option>
              @foreach($areas as $a)
                <option value="{{ $a->id_area }}" {{ old('id_area') == $a->id_area ? 'selected' : '' }}>
                  {{ $a->nama_area }} (sisa {{ $a->sisa }} slot)
                </option>
              @endforeach
            </select>
          @endif
        </div>

        {{-- Waktu masuk --}}
        <div class="fg">
          <label>Waktu Masuk</label>
          <input type="text" id="waktu_inp" readonly style="color:var(--gray)">
        </div>

        <button type="submit" class="btn btn-grn"
                style="width:100%;justify-content:center;padding:13px;font-size:14px">
          Catat Kendaraan Masuk
        </button>
      </form>
    </div>
  </div>

  {{-- ─────────────────── PANEL KANAN ────────────────────── --}}
  @if(isset($area))
  <div class="panel" style="display:flex;flex-direction:column">

    {{-- Header --}}
    <div class="ph">
      <div class="pt">{{ $area->nama_area }}</div>
      <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:10px;
           background:rgba(137,233,0,.12);color:var(--grn);border:1px solid rgba(137,233,0,.25)">
        Area tugasmu
      </span>
    </div>

    {{-- ── Donut Chart ── --}}
    @php
      $kap   = $area->kapasitas;
      $isi   = $area->terisi;
      $sisa  = $area->sisa;
      $pct   = $kap > 0 ? round($isi / $kap * 100) : 0;
      $r     = 36;
      $circ  = round(2 * 3.14159 * $r, 2);         // ~226.19
      $dash1 = round($circ * $pct / 100, 2);        // terisi (oranye)
      $dash2 = round($circ - $dash1, 2);            // kosong (hijau)
      $occColor = $pct >= 90 ? 'var(--red)' : ($pct >= 70 ? 'var(--ora)' : 'var(--grn)');
    @endphp

    <div style="padding:16px 20px;border-bottom:1px solid var(--bdr)">
      <div style="display:flex;align-items:center;gap:18px">

        {{-- Donut SVG --}}
        <div style="position:relative;width:90px;height:90px;flex-shrink:0">
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="{{ $r }}" fill="none" stroke="var(--s2)" stroke-width="9"/>
            {{-- Arc terisi --}}
            <circle cx="45" cy="45" r="{{ $r }}" fill="none" stroke="{{ $occColor }}" stroke-width="9"
              stroke-dasharray="{{ $dash1 }} {{ $dash2 }}"
              stroke-dashoffset="{{ round($circ * 0.25, 2) }}"
              stroke-linecap="round"
              style="transition:stroke-dasharray .6s ease"/>
            {{-- Arc sisa --}}
            @if($sisa > 0)
            <circle cx="45" cy="45" r="{{ $r }}" fill="none" stroke="var(--grn)" stroke-width="9"
              stroke-dasharray="{{ $dash2 }} {{ $dash1 }}"
              stroke-dashoffset="{{ round($circ * 0.25 - $dash1, 2) }}"
              stroke-linecap="round"
              opacity="0.35"/>
            @endif
          </svg>
          <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center">
            <div style="font-size:18px;font-weight:800;color:{{ $occColor }};line-height:1">{{ $pct }}%</div>
            <div style="font-size:9px;color:var(--gray2);margin-top:2px">terisi</div>
          </div>
        </div>

        {{-- Legend --}}
        <div style="flex:1">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:9px;height:9px;border-radius:50%;background:var(--grn)"></div>
              <span style="font-size:12px;color:var(--gray)">Tersedia</span>
            </div>
            <div style="text-align:right">
              <div style="font-size:16px;font-weight:800;color:var(--grn)">{{ $sisa }}</div>
              <div style="font-size:9px;color:var(--gray2)">slot kosong</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:9px;height:9px;border-radius:50%;background:{{ $occColor }}"></div>
              <span style="font-size:12px;color:var(--gray)">Terisi</span>
            </div>
            <div style="text-align:right">
              <div style="font-size:16px;font-weight:800;color:{{ $occColor }}">{{ $isi }}</div>
              <div style="font-size:9px;color:var(--gray2)">kendaraan</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:9px;height:9px;border-radius:50%;background:var(--b2)"></div>
              <span style="font-size:12px;color:var(--gray)">Kapasitas</span>
            </div>
            <div style="text-align:right">
              <div style="font-size:16px;font-weight:800;color:var(--gray2)">{{ $kap }}</div>
              <div style="font-size:9px;color:var(--gray2)">total slot</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Progress bar --}}
      <div style="margin-top:14px">
        <div style="display:flex;justify-content:space-between;margin-bottom:5px">
          <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Tingkat Okupansi</span>
          <span style="font-size:11px;font-weight:700;color:var(--gray)">{{ $isi }} dari {{ $kap }} slot</span>
        </div>
        <div style="height:7px;background:var(--s2);border-radius:4px;overflow:hidden">
          <div style="height:7px;width:{{ $pct }}%;background:{{ $occColor }};border-radius:4px;transition:width .6s ease"></div>
        </div>
      </div>
    </div>

    {{-- ── Live Activity Feed ── --}}
    <div style="flex:1;overflow-y:auto">
      <div style="padding:12px 20px 8px;display:flex;align-items:center;justify-content:space-between">
        <span style="font-size:10px;font-weight:700;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">
          Aktivitas hari ini
        </span>
        <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--grn)">
          <div id="live_pulse" style="width:6px;height:6px;border-radius:50%;background:var(--grn)"></div>
          Live
        </div>
      </div>

      @forelse($liveFeed as $trx)
      @php
        $isIn  = $trx->status === 'masuk';
        $icoC  = $isIn ? 'rgba(137,233,0,.12)' : 'rgba(58,143,255,.12)';
        $icoS  = $isIn ? 'var(--grn)' : 'var(--blu)';
        $stBg  = $isIn ? 'rgba(137,233,0,.12)' : 'rgba(58,143,255,.12)';
        $stC   = $isIn ? 'var(--grn)' : 'var(--blu)';
        $stLbl = $isIn ? 'Masuk' : 'Keluar';
        $waktu = $isIn
            ? $trx->waktu_masuk->format('H:i')
            : $trx->waktu_keluar?->format('H:i');
      @endphp
      <div style="display:flex;align-items:center;gap:10px;padding:9px 20px;border-top:1px solid var(--bdr)">
        {{-- Icon --}}
        <div style="width:30px;height:30px;border-radius:8px;background:{{ $icoC }};
             display:flex;align-items:center;justify-content:center;flex-shrink:0">
          @if($isIn)
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $icoS }}" stroke-width="2.5">
              <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
            </svg>
          @else
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $icoS }}" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
            </svg>
          @endif
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:0">
          <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $trx->kendaraan->plat_nomor ?? '—' }}
          </div>
          <div style="font-size:10px;color:var(--gray2);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $trx->kendaraan->jenisLabel ?? '' }}
            @if($trx->kendaraan->merek) · {{ $trx->kendaraan->merek }} @endif
            @if(!$isIn && $trx->biaya_total > 0) · {{ $trx->biayaRupiah }} @endif
          </div>
        </div>

        {{-- Status & waktu --}}
        <div style="text-align:right;flex-shrink:0">
          <div style="font-size:10px;color:var(--gray2)">{{ $waktu }}</div>
          <div style="font-size:9px;font-weight:700;padding:2px 7px;border-radius:5px;
               background:{{ $stBg }};color:{{ $stC }};margin-top:3px">
            {{ $stLbl }}
          </div>
        </div>
      </div>
      @empty
      <div style="padding:30px 20px;text-align:center;color:var(--gray2);font-size:13px">
        Belum ada aktivitas hari ini.
      </div>
      @endforelse
    </div>

  </div>

  @else
  {{-- Petugas tidak punya area tetap --}}
  <div class="panel" style="display:flex;align-items:center;justify-content:center;min-height:200px">
    <div style="text-align:center;color:var(--gray2)">
      <div style="font-size:32px;margin-bottom:10px">⚠️</div>
      <div style="font-weight:700;color:var(--ora);margin-bottom:6px">Akun belum punya area tugas</div>
      <div style="font-size:12px">Hubungi admin untuk di-assign ke area parkir.</div>
    </div>
  </div>
  @endif

</div>{{-- /grid --}}
@endsection

@push('scripts')
<script>
const CARI_URL = "{{ route('petugas.transaksi.cari-plat') }}";
const inpPlat  = document.getElementById('inp_plat');
const dd       = document.getElementById('plat_dd');
const selTarif = document.getElementById('sel_tarif');
const kiBox    = document.getElementById('kend_info_box');

// Map jenis → id_tarif untuk auto-select
const tarifMap = {};
selTarif.querySelectorAll('option[data-jenis]').forEach(o => { tarifMap[o.dataset.jenis] = o.value; });

let timer;
inpPlat.addEventListener('input', function () {
  const q = this.value.trim();
  clearTimeout(timer);
  if (q.length < 2) { dd.style.display = 'none'; hideKendInfo(); return; }
  timer = setTimeout(() => {
    fetch(`${CARI_URL}?q=${encodeURIComponent(q)}`, { headers:{'Accept':'application/json'} })
      .then(r => r.json())
      .then(data => renderDD(data))
      .catch(() => { dd.style.display = 'none'; });
  }, 280);
});

function renderDD(data) {
  if (!data.length) {
    dd.innerHTML = `<div style="padding:12px 16px;font-size:12px;color:var(--gray)">
      Plat tidak ditemukan — akan otomatis didaftarkan saat disimpan.
    </div>`;
    dd.style.display = 'block';
    return;
  }
  dd.innerHTML = data.map(k => `
    <div class="dd-item" data-plat="${k.plat_nomor}" data-jenis="${k.jenis_kendaraan}"
         data-jlbl="${k.jenis_label}" data-merek="${k.merek}"
         data-warna="${k.warna}" data-pemilik="${k.pemilik}" data-foto="${k.foto_url}"
         data-idtarif="${k.id_tarif_match ?? ''}"
         style="padding:10px 16px;cursor:pointer;border-bottom:1px solid var(--bdr);
                display:flex;align-items:center;gap:12px">
      <div style="flex:1">
        <div style="font-size:14px;font-weight:700;letter-spacing:1px">${k.plat_nomor}</div>
        <div style="font-size:11px;color:var(--gray);margin-top:2px">
          ${k.jenis_label}${k.merek?' · '+k.merek:''}${k.pemilik?' · '+k.pemilik:''}
        </div>
      </div>
      ${k.foto_url && !k.foto_url.includes('/img/') ? `<img src="${k.foto_url}" style="width:44px;height:34px;object-fit:cover;border-radius:6px;border:1px solid var(--b2)">` : ''}
    </div>
  `).join('');

  dd.querySelectorAll('.dd-item').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = 'var(--s2)');
    el.addEventListener('mouseleave', () => el.style.background = '');
    el.addEventListener('click',      () => pilihKendaraan(el));
  });
  dd.style.display = 'block';
}

function pilihKendaraan(el) {
  inpPlat.value = el.dataset.plat;
  dd.style.display = 'none';

  if (el.dataset.idtarif) {
    selTarif.value = el.dataset.idtarif;
  } else if (tarifMap[el.dataset.jenis]) {
    selTarif.value = tarifMap[el.dataset.jenis];
  }

  // Tampilkan info box
  document.getElementById('ki_merek').textContent   = el.dataset.merek   || '—';
  document.getElementById('ki_jenis').textContent   = el.dataset.jlbl    || '—';
  document.getElementById('ki_warna').textContent   = el.dataset.warna   || '—';
  document.getElementById('ki_pemilik').textContent = el.dataset.pemilik || '—';

  const fw = document.getElementById('ki_foto_wrap');
  const fi = document.getElementById('ki_foto');
  if (el.dataset.foto && !el.dataset.foto.includes('/img/')) {
    fi.src = el.dataset.foto; fw.style.display = 'block';
  } else {
    fw.style.display = 'none';
  }
  kiBox.style.display = 'block';
}

function hideKendInfo() { kiBox.style.display = 'none'; }

// Tutup dropdown jika klik di luar
document.addEventListener('click', e => {
  if (!inpPlat.contains(e.target) && !dd.contains(e.target)) dd.style.display = 'none';
});

// Live clock
function updateClock() {
  const d   = new Date();
  const pad = n => String(n).padStart(2,'0');
  document.getElementById('waktu_inp').value =
    `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
}
updateClock();
setInterval(updateClock, 1000);

// Live pulse blink
let blink = true;
setInterval(() => {
  const dot = document.getElementById('live_pulse');
  if (dot) { dot.style.opacity = blink ? '1' : '0.15'; blink = !blink; }
}, 800);
</script>
@endpush