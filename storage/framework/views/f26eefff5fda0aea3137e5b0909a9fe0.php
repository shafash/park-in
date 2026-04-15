<?php $__env->startSection('title','Catat Masuk'); ?>
<?php $__env->startSection('page-title','Catat Kendaraan Masuk'); ?>
<?php $__env->startSection('page-sub','Daftarkan kendaraan masuk area parkir'); ?>

<?php $__env->startSection('topbar-right'); ?>
<a href="<?php echo e(route('petugas.transaksi.index')); ?>" class="btn btn-out btn-sm">← Transaksi</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="stats">
  <div class="sc" style="--acc:var(--grn)"><div class="sc-lbl">Masuk Hari Ini</div><div class="sc-val"><?php echo e($masuk); ?></div><div class="sc-sub">Total kendaraan masuk</div></div>
  <div class="sc" style="--acc:var(--blu)"><div class="sc-lbl">Keluar Hari Ini</div><div class="sc-val"><?php echo e($keluar); ?></div><div class="sc-sub">Total kendaraan keluar</div></div>
  <div class="sc" style="--acc:var(--ora)"><div class="sc-lbl">Di Area Sekarang</div><div class="sc-val"><?php echo e($diarea); ?></div><div class="sc-sub">Masih di parkiran</div></div>
  <div class="sc" style="--acc:var(--red)"><div class="sc-lbl">Struk Dicetak</div><div class="sc-val"><?php echo e($struk); ?></div><div class="sc-sub">Hari ini</div></div>
</div>

<div class="two-col">
  
  <div class="panel">
    <div class="ph"><div class="pt">Form Kendaraan Masuk</div></div>
    <div class="pb-body">
      <form method="POST" action="<?php echo e(route('petugas.transaksi.masuk.store')); ?>" id="form-masuk">
        <?php echo csrf_field(); ?>

        
        <div class="fg" style="position:relative">
          <label>Plat Nomor <span style="color:var(--gray2);font-size:11px">(ketik untuk cari)</span></label>
          <input type="text" name="plat_nomor" id="inp_plat"
                 value="<?php echo e(old('plat_nomor')); ?>"
                 placeholder="Ketik plat, contoh: B 1234 ABC"
                 required autocomplete="off"
                 style="text-transform:uppercase;font-size:16px;font-weight:700;letter-spacing:2px">

          
          <div id="plat_dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:99;
               background:var(--surf);border:1px solid var(--b2);border-radius:0 0 10px 10px;
               max-height:240px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.5)">
          </div>
        </div>

        
        <div id="info_kendaraan" style="display:none;background:var(--s2);border-radius:10px;padding:14px;margin-bottom:14px;border:1px solid var(--grn3)">
          <div style="font-size:11px;color:var(--grn);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">
            ✓ Data kendaraan ditemukan
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div>
              <div style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Merek / Model</div>
              <div id="info_merek" class="fw7" style="margin-top:2px">—</div>
            </div>
            <div>
              <div style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Jenis</div>
              <div id="info_jenis" class="fw7" style="margin-top:2px">—</div>
            </div>
            <div>
              <div style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Warna</div>
              <div id="info_warna" style="margin-top:2px">—</div>
            </div>
            <div>
              <div style="font-size:10px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Pemilik</div>
              <div id="info_pemilik" style="margin-top:2px">—</div>
            </div>
          </div>
          <div id="foto_kend_wrap" style="margin-top:10px;display:none">
            <img id="foto_kend" src="" alt="Foto kendaraan"
                 style="height:70px;border-radius:8px;border:1px solid var(--b2);object-fit:cover">
          </div>
        </div>

        
        <div class="fg">
          <label>Jenis / Tarif Kendaraan</label>
          <select name="id_tarif" id="sel_tarif" required>
            <option value="">-- Pilih Jenis --</option>
            <?php $__currentLoopData = $tarifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($t->id_tarif); ?>"
                      data-jenis="<?php echo e($t->jenis_kendaraan); ?>"
                      <?php echo e(old('id_tarif')==$t->id_tarif?'selected':''); ?>>
                <?php echo e($t->jenis_kendaraan === 'lainnya' ? 'Truk' : ucfirst($t->jenis_kendaraan)); ?> — <?php echo e($t->rupiah); ?>/jam
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="fg">
          <label>Area Parkir</label>
          <select name="id_area" required>
            <option value="">-- Pilih Area --</option>
            <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($a->id_area); ?>" <?php echo e(old('id_area')==$a->id_area?'selected':''); ?>>
                <?php echo e($a->nama_area); ?> (sisa <?php echo e($a->sisa); ?> slot)
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div class="fg">
          <label>Waktu Masuk</label>
          <input type="text" id="waktu_masuk_display" value="<?php echo e(now()->format('d/m/Y H:i:s')); ?>"
                 readonly style="color:var(--gray)">
        </div>

        <button type="submit" class="btn btn-grn"
                style="width:100%;justify-content:center;padding:13px;font-size:14px">
          Catat Kendaraan Masuk
        </button>
      </form>
    </div>
  </div>

  
  <div class="panel">
    <div class="ph"><div class="pt">Status Area Parkir</div></div>
    <?php $__currentLoopData = $allAreas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $pct=$a->okupansi; $col=$a->okupansiColor; ?>
    <div style="padding:14px 22px;border-bottom:1px solid var(--bdr)">
      <div style="display:flex;justify-content:space-between;margin-bottom:8px">
        <span class="fw7"><?php echo e($a->nama_area); ?></span>
        <span style="font-weight:700;color:<?php echo e($col); ?>"><?php echo e($pct); ?>%</span>
      </div>
      <div style="height:6px;background:var(--s3);border-radius:3px;overflow:hidden">
        <div style="height:6px;width:<?php echo e($pct); ?>%;background:<?php echo e($col); ?>;border-radius:3px"></div>
      </div>
      <div style="font-size:11px;color:var(--gray2);margin-top:4px"><?php echo e($a->terisi); ?>/<?php echo e($a->kapasitas); ?> terisi</div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const CARI_URL   = "<?php echo e(route('petugas.transaksi.cari-plat')); ?>";
const CSRF_TOKEN = "<?php echo e(csrf_token()); ?>";

const inpPlat    = document.getElementById('inp_plat');
const dropdown   = document.getElementById('plat_dropdown');
const selTarif   = document.getElementById('sel_tarif');
const infoBox    = document.getElementById('info_kendaraan');

// Map jenis → id_tarif
const tarifMap = {};
selTarif.querySelectorAll('option[data-jenis]').forEach(opt => {
  tarifMap[opt.dataset.jenis] = opt.value;
});

let debounceTimer;

inpPlat.addEventListener('input', function () {
  const q = this.value.trim();
  clearTimeout(debounceTimer);

  if (q.length < 2) {
    dropdown.style.display = 'none';
    hideInfo();
    return;
  }

  debounceTimer = setTimeout(() => {
    fetch(`${CARI_URL}?q=${encodeURIComponent(q)}`, {
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => renderDropdown(data))
    .catch(() => { dropdown.style.display = 'none'; });
  }, 280);
});

function renderDropdown(data) {
  if (!data.length) {
    dropdown.innerHTML = `<div style="padding:12px 16px;font-size:12px;color:var(--gray)">Plat tidak ditemukan di database — akan otomatis didaftarkan saat disimpan.</div>`;
    dropdown.style.display = 'block';
    return;
  }

  dropdown.innerHTML = data.map(k => `
    <div class="plat-item" data-id="${k.id_kendaraan}"
         data-plat="${k.plat_nomor}" data-jenis="${k.jenis_kendaraan}"
         data-merek="${k.merek || ''}" data-warna="${k.warna || ''}"
         data-pemilik="${k.pemilik || ''}" data-foto="${k.foto_url || ''}"
         style="padding:10px 16px;cursor:pointer;border-bottom:1px solid var(--bdr);
                display:flex;align-items:center;gap:12px;transition:background .1s">
      <div style="flex:1">
        <div class="fw7" style="font-size:14px;letter-spacing:1px">${k.plat_nomor}</div>
        <div style="font-size:11px;color:var(--gray);margin-top:2px">
          ${k.jenis_label} ${k.merek ? '· ' + k.merek : ''} ${k.pemilik ? '· ' + k.pemilik : ''}
        </div>
      </div>
      ${k.foto_url ? `<img src="${k.foto_url}" style="width:44px;height:34px;object-fit:cover;border-radius:6px;border:1px solid var(--b2)">` : ''}
    </div>
  `).join('');

  dropdown.querySelectorAll('.plat-item').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = 'var(--s2)');
    el.addEventListener('mouseleave', () => el.style.background = '');
    el.addEventListener('click', () => pilihKendaraan(el));
  });

  dropdown.style.display = 'block';
}

function pilihKendaraan(el) {
  inpPlat.value = el.dataset.plat;
  dropdown.style.display = 'none';

  // Auto-set tarif
  const jenis = el.dataset.jenis;
  if (tarifMap[jenis]) selTarif.value = tarifMap[jenis];

  // Tampilkan info kendaraan
  document.getElementById('info_merek').textContent   = el.dataset.merek   || '—';
  document.getElementById('info_jenis').textContent   = jenis === 'lainnya' ? 'Truk' : (jenis.charAt(0).toUpperCase() + jenis.slice(1));
  document.getElementById('info_warna').textContent   = el.dataset.warna   || '—';
  document.getElementById('info_pemilik').textContent = el.dataset.pemilik || '—';

  const fotoWrap = document.getElementById('foto_kend_wrap');
  const fotoImg  = document.getElementById('foto_kend');
  if (el.dataset.foto && !el.dataset.foto.includes('img/')) {
    fotoImg.src = el.dataset.foto;
    fotoWrap.style.display = 'block';
  } else {
    fotoWrap.style.display = 'none';
  }

  infoBox.style.display = 'block';
}

function hideInfo() {
  infoBox.style.display = 'none';
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function (e) {
  if (!inpPlat.contains(e.target) && !dropdown.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});

// Update jam realtime
setInterval(() => {
  const now = new Date();
  document.getElementById('waktu_masuk_display').value =
    now.toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'}) + ' ' +
    now.toLocaleTimeString('id-ID');
}, 1000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\parkir_laravel\resources\views/petugas/masuk.blade.php ENDPATH**/ ?>