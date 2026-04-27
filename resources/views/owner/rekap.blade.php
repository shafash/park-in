@extends('layouts.app')
@section('title','Rekap Transaksi')
@section('page-title','Rekap Transaksi')
@section('page-sub', ($area ? $area->nama_area . ' · ' : '') . $subLabel)

@section('topbar-right')
<div style="display:flex;background:var(--s2);border:1px solid var(--b2);border-radius:8px;overflow:hidden">
    @foreach(['harian'=>'Harian','bulanan'=>'Bulanan','tahunan'=>'Tahunan','custom'=>'Custom'] as $k => $v)
        <a href="{{ request()->fullUrlWithQuery(['filter' => $k]) }}"
           style="padding:8px 14px;font-size:12px;font-weight:600;white-space:nowrap;text-decoration:none;
                  color:{{ $filter === $k ? '#111' : 'var(--gray)' }};
                  background:{{ $filter === $k ? 'var(--pur)' : 'transparent' }};
                  transition:all .15s">
            {{ $v }}
        </a>
    @endforeach
</div>
<a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-out btn-sm">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    Export Excel
</a>
@endsection

@section('content')

@if($filter === 'custom')
<div class="panel" style="margin-bottom:16px">
    <form method="GET" style="padding:12px 20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap">
        <input type="hidden" name="filter" value="custom">
        <span style="font-size:12px;color:var(--gray)">Dari:</span>
        <input type="date" name="dari" value="{{ $df->format('Y-m-d') }}"
               style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;
                      padding:8px 12px;color:var(--wht);font-size:13px;outline:none">
        <span style="font-size:12px;color:var(--gray)">Sampai:</span>
        <input type="date" name="sampai" value="{{ $dt->format('Y-m-d') }}"
               style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;
                      padding:8px 12px;color:var(--wht);font-size:13px;outline:none">
        <button type="submit" class="btn btn-grn btn-sm">Tampilkan</button>
    </form>
</div>
@endif

<div class="stats">
    <div class="sc" style="--acc:var(--grn)">
        <div class="sc-lbl">Total Pendapatan</div>
        <div class="sc-val" style="font-size:22px">{{ 'Rp ' . number_format($totalRev, 0, ',', '.') }}</div>
        <div class="sc-sub">{{ $area ? $area->nama_area : 'Semua area' }}</div>
    </div>
    <div class="sc" style="--acc:var(--pur)">
        <div class="sc-lbl">Total Kendaraan</div>
        <div class="sc-val">{{ $totalKend }}</div>
        <div class="sc-sub">Transaksi selesai</div>
    </div>
    <div class="sc" style="--acc:var(--ora)">
        <div class="sc-lbl">Rata-rata / Trx</div>
        <div class="sc-val" style="font-size:20px">{{ 'Rp ' . number_format($avgBiaya, 0, ',', '.') }}</div>
        <div class="sc-sub">Per kendaraan</div>
    </div>
    <div class="sc" style="--acc:var(--blu)">
        <div class="sc-lbl">Sedang Parkir</div>
        <div class="sc-val">{{ $sedangParkir }}</div>
        <div class="sc-sub">Kendaraan aktif</div>
    </div>
</div>

<div class="panel">
    <div class="ph">
        <div class="pt" style="color:var(--pur)">
            @include('layouts._icon', ['name' => 'chart'])
            Data Rekap Transaksi
        </div>
        @if($area)
        <div style="display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;
                    padding:3px 10px;border-radius:6px;background:rgba(204,68,255,.1);
                    color:var(--pur);border:1px solid rgba(204,68,255,.2)">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            {{ $area->nama_area }}
        </div>
        @endif
    </div>

    <div style="padding:18px 20px">

        <div style="display:flex;align-items:flex-start;justify-content:space-between;
                    gap:14px;margin-bottom:16px;flex-wrap:wrap">

            <div style="flex:1;min-width:200px">
                <div style="font-size:30px;font-weight:800;letter-spacing:-1.5px;
                            color:var(--grn);line-height:1">
                    {{ 'Rp ' . number_format($totalRev, 0, ',', '.') }}
                </div>
                <div style="font-size:11px;color:var(--gray2);margin-top:4px">
                    Total pendapatan · 12 hari terakhir
                </div>

                <div style="display:flex;gap:14px;margin-top:10px">
                    <div style="display:flex;align-items:center;gap:5px">
                        <svg width="22" height="8" viewBox="0 0 22 8">
                            <line x1="0" y1="4" x2="22" y2="4" stroke="#89E900" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="11" cy="4" r="2.5" fill="#89E900"/>
                        </svg>
                        <span style="font-size:10px;color:var(--gray2)">Hari ini</span>
                        <span style="font-size:11px;font-weight:700;color:var(--grn)">
                            {{ 'Rp ' . number_format($revHariIni, 0, ',', '.') }}
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:5px">
                        <svg width="22" height="8" viewBox="0 0 22 8">
                            <line x1="0" y1="4" x2="22" y2="4" stroke="#cc44ff"
                                  stroke-width="1.5" stroke-dasharray="4 3"
                                  stroke-linecap="round" opacity="0.7"/>
                        </svg>
                        <span style="font-size:10px;color:var(--gray2)">Kemarin</span>
                        <span style="font-size:11px;font-weight:700;color:var(--gray)">
                            {{ 'Rp ' . number_format($revKemarin, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;
                        flex-shrink:0;width:220px">
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--pur)">{{ $totalKend }}</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;
                                text-transform:uppercase;letter-spacing:.5px">Kend.</div>
                </div>
                @if($area)
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--ora)">{{ $area->okupansi }}%</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;
                                text-transform:uppercase;letter-spacing:.5px">Okupansi</div>
                </div>
                @else
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--ora)">{{ $totalArea }}</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;
                                text-transform:uppercase;letter-spacing:.5px">Area</div>
                </div>
                @endif
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--blu)">{{ $sedangParkir }}</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;
                                text-transform:uppercase;letter-spacing:.5px">Aktif</div>
                </div>
            </div>
        </div>

        <div style="position:relative;height:120px;margin-bottom:6px">
            <svg id="rekap_chart" viewBox="0 0 480 120" preserveAspectRatio="none"
                 style="display:block;width:100%;height:120px">
                <defs>
                    <linearGradient id="gradHari" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%"   stop-color="#89E900" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#89E900" stop-opacity="0.02"/>
                    </linearGradient>
                    <linearGradient id="gradKem" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%"   stop-color="#cc44ff" stop-opacity="0.15"/>
                        <stop offset="100%" stop-color="#cc44ff" stop-opacity="0.01"/>
                    </linearGradient>
                </defs>
                {{-- Grid lines --}}
                <line x1="0" y1="20" x2="480" y2="20" stroke="var(--s2)" stroke-width="1"/>
                <line x1="0" y1="40" x2="480" y2="40" stroke="var(--s2)" stroke-width="1"/>
                <line x1="0" y1="60" x2="480" y2="60" stroke="var(--s2)" stroke-width="1"/>
                {{-- Paths dirender JS --}}
            </svg>
        </div>
        <div id="chart_dates" style="display:flex;justify-content:space-between;padding:0 2px"></div>
    </div>

    <!-- summary below chart removed (already shown above) -->
</div>

<div class="panel">
    <form method="GET">
        <input type="hidden" name="filter"  value="{{ $filter }}">
        <input type="hidden" name="dari"    value="{{ $df->format('Y-m-d') }}">
        <input type="hidden" name="sampai"  value="{{ $dt->format('Y-m-d') }}">

        <div style="padding:12px 20px;display:flex;gap:12px;align-items:flex-end;
                    flex-wrap:wrap;border-bottom:1px solid var(--bdr)">

            <div style="display:flex;flex-direction:column;gap:4px">
                <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Area</span>
                @if($area)
                <div style="background:var(--s2);border:1px solid rgba(204,68,255,.35);border-radius:8px;
                            padding:8px 12px;font-size:12px;color:var(--pur);font-weight:700;
                            display:flex;align-items:center;gap:6px;white-space:nowrap">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:.6">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    {{ $area->nama_area }}
                </div>
                @else
                <select name="area" onchange="this.form.submit()"
                        style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;
                               padding:8px 12px;color:var(--wht);font-size:12px;outline:none;min-width:160px">
                    <option value="0">Semua Area</option>
                    @foreach($areaList as $al)
                        <option value="{{ $al->id_area }}" {{ $fa == $al->id_area ? 'selected' : '' }}>
                            {{ $al->nama_area }}
                        </option>
                    @endforeach
                </select>
                @endif
            </div>

            <div style="display:flex;flex-direction:column;gap:4px">
                <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Jenis Kendaraan</span>
                <select name="jenis" onchange="this.form.submit()"
                        style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;
                               padding:8px 12px;color:var(--wht);font-size:12px;outline:none;min-width:150px">
                    <option value="">Semua Jenis</option>
                    @if(isset($jenisList) && count($jenisList))
                        @foreach($jenisList as $j)
                            <option value="{{ $j }}" {{ $fj === $j ? 'selected' : '' }}>{{ ucfirst($j) }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div style="display:flex;flex-direction:column;gap:4px">
                <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Urutkan</span>
                <select name="sort" onchange="this.form.submit()"
                        style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;
                               padding:8px 12px;color:var(--wht);font-size:12px;outline:none;min-width:130px">
                    <option value="waktu_masuk" {{ $fsort === 'waktu_masuk' ? 'selected' : '' }}>Terbaru</option>
                    <option value="biaya_total" {{ $fsort === 'biaya_total' ? 'selected' : '' }}>Total Biaya</option>
                </select>
            </div>

            <a href="?filter={{ $filter }}" class="btn btn-out btn-sm" style="align-self:flex-end">
                Reset Filter
            </a>
        </div>
    </form>

    <table class="tbl">
        <thead>
            <tr>
                <th>ID Transaksi</th><th>Tanggal</th><th>Plat Nomor</th>
                <th>Jenis</th><th>Area</th><th>Masuk</th>
                <th>Keluar</th><th>Durasi</th><th>Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rekap as $t)
        @php
            $kj = $t->kendaraan->jenis_kendaraan ?? '';
            $jc = $jenisColors[$kj] ?? 'p-blu';
            $jl = $kj ? ucfirst($kj) : '—';
        @endphp
        <tr>
            <td class="t-gray" style="font-size:11px">
                TRX-{{ str_pad($t->id_parkir, 4, '0', STR_PAD_LEFT) }}
            </td>
            <td style="font-size:11px">{{ $t->waktu_masuk->format('d M Y') }}</td>
            <td class="fw7">{{ $t->kendaraan->plat_nomor ?? '—' }}</td>
            <td><span class="pill {{ $jc }}">{{ $jl }}</span></td>
            <td class="t-gray" style="font-size:11px">{{ $t->area->nama_area ?? '—' }}</td>
            <td style="font-size:11px">{{ $t->waktu_masuk->format('H:i') }}</td>
            <td style="font-size:11px">{{ $t->waktu_keluar?->format('H:i') ?? '—' }}</td>
            <td style="font-size:11px">{{ $t->durasiLabel }}</td>
            <td class="fw7 t-grn">{{ $t->biayaRupiah }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center;color:var(--gray2);padding:32px">
                Tidak ada data untuk periode ini.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

    <div class="pager">
        <span class="pager-info">
            Menampilkan {{ $rekap->firstItem() ?? 0 }} - {{ $rekap->lastItem() ?? 0 }}
            dari {{ $rekap->total() }} transaksi
        </span>
        <div class="pager-btns">
            @if($rekap->onFirstPage())
                <span class="pb dis">&#8249;</span>
            @else
                <a href="{{ $rekap->previousPageUrl() }}" class="pb">&#8249;</a>
            @endif
            @foreach($rekap->getUrlRange(
                max(1, $rekap->currentPage() - 2),
                min($rekap->lastPage(), $rekap->currentPage() + 2)
            ) as $page => $url)
                <a href="{{ $url }}" class="pb {{ $page === $rekap->currentPage() ? 'act' : '' }}">
                    {{ $page }}
                </a>
            @endforeach
            @if($rekap->hasMorePages())
                <a href="{{ $rekap->nextPageUrl() }}" class="pb">&#8250;</a>
            @else
                <span class="pb dis">&#8250;</span>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
const chartHariIni = @json($chart);        
const chartKemarin = @json($chartKemarin);  
const chartMax     = {{ $chartMax }};        

const svg  = document.getElementById('rekap_chart');
const dRow = document.getElementById('chart_dates');

if (!svg) return;

const W   = 480;
const H   = 120;
const PAD = 6;
// allow rendering when either series exists
const n = (chartHariIni.length || chartKemarin.length || 0);
if (n === 0) {
    // no data — render placeholders
    dRow.innerHTML = Array.from({length:12}).map(() => '<span style="flex:1;text-align:center;font-size:9px;color:#666">-</span>').join('');
    return;
}

function xOf(i) {
    return PAD + i * ((W - PAD * 2) / (n - 1));
}

function yOf(val) {
    if (chartMax <= 0) return H - PAD;
    return H - PAD - (val / chartMax) * (H - PAD * 2);
}

function makePath(points) {
    if (points.length < 2) return '';
    let d = `M ${points[0].x},${points[0].y}`;
    for (let i = 0; i < points.length - 1; i++) {
        const tensionX = (points[i+1].x - points[i].x) * 0.45;
        const cp1x = points[i].x   + tensionX;
        const cp1y = points[i].y;
        const cp2x = points[i+1].x - tensionX;
        const cp2y = points[i+1].y;
        d += ` C ${cp1x},${cp1y} ${cp2x},${cp2y} ${points[i+1].x},${points[i+1].y}`;
    }
    return d;
}

function svgEl(tag, attrs) {
    const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.entries(attrs).forEach(([k, v]) => el.setAttribute(k, v));
    return el;
}

// Prepare bar chart rendering (grouped bars: kemarin + hari ini)
const valsHari = chartHariIni.map(d => d?.val ?? 0);
const valsKem  = chartKemarin.map(d => d?.val ?? 0);

const slotW = (W - PAD * 2) / n;
// make bars larger: use most of slot and larger min widths
const groupW = Math.max(18, slotW * 0.9);
const barW = Math.max(10, Math.floor(groupW / 2) - 2);

for (let i = 0; i < n; i++){
    const xCenter = PAD + i * slotW + slotW / 2;
    const groupLeft = xCenter - groupW / 2;

    const valKem = valsKem[i] || 0;
    const valHari = valsHari[i] || 0;

    const yKem = valKem ? yOf(valKem) : H - PAD;
    const yHari = valHari ? yOf(valHari) : H - PAD;

    const hKem = Math.max(0, H - PAD - yKem);
    const hHari = Math.max(0, H - PAD - yHari);

    // Kemarin (ungu) — left bar
    const kemX = groupLeft;
    if (hKem > 0) {
        svg.appendChild(svgEl('rect', {
            x: kemX,
            y: yKem,
            width: barW,
            height: hKem,
            fill: '#cc44ff',
            rx: 3
        }));
    } else {
        // tiny indicator when zero
        svg.appendChild(svgEl('rect', { x: kemX, y: H - PAD - 2, width: barW, height: 2, fill: '#f3e6ff', rx:2 }));
    }

    // Hari ini (hijau) — right bar
    const hariX = groupLeft + barW + 4;
    if (hHari > 0) {
        svg.appendChild(svgEl('rect', {
            x: hariX,
            y: yHari,
            width: barW,
            height: hHari,
            fill: '#89E900',
            rx: 3
        }));
    } else {
        svg.appendChild(svgEl('rect', { x: hariX, y: H - PAD - 2, width: barW, height: 2, fill: '#e9f9d9', rx:2 }));
    }

    // labels under chart
    const day = chartHariIni[i]?.day ?? chartKemarin[i]?.day ?? '';
    const s = document.createElement('span');
    s.textContent = day;
    s.style.cssText = `flex:1;text-align:center;font-size:9px;color:${i === n - 1 ? '#89E900' : '#333'}`;
    dRow.appendChild(s);
}
});
</script>
@endpush