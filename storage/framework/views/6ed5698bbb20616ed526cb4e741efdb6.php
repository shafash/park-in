<?php $__env->startSection('title','Cetak Struk'); ?>
<?php $__env->startSection('page-title','Cetak Struk Parkir'); ?>
<?php $__env->startSection('page-sub','Cari transaksi dan cetak struk'); ?>

<?php $__env->startSection('topbar-right'); ?>
<a href="<?php echo e(route('petugas.transaksi.masuk')); ?>" class="btn btn-grn">+ &nbsp;Catat Masuk</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="stats">
  <div class="sc" style="--acc:var(--grn)"><div class="sc-lbl">Masuk Hari Ini</div><div class="sc-val"><?php echo e($masuk); ?></div><div class="sc-sub">Total kendaraan masuk</div></div>
  <div class="sc" style="--acc:var(--blu)"><div class="sc-lbl">Keluar Hari Ini</div><div class="sc-val"><?php echo e($keluar); ?></div><div class="sc-sub">Total kendaraan keluar</div></div>
  <div class="sc" style="--acc:var(--ora)"><div class="sc-lbl">Di Area Sekarang</div><div class="sc-val"><?php echo e($diarea); ?></div><div class="sc-sub">Masih di parkiran</div></div>
  <div class="sc" style="--acc:var(--red)"><div class="sc-lbl">Struk Dicetak</div><div class="sc-val"><?php echo e($struk); ?></div><div class="sc-sub">Hari ini</div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 310px;gap:20px">
  
  <div class="panel">
    <div class="ph">
      <div class="pt" style="color:var(--grn)">
        <?php echo $__env->make('layouts._icon',['name'=>'print'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Cari Transaksi untuk Cetak
      </div>
    </div>
    <form method="GET" action="<?php echo e(route('petugas.struk.index')); ?>" style="padding:16px 22px;display:flex;gap:8px">
      <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Masukkan ID transaksi atau plat nomor..."
        style="flex:1;background:var(--s2);border:1px solid var(--b2);border-radius:9px;padding:11px 14px;color:var(--wht);font-size:13px;outline:none">
      <button type="submit" class="btn btn-grn">Cari</button>
    </form>

    <table class="tbl">
      <thead><tr><th>ID</th><th>Plat</th><th>Masuk</th><th>Keluar</th><th>Durasi</th><th>Total</th></tr></thead>
      <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php $sel = isset($trx) && $trx->id_parkir === $r->id_parkir; ?>
      <tr style="<?php echo e($sel ? 'background:rgba(137,233,0,.07)' : ''); ?>;cursor:pointer"
          onclick="window.location='<?php echo e(route('petugas.struk.show', $r->id_parkir)); ?>'">
        <td class="t-gray" style="font-size:12px"><?php echo e($r->tid); ?></td>
        <td class="fw7"><?php echo e($r->kendaraan->plat_nomor ?? '—'); ?></td>
        <td style="font-size:13px"><?php echo e($r->waktu_masuk->format('H:i')); ?> WIB</td>
        <td style="font-size:13px"><?php echo e($r->waktu_keluar ? $r->waktu_keluar->format('H:i').' WIB' : '—'); ?></td>
        <td style="font-size:13px"><?php echo e($r->durasiLabel); ?></td>
        <td class="fw7 t-grn"><?php echo e($r->biayaRupiah); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:26px">Tidak ada transaksi selesai.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div>
    <div class="panel" style="margin-bottom:12px">
      <div class="ph"><div class="pt">Preview Struk</div></div>
      <?php if(isset($trx) && $trx): ?>
      <div class="pb-body">
        <div class="struk-wrap">
          <div class="struk-title">Park In<br><span style="font-size:10px;font-weight:400">Struk Parkir Resmi</span></div>
          <div class="struk-row"><span>No.</span><span><?php echo e($trx->tid); ?></span></div>
          <div class="struk-row"><span>Tgl</span><span><?php echo e($trx->waktu_masuk->format('d/m/Y')); ?></span></div>
          <div style="border-top:1px dashed #bbb;margin:6px 0"></div>
          <div class="struk-row"><span>Plat</span><span style="font-weight:700"><?php echo e($trx->kendaraan->plat_nomor); ?></span></div>
          <div class="struk-row"><span>Jenis</span><span><?php echo e(ucfirst($trx->kendaraan->jenis_kendaraan)); ?></span></div>
          <div class="struk-row"><span>Pemilik</span><span><?php echo e($trx->kendaraan->pemilik ?: '-'); ?></span></div>
          <div style="border-top:1px dashed #bbb;margin:6px 0"></div>
          <div class="struk-row"><span>Area</span><span><?php echo e($trx->area->nama_area); ?></span></div>
          <div class="struk-row"><span>Masuk</span><span><?php echo e($trx->waktu_masuk->format('H:i')); ?></span></div>
          <div class="struk-row"><span>Keluar</span><span><?php echo e($trx->waktu_keluar->format('H:i')); ?></span></div>
          <div class="struk-row"><span>Durasi</span><span><?php echo e($trx->durasi_jam); ?> jam</span></div>
          <div class="struk-row"><span>Tarif/jam</span><span><?php echo e($trx->tarif->rupiah); ?></span></div>
          <div class="struk-total"><span>TOTAL</span><span><?php echo e($trx->biayaRupiah); ?></span></div>
          <div class="struk-foot">
            Petugas: <?php echo e($trx->user->nama_lengkap); ?><br>
            Cetak: <?php echo e(now()->format('d/m/Y H:i:s')); ?><br><br>
            Terima kasih — Park In
          </div>
        </div>
      </div>
      <?php else: ?>
      <div style="padding:42px 22px;text-align:center;color:var(--gray);font-size:13px">
        Pilih transaksi untuk<br>melihat preview struk
      </div>
      <?php endif; ?>
    </div>

    <div style="display:flex;gap:8px">
      <?php if(isset($trx) && $trx): ?>
        <a href="<?php echo e(route('petugas.struk.print', $trx->id_parkir)); ?>" target="_blank"
           class="btn btn-grn" style="flex:1;justify-content:center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"/>
            <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
          </svg>
          Cetak Struk
        </a>
        <button class="btn btn-out">PDF</button>
      <?php else: ?>
        <button class="btn btn-grn" style="flex:1;justify-content:center" disabled>Cetak Struk</button>
        <button class="btn btn-out" disabled>PDF</button>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\parkir_laravel\resources\views/petugas/struk.blade.php ENDPATH**/ ?>