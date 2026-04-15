{{-- Admin stats: total_user, area_aktif, jenis_tarif, log_hari --}}
<div class="stats">
  <div class="sc" style="--acc:var(--blu)">
    <div class="sc-lbl">Total User</div>
    <div class="sc-val">{{ $total_user }}</div>
    <div class="sc-sub">Terdaftar di sistem</div>
  </div>
  <div class="sc" style="--acc:var(--grn)">
    <div class="sc-lbl">Area Aktif</div>
    <div class="sc-val">{{ $area_aktif }}</div>
    <div class="sc-sub">Lokasi parkir</div>
  </div>
  <div class="sc" style="--acc:var(--ora)">
    <div class="sc-lbl">Jenis Tarif</div>
    <div class="sc-val">{{ $jenis_tarif }}</div>
    <div class="sc-sub">Tipe kendaraan</div>
  </div>
  <div class="sc" style="--acc:var(--red)">
    <div class="sc-lbl">Log Hari Ini</div>
    <div class="sc-val">{{ $log_hari }}</div>
    <div class="sc-sub">Aktivitas tercatat</div>
  </div>
</div>
