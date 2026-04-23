<?php $__env->startSection('title','Transaksi Parkir'); ?>
<?php $__env->startSection('page-title','Transaksi Parkir'); ?>
<?php $__env->startSection('page-sub','Data masuk & keluar kendaraan hari ini'); ?>

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

<div class="panel">
  <div class="ph" style="gap:8px">
    <div class="pt" style="color:var(--grn)">
      <?php echo $__env->make('layouts._icon',['name'=>'trx'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Data Transaksi
    </div>
    <form method="GET" class="sbar" style="flex:1;justify-content:flex-end">
      <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari Plat Nomor...." style="width:180px">
      <select name="status" onchange="this.form.submit()" style="min-width:140px">
        <option value="">Semua Status</option>
        <option value="masuk"  <?php echo e($status==='masuk'?'selected':''); ?>>Masuk</option>
        <option value="keluar" <?php echo e($status==='keluar'?'selected':''); ?>>Keluar</option>
      </select>
      <select name="jenis" onchange="this.form.submit()" style="min-width:130px">
        <option value="">Semua Jenis</option>
        <?php if(isset($jenisList) && count($jenisList)): ?>
          <?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($j); ?>" <?php echo e($jenis === $j ? 'selected' : ''); ?>><?php echo e(ucfirst($j)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      </select>
      <select name="sort" onchange="this.form.submit()" style="min-width:120px">
        <option value="waktu_masuk"  <?php echo e($sort==='waktu_masuk'?'selected':''); ?>>Terbaru</option>
        <option value="biaya_total"  <?php echo e($sort==='biaya_total'?'selected':''); ?>>Total</option>
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
    <?php $__empty_1 = true; $__currentLoopData = $transaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
      $kj = $t->kendaraan->jenis_kendaraan ?? '';
      $jc = ($jenisColors[$kj] ?? 'p-blu');
      $jl = $kj ? ucfirst($kj) : '—';
      $sc = $t->status === 'masuk' ? 'p-grn' : 'p-blu';
    ?>
    <tr>
      <td class="t-gray" style="font-size:12px"><?php echo e($t->tid); ?></td>
      <td class="fw7"><?php echo e($t->kendaraan->plat_nomor ?? '—'); ?></td>
      <td><span class="pill <?php echo e($jc); ?>"><?php echo e($jl); ?></span></td>
      <td style="font-size:13px"><?php echo e($t->waktu_masuk->format('H:i')); ?> WIB</td>
      <td style="font-size:13px;color:var(--gray)"><?php echo e($t->waktu_keluar ? $t->waktu_keluar->format('H:i').' WIB' : '—'); ?></td>
      <td style="font-size:13px"><?php echo e($t->durasiLabel); ?></td>
      <td class="fw7 t-grn"><?php echo e($t->biaya_total > 0 ? $t->biayaRupiah : '—'); ?></td>
      <td><span class="pill <?php echo e($sc); ?>"><span class="p-dot"></span> <?php echo e(ucfirst($t->status)); ?></span></td>
      <td>
        <div class="tbl-acts">
          <?php if($t->status === 'masuk'): ?>
            <button type="button"
                  data-modal="keluar"
                  data-id="<?php echo e($t->id_parkir); ?>"
                  data-plat="<?php echo e($t->kendaraan->plat_nomor ?? '—'); ?>"
                  data-jenis="<?php echo e($t->kendaraan->jenisLabel ?? '—'); ?>"
                  data-jenis-pill="<?php echo e($t->kendaraan->jenisPill ?? 'p-grn'); ?>"
                  data-masuk="<?php echo e($t->waktu_masuk->format('H:i')); ?> WIB"
                  data-durasi="<?php echo e($t->durasiLabel); ?>"
                  data-est="<?php echo e($t->biaya_total > 0 ? $t->biayaRupiah : 'Dihitung saat keluar'); ?>"
                  class="btn btn-red btn-xs">
            Keluar
          </button>
          <?php else: ?>
            <a href="<?php echo e(route('petugas.struk.show', $t->id_parkir)); ?>" class="btn btn-blu btn-xs">Struk</a>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="9" style="text-align:center;color:var(--gray);padding:30px">Tidak ada transaksi.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <div class="pager">
    <span class="pager-info">Menampilkan <?php echo e($transaksis->firstItem() ?? 0); ?> - <?php echo e($transaksis->lastItem() ?? 0); ?> dari <?php echo e($transaksis->total()); ?> transaksi</span>
    <div class="pager-btns">
      <?php if($transaksis->onFirstPage()): ?> <span class="pb dis">&#8249;</span> <?php else: ?> <a href="<?php echo e($transaksis->previousPageUrl()); ?>" class="pb">&#8249;</a> <?php endif; ?>
      <?php $__currentLoopData = $transaksis->getUrlRange(max(1,$transaksis->currentPage()-2), min($transaksis->lastPage(),$transaksis->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>" class="pb <?php echo e($page === $transaksis->currentPage() ? 'act' : ''); ?>"><?php echo e($page); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($transaksis->hasMorePages()): ?> <a href="<?php echo e($transaksis->nextPageUrl()); ?>" class="pb">&#8250;</a> <?php else: ?> <span class="pb dis">&#8250;</span> <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\park-in\resources\views/petugas/transaksi.blade.php ENDPATH**/ ?>