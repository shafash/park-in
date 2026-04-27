<?php $__env->startSection('title','Catat Masuk'); ?>
<?php $__env->startSection('page-title','Catat Kendaraan Masuk'); ?>
<?php $__env->startSection('page-sub', isset($area) ? 'Area tugasmu: ' . $area->nama_area : 'Pilih area parkir'); ?>

<?php $__env->startSection('topbar-right'); ?>
<a href="<?php echo e(route('petugas.transaksi.index')); ?>" class="btn btn-out btn-sm">
  ← Transaksi
</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="stats">
  <div class="sc" style="--acc:var(--grn)">
    <div class="sc-lbl">Masuk Hari Ini</div>
    <div class="sc-val"><?php echo e($masuk); ?></div>
    <div class="sc-sub">Total kendaraan masuk</div>
  </div>
  <div class="sc" style="--acc:var(--blu)">
    <div class="sc-lbl">Keluar Hari Ini</div>
    <div class="sc-val"><?php echo e($keluar); ?></div>
    <div class="sc-sub">Total kendaraan keluar</div>
  </div>
  <div class="sc" style="--acc:var(--ora)">
    <div class="sc-lbl">Di Area Sekarang</div>
    <div class="sc-val"><?php echo e($diarea); ?></div>
    <div class="sc-sub">Masih di parkiran</div>
  </div>
  <div class="sc" style="--acc:var(--red)">
    <div class="sc-lbl">Struk Dicetak</div>
    <div class="sc-val"><?php echo e($struk); ?></div>
    <div class="sc-sub">Hari ini</div>
  </div>
</div>


<div style="display:grid;grid-template-columns:400px 1fr;gap:20px">

  
  <div class="panel">
    <div class="ph">
      <div class="pt" style="color:var(--grn)">
        <?php echo $__env->make('layouts._icon',['name'=>'plus'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Form Kendaraan Masuk
      </div>
    </div>
    <div class="pb-body">
      <form method="POST" action="<?php echo e(route('petugas.transaksi.masuk.store')); ?>" id="form-masuk">
        <?php echo csrf_field(); ?>

        
        <div class="fg" style="position:relative">
          <label>Plat Nomor
            <span style="color:var(--gray2);font-size:11px;font-weight:400">(ketik untuk cari di database)</span>
          </label>
          <input type="text" name="plat_nomor" id="inp_plat"
                 value="<?php echo e(old('plat_nomor')); ?>"
                 placeholder="Ketik plat, contoh: B 1234 ABC"
                 required autocomplete="off"
                 style="text-transform:uppercase;font-size:16px;font-weight:700;letter-spacing:2px">

          
          <div id="plat_dd" class="no-scrollbar" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:99;
            background:var(--surf);border:1px solid var(--b2);border-radius:0 0 10px 10px;
            max-height:220px;overflow-y:auto;box-shadow:0 10px 30px rgba(0,0,0,.6)">
          </div>
        </div>

        
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

        
        <div class="fg">
          <label>Jenis / Tarif Kendaraan</label>
          <select name="id_tarif" id="sel_tarif" required>
            <option value="">-- Pilih Jenis --</option>
            <?php $__currentLoopData = $tarifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($t->id_tarif); ?>"
                      data-jenis="<?php echo e($t->jenis_kendaraan); ?>"
                      <?php echo e(old('id_tarif') == $t->id_tarif ? 'selected' : ''); ?>>
                <?php echo e($t->jenis_kendaraan === 'lainnya' ? 'Truk' : ucfirst($t->jenis_kendaraan)); ?>

                — <?php echo e($t->rupiah); ?>/jam
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="fg">
          <label>
            Area Parkir
            <?php if(isset($area)): ?>
              <span style="font-size:9px;font-weight:700;padding:2px 7px;border-radius:6px;
                   background:rgba(137,233,0,.12);color:var(--grn);margin-left:6px">Otomatis</span>
            <?php endif; ?>
          </label>

          <?php if(isset($area)): ?>
            
            <input type="hidden" name="id_area" value="<?php echo e($area->id_area); ?>">
            <div style="background:var(--s2);border:1.5px solid rgba(137,233,0,.4);border-radius:9px;
                 padding:11px 14px;display:flex;align-items:center;justify-content:space-between">
              <div>
                <div style="font-size:14px;font-weight:700"><?php echo e($area->nama_area); ?></div>
                <div style="font-size:10px;color:var(--gray2);margin-top:2px">
                  <?php echo e($area->alamat); ?>

                  · <span style="color:var(--grn);font-weight:600"><?php echo e($area->sisa); ?> slot tersedia</span>
                </div>
              </div>
              <div style="width:8px;height:8px;border-radius:50%;background:var(--grn);flex-shrink:0"></div>
            </div>
          <?php else: ?>
            
            <select name="id_area" required>
              <option value="">-- Pilih Area --</option>
              <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($a->id_area); ?>" <?php echo e(old('id_area') == $a->id_area ? 'selected' : ''); ?>>
                  <?php echo e($a->nama_area); ?> (sisa <?php echo e($a->sisa); ?> slot)
                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          <?php endif; ?>
        </div>

        
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

  
  <?php if(isset($area)): ?>
  <div class="panel" style="display:flex;flex-direction:column">

    
    <div class="ph">
      <div class="pt"><?php echo e($area->nama_area); ?></div>
      <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:10px;
           background:rgba(137,233,0,.12);color:var(--grn);border:1px solid rgba(137,233,0,.25)">
        Area tugasmu
      </span>
    </div>

    
    <?php
      $kap   = $area->kapasitas;
      $isi   = $area->terisi;
      $sisa  = $area->sisa;
      $pct   = $kap > 0 ? round($isi / $kap * 100) : 0;
      $r     = 36;
      $circ  = round(2 * 3.14159 * $r, 2);         // ~226.19
      $dash1 = round($circ * $pct / 100, 2);        // terisi (oranye)
      $dash2 = round($circ - $dash1, 2);            // kosong (hijau)
      $occColor = $pct >= 90 ? 'var(--red)' : ($pct >= 70 ? 'var(--ora)' : 'var(--grn)');
    ?>

    <div style="padding:16px 20px;border-bottom:1px solid var(--bdr)">
      <div style="display:flex;align-items:center;gap:18px">

        
        <div style="position:relative;width:90px;height:90px;flex-shrink:0">
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="<?php echo e($r); ?>" fill="none" stroke="var(--s2)" stroke-width="9"/>
            
            <circle cx="45" cy="45" r="<?php echo e($r); ?>" fill="none" stroke="<?php echo e($occColor); ?>" stroke-width="9"
              stroke-dasharray="<?php echo e($dash1); ?> <?php echo e($dash2); ?>"
              stroke-dashoffset="<?php echo e(round($circ * 0.25, 2)); ?>"
              stroke-linecap="round"
              style="transition:stroke-dasharray .6s ease"/>
            
            <?php if($sisa > 0): ?>
            <circle cx="45" cy="45" r="<?php echo e($r); ?>" fill="none" stroke="var(--grn)" stroke-width="9"
              stroke-dasharray="<?php echo e($dash2); ?> <?php echo e($dash1); ?>"
              stroke-dashoffset="<?php echo e(round($circ * 0.25 - $dash1, 2)); ?>"
              stroke-linecap="round"
              opacity="0.35"/>
            <?php endif; ?>
          </svg>
          <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center">
            <div style="font-size:18px;font-weight:800;color:<?php echo e($occColor); ?>;line-height:1"><?php echo e($pct); ?>%</div>
            <div style="font-size:9px;color:var(--gray2);margin-top:2px">terisi</div>
          </div>
        </div>

        
        <div style="flex:1">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:9px;height:9px;border-radius:50%;background:var(--grn)"></div>
              <span style="font-size:12px;color:var(--gray)">Tersedia</span>
            </div>
            <div style="text-align:right">
              <div style="font-size:16px;font-weight:800;color:var(--grn)"><?php echo e($sisa); ?></div>
              <div style="font-size:9px;color:var(--gray2)">slot kosong</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:9px;height:9px;border-radius:50%;background:<?php echo e($occColor); ?>"></div>
              <span style="font-size:12px;color:var(--gray)">Terisi</span>
            </div>
            <div style="text-align:right">
              <div style="font-size:16px;font-weight:800;color:<?php echo e($occColor); ?>"><?php echo e($isi); ?></div>
              <div style="font-size:9px;color:var(--gray2)">kendaraan</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:9px;height:9px;border-radius:50%;background:var(--b2)"></div>
              <span style="font-size:12px;color:var(--gray)">Kapasitas</span>
            </div>
            <div style="text-align:right">
              <div style="font-size:16px;font-weight:800;color:var(--gray2)"><?php echo e($kap); ?></div>
              <div style="font-size:9px;color:var(--gray2)">total slot</div>
            </div>
          </div>
        </div>
      </div>

      
      <div style="margin-top:14px">
        <div style="display:flex;justify-content:space-between;margin-bottom:5px">
          <span style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:.8px">Tingkat Okupansi</span>
          <span style="font-size:11px;font-weight:700;color:var(--gray)"><?php echo e($isi); ?> dari <?php echo e($kap); ?> slot</span>
        </div>
        <div style="height:7px;background:var(--s2);border-radius:4px;overflow:hidden">
          <div style="height:7px;width:<?php echo e($pct); ?>%;background:<?php echo e($occColor); ?>;border-radius:4px;transition:width .6s ease"></div>
        </div>
      </div>
    </div>

    
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

      <?php $__empty_1 = true; $__currentLoopData = $liveFeed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php
        $isIn  = $trx->status === 'masuk';
        $icoC  = $isIn ? 'rgba(137,233,0,.12)' : 'rgba(58,143,255,.12)';
        $icoS  = $isIn ? 'var(--grn)' : 'var(--blu)';
        $stBg  = $isIn ? 'rgba(137,233,0,.12)' : 'rgba(58,143,255,.12)';
        $stC   = $isIn ? 'var(--grn)' : 'var(--blu)';
        $stLbl = $isIn ? 'Masuk' : 'Keluar';
        $waktu = $isIn
            ? $trx->waktu_masuk->format('H:i')
            : $trx->waktu_keluar?->format('H:i');
      ?>
      <div style="display:flex;align-items:center;gap:10px;padding:9px 20px;border-top:1px solid var(--bdr)">
        
        <div style="width:30px;height:30px;border-radius:8px;background:<?php echo e($icoC); ?>;
             display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <?php if($isIn): ?>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="<?php echo e($icoS); ?>" stroke-width="2.5">
              <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
            </svg>
          <?php else: ?>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="<?php echo e($icoS); ?>" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
            </svg>
          <?php endif; ?>
        </div>

        
        <div style="flex:1;min-width:0">
          <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            <?php echo e($trx->kendaraan->plat_nomor ?? '—'); ?>

          </div>
          <div style="font-size:10px;color:var(--gray2);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            <?php echo e($trx->kendaraan->jenisLabel ?? ''); ?>

            <?php if($trx->kendaraan->merek): ?> · <?php echo e($trx->kendaraan->merek); ?> <?php endif; ?>
            <?php if(!$isIn && $trx->biaya_total > 0): ?> · <?php echo e($trx->biayaRupiah); ?> <?php endif; ?>
          </div>
        </div>

        
        <div style="text-align:right;flex-shrink:0">
          <div style="font-size:10px;color:var(--gray2)"><?php echo e($waktu); ?></div>
          <div style="font-size:9px;font-weight:700;padding:2px 7px;border-radius:5px;
               background:<?php echo e($stBg); ?>;color:<?php echo e($stC); ?>;margin-top:3px">
            <?php echo e($stLbl); ?>

          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div style="padding:30px 20px;text-align:center;color:var(--gray2);font-size:13px">
        Belum ada aktivitas hari ini.
      </div>
      <?php endif; ?>
    </div>

  </div>

  <?php else: ?>
  
  <div class="panel" style="display:flex;align-items:center;justify-content:center;min-height:200px">
    <div style="text-align:center;color:var(--gray2)">
      <div style="font-size:32px;margin-bottom:10px">⚠️</div>
      <div style="font-weight:700;color:var(--ora);margin-bottom:6px">Akun belum punya area tugas</div>
      <div style="font-size:12px">Hubungi admin untuk di-assign ke area parkir.</div>
    </div>
  </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const CARI_URL = "<?php echo e(route('petugas.transaksi.cari-plat')); ?>";
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\SHAFA\park-in\resources\views/petugas/masuk.blade.php ENDPATH**/ ?>