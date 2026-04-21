@extends('layouts.app')
@section('title','Rekap Transaksi')
@section('page-title','Rekap Transaksi')
@section('page-sub', ($area ? $area->nama_area . ' · ' : '') . $subLabel)

@section('topbar-right')
{{-- TABS periode --}}
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
    Export
</a>
@endsection

@section('content')

{{-- ─── Custom date range form ───────────────────────── --}}
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

{{-- ─── 4 STAT CARDS ─────────────────────────────────── --}}
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

{{-- ─── CHART + STATS PANEL ──────────────────────────── --}}
<div class="panel">
    <div class="ph">
        <div class="pt" style="color:var(--pur)">
            @include('layouts._icon', ['name' => 'chart'])
            Data Rekap Transaksi
        </div>
        {{-- Area badge / lock --}}
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

    {{-- Revenue besar + KPI boxes + area chart --}}
    <div style="padding:18px 20px">

        {{-- Baris atas: revenue kiri, KPI kanan --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;
                    gap:14px;margin-bottom:16px;flex-wrap:wrap">

            {{-- Kiri: Revenue besar (dari B) --}}
            <div style="flex:1;min-width:200px">
                <div style="font-size:30px;font-weight:800;letter-spacing:-1.5px;
                            color:var(--grn);line-height:1">
                    {{ 'Rp ' . number_format($totalRev, 0, ',', '.') }}
                </div>
                <div style="font-size:11px;color:var(--gray2);margin-top:4px">
                    Total pendapatan · 12 hari terakhir
                </div>
                {{-- Legenda hari ini vs kemarin --}}
                <div style="display:flex;gap:14px;margin-top:10px">
                    <div style="display:flex;align-items:center;gap:5px">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--grn)"></div>
                        <span style="font-size:10px;color:var(--gray2)">Hari ini</span>
                        <span style="font-size:11px;font-weight:700;color:var(--grn)">
                            {{ 'Rp ' . number_format($revHariIni, 0, ',', '.') }}
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:5px">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--pur);opacity:.5"></div>
                        <span style="font-size:10px;color:var(--gray2)">Kemarin</span>
                        <span style="font-size:11px;font-weight:700;color:var(--gray)">
                            {{ 'Rp ' . number_format($revKemarin, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Kanan: 3 KPI boxes (dari A) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;flex-shrink:0;width:220px">
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--pur)">{{ $totalKend }}</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;text-transform:uppercase;letter-spacing:.5px">Kend.</div>
                </div>
                @if($area)
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--ora)">{{ $area->okupansi }}%</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;text-transform:uppercase;letter-spacing:.5px">Okupansi</div>
                </div>
                @else
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--ora)">{{ $totalArea }}</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;text-transform:uppercase;letter-spacing:.5px">Area</div>
                </div>
                @endif
                <div style="background:var(--s2);border-radius:9px;padding:10px;text-align:center">
                    <div style="font-size:16px;font-weight:800;color:var(--blu)">{{ $sedangParkir }}</div>
                    <div style="font-size:9px;color:var(--gray2);margin-top:3px;text-transform:uppercase;letter-spacing:.5px">Aktif</div>
                </div>
            </div>
        </div>

        {{-- Smooth area chart (dari A) — SVG server-side + JS polish --}}
        <div style="position:relative;height:80px;margin-bottom:6px">
            <svg id="rekap_chart" viewBox="0 0 480 80" preserveAspectRatio="none"
                 style="display:block;width:100%;height:80px">
                <defs>
                    <linearGradient id="gradHari" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#89E900" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#89E900" stop-opacity="0.02"/>
                    </linearGradient>
                    <linearGradient id="gradKem" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#cc44ff" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="#cc44ff" stop-opacity="0.02"/>
                    </linearGradient>
                </defs>
                {{-- Grid lines --}}
                <line x1="0" y1="20" x2="480" y2="20" stroke="var(--s2)" stroke-width="1"/>
                <line x1="0" y1="40" x2="480" y2="40" stroke="var(--s2)" stroke-width="1"/>
                <line x1="0" y1="60" x2="480" y2="60" stroke="var(--s2)" stroke-width="1"/>
                {{-- Chart di-render oleh JS dari data PHP --}}
            </svg>
        </div>

        {{-- Tanggal label --}}
        <div id="chart_dates" style="display:flex;justify-content:space-between;padding:0 2px;margin-bottom:0"></div>
    </div>

    {{-- Stats strip 4 kolom (dari B) --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--bdr);
                border-top:1px solid var(--bdr)">
        <div style="background:var(--card);padding:12px 16px;text-align:center">
            <div style="font-size:15px;font-weight:800;color:var(--grn)">
                {{ 'Rp ' . number_format($totalRev, 0, ',', '.') }}
            </div>
            <div style="font-size:9px;color:var(--gray2);margin-top:4px;text-transform:uppercase;letter-spacing:.5px">Pendapatan</div>
        </div>
        <div style="background:var(--card);padding:12px 16px;text-align:center">
            <div style="font-size:15px;font-weight:800;color:var(--pur)">{{ $totalKend }}</div>
            <div style="font-size:9px;color:var(--gray2);margin-top:4px;text-transform:uppercase;letter-spacing:.5px">Kendaraan</div>
        </div>
        <div style="background:var(--card);padding:12px 16px;text-align:center">
            <div style="font-size:15px;font-weight:800;color:var(--ora)">
                {{ 'Rp ' . number_format($avgBiaya, 0, ',', '.') }}
            </div>
            <div style="font-size:9px;color:var(--gray2);margin-top:4px;text-transform:uppercase;letter-spacing:.5px">Rata-rata</div>
        </div>
        <div style="background:var(--card);padding:12px 16px;text-align:center">
            <div style="font-size:15px;font-weight:800;color:var(--blu)">{{ $sedangParkir }}</div>
            <div style="font-size:9px;color:var(--gray2);margin-top:4px;text-transform:uppercase;letter-spacing:.5px">Aktif Parkir</div>
        </div>
    </div>
</div>

{{-- ─── FILTER + TABLE PANEL ─────────────────────────── --}}
<div class="panel">
    <form method="GET">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <input type="hidden" name="dari"   value="{{ $df->format('Y-m-d') }}">
        <input type="hidden" name="sampai" value="{{ $dt->format('Y-m-d') }}">

        <div style="padding:12px 20px;display:flex;gap:12px;align-items:flex-end;
                    flex-wrap:wrap;border-bottom:1px solid var(--bdr)">

            {{-- Area: locked jika owner punya area, dropdown jika tidak --}}
            <div style="display:flex;flex-direction:column;gap:4px">
                <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Area</span>
                @if($area)
                {{-- Locked field --}}
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
                {{-- Owner tanpa area tetap bisa pilih --}}
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
                            @php $label = $j === 'lainnya' ? 'Truk / Lainnya' : ucfirst($j); @endphp
                            <option value="{{ $j }}" {{ $fj === $j ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    @else
                        <option value="motor"   {{ $fj === 'motor'   ? 'selected' : '' }}>Motor</option>
                        <option value="mobil"   {{ $fj === 'mobil'   ? 'selected' : '' }}>Mobil</option>
                        <option value="lainnya" {{ $fj === 'lainnya' ? 'selected' : '' }}>Truk / Lainnya</option>
                    @endif
                </select>
            </div>

            <div style="display:flex;flex-direction:column;gap:4px">
                <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Urutkan</span>
                <select name="sort" onchange="this.form.submit()"
                        style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;
                               padding:8px 12px;color:var(--wht);font-size:12px;outline:none;min-width:130px">
                    <option value="waktu_masuk"  {{ $fsort === 'waktu_masuk'  ? 'selected' : '' }}>Terbaru</option>
                    <option value="biaya_total"  {{ $fsort === 'biaya_total'  ? 'selected' : '' }}>Total Biaya</option>
                </select>
            </div>

            <a href="?filter={{ $filter }}" class="btn btn-out btn-sm" style="align-self:flex-end">
                Reset Filter
            </a>
        </div>
    </form>

    {{-- Tabel --}}
    <table class="tbl">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Plat Nomor</th>
                <th>Jenis</th>
                <th>Area</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Durasi</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rekap as $t)
        @php
            $kj = $t->kendaraan->jenis_kendaraan ?? '';
            $jc = $jenisColors[$kj] ?? 'p-blu';
            $jl = $kj === 'lainnya' ? 'Truk' : ($kj ? ucfirst($kj) : '—');
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

    {{-- Pagination --}}
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
                <a href="{{ $url }}"
                   class="pb {{ $page === $rekap->currentPage() ? 'act' : '' }}">
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
{{-- Data chart dari controller (12 hari terakhir) --}}
const chartData = @json($chart);       // [{date, day, val}, ...]
const chartMax  = {{ $chartMax }};

const svg   = document.getElementById('rekap_chart');
const dRow  = document.getElementById('chart_dates');
const W     = 480;
const H     = 80;
const PAD   = 4;

if (chartData && chartData.length) {
    const n     = chartData.length;
    const step  = (W - PAD * 2) / (n - 1);

    // Konversi ke koordinat SVG (y dari BAWAH)
    const pts = chartData.map((d, i) => ({
        x : PAD + i * step,
        y : chartMax > 0 ? H - PAD - ((d.val / chartMax) * (H - PAD * 2)) : H - PAD,
        v : d.val,
        day: d.day,
    }));

    // ── Smooth bezier path helper ──────────────────
    function bezierPath(points) {
        if (points.length < 2) return '';
        let d = `M ${points[0].x},${points[0].y}`;
        for (let i = 0; i < points.length - 1; i++) {
            const cp1x = points[i].x + (points[i+1].x - points[i].x) * 0.45;
            const cp1y = points[i].y;
            const cp2x = points[i+1].x - (points[i+1].x - points[i].x) * 0.45;
            const cp2y = points[i+1].y;
            d += ` C ${cp1x},${cp1y} ${cp2x},${cp2y} ${points[i+1].x},${points[i+1].y}`;
        }
        return d;
    }

    const linePath = bezierPath(pts);
    const last     = pts[pts.length - 1];
    const first    = pts[0];

    // ── Area fill ──────────────────────────────────
    const areaPath = `${linePath} L ${last.x},${H} L ${first.x},${H} Z`;
    const areEl = document.createElementNS('http://www.w3.org/2000/svg','path');
    areEl.setAttribute('d', areaPath);
    areEl.setAttribute('fill', 'url(#gradHari)');
    svg.appendChild(areEl);

    // ── Line ──────────────────────────────────────
    const lineEl = document.createElementNS('http://www.w3.org/2000/svg','path');
    lineEl.setAttribute('d', linePath);
    lineEl.setAttribute('fill', 'none');
    lineEl.setAttribute('stroke', '#89E900');
    lineEl.setAttribute('stroke-width', '1.8');
    lineEl.setAttribute('stroke-linecap', 'round');
    svg.appendChild(lineEl);

    // ── Endpoint dot (hari ini) ───────────────────
    const dotEl = document.createElementNS('http://www.w3.org/2000/svg','circle');
    dotEl.setAttribute('cx', last.x);
    dotEl.setAttribute('cy', last.y);
    dotEl.setAttribute('r', '3.5');
    dotEl.setAttribute('fill', '#89E900');
    svg.appendChild(dotEl);

    // ── Hover dots (invisible, show on hover) ─────
    pts.forEach((p, i) => {
        const c = document.createElementNS('http://www.w3.org/2000/svg','circle');
        c.setAttribute('cx', p.x); c.setAttribute('cy', p.y);
        c.setAttribute('r', '3'); c.setAttribute('fill', '#89E900');
        c.setAttribute('opacity', i === n-1 ? '0' : '0'); // already shown above for last
        c.style.cursor = 'pointer';
        svg.appendChild(c);
    });

    // ── Date labels ───────────────────────────────
    pts.forEach((p, i) => {
        const s = document.createElement('span');
        s.textContent = p.day;
        s.style.cssText = `flex:1;text-align:center;font-size:9px;
            color:${i === n-1 ? '#89E900' : '#333'}`;
        dRow.appendChild(s);
    });
}
</script>
@endpush