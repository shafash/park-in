<?php $__env->startSection('title','Proses Keluar'); ?>
<?php $__env->startSection('page-title','Proses Kendaraan Keluar'); ?>
<?php $__env->startSection('page-sub','Hitung biaya dan catat waktu keluar'); ?>

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
  <div class="ph"><div class="pt">Detail Kendaraan</div></div>
  <div class="pb-body">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Plat Nomor</div>
        <div style="font-size:26px;font-weight:800;letter-spacing:3px;margin-top:6px"><?php echo e($trx->kendaraan->plat_nomor); ?></div>
      </div>
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Area Parkir</div>
        <div style="font-size:16px;font-weight:700;margin-top:6px"><?php echo e($trx->area->nama_area); ?></div>
      </div>
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Waktu Masuk</div>
        <div style="font-size:18px;font-weight:700;font-family:monospace;margin-top:6px"><?php echo e($trx->waktu_masuk->format('H:i')); ?> WIB</div>
      </div>
      <div style="background:var(--s2);border-radius:10px;padding:16px">
        <div style="font-size:11px;color:var(--gray2);text-transform:uppercase;letter-spacing:1px">Estimasi Biaya</div>
        <div style="font-size:24px;font-weight:800;color:var(--grn);margin-top:6px">
          Rp. <?php echo e(number_format($estBiaya, 0, ',', '.')); ?>

        </div>
        <div style="font-size:11px;color:var(--gray2)"><?php echo e($durEst); ?>j × Rp. <?php echo e(number_format($trx->tarif->tarif_per_jam, 0, ',', '.')); ?>/j</div>
      </div>
    </div>

    <form method="POST" action="<?php echo e(route('petugas.transaksi.keluar.store', $trx->id_parkir)); ?>">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-grn" style="width:100%;justify-content:center;padding:13px;font-size:14px"
        onclick="return confirm('Proses kendaraan keluar sekarang?')">
        Proses Keluar &amp; Hitung Biaya Final
      </button>
    </form>
  </div>
</div>

<div style="margin-top:12px">
  <a href="<?php echo e(route('petugas.transaksi.index')); ?>" class="btn btn-out">← Kembali ke Transaksi</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\park-in\resources\views/petugas/keluar.blade.php ENDPATH**/ ?>