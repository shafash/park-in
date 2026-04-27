<?php $__env->startSection('title','Log Aktivitas'); ?>
<?php $__env->startSection('page-title','Log Aktivitas'); ?>
<?php $__env->startSection('page-sub','Kelola aktivitas pengguna pada sistem'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts._stats_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      <?php echo $__env->make('layouts._icon',['name'=>'log'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Log Aktivitas Sistem
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <form method="GET" style="display:flex">
        <select name="role" onchange="this.form.submit()" style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:8px 14px;color:var(--wht);font-size:13px;outline:none;min-width:130px">
          <option value="">Semua Role</option>
          <option value="admin"   <?php echo e($role==='admin'?'selected':''); ?>>Admin</option>
          <option value="petugas" <?php echo e($role==='petugas'?'selected':''); ?>>Petugas</option>
          <option value="owner"   <?php echo e($role==='owner'?'selected':''); ?>>Owner</option>
        </select>
      </form>
      <a href="<?php echo e(route('admin.log.export', ['role'=>$role])); ?>" class="btn btn-out btn-sm">Export CSV</a>
    </div>
  </div>

  <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <?php
    $dc = match($log->user->role ?? '') { 'admin'=>'var(--blu)', 'petugas'=>'var(--grn)', 'owner'=>'var(--pur)', default=>'var(--gray)' };
    $wkt = $log->waktu_aktivitas->format('H:i') . ' WIB — ' . $log->waktu_aktivitas->format('d M Y');
  ?>
  <div style="display:flex;align-items:flex-start;gap:14px;padding:15px 22px;border-bottom:1px solid var(--bdr)">
    <div style="width:8px;height:8px;border-radius:50%;background:<?php echo e($dc); ?>;flex-shrink:0;margin-top:5px"></div>
    <div>
      <div style="font-size:14px">
        <strong><?php echo e($log->user->nama_lengkap ?? '—'); ?></strong>
        <span class="t-gray"> (<?php echo e(ucfirst($log->user->role ?? '—')); ?>)</span>
        — <?php echo e($log->aktivitas); ?>

      </div>
      <div style="font-size:12px;color:var(--gray2);margin-top:3px"><?php echo e($wkt); ?></div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div style="text-align:center;color:var(--gray);padding:34px">Belum ada aktivitas.</div>
  <?php endif; ?>

  <?php if($logs->hasPages()): ?>
  <div class="pager">
    <span class="pager-info">Hal <?php echo e($logs->currentPage()); ?>/<?php echo e($logs->lastPage()); ?> — <?php echo e($logs->total()); ?> entri</span>
    <div class="pager-btns">
      <?php if($logs->onFirstPage()): ?> <span class="pb dis">&#8249;</span> <?php else: ?> <a href="<?php echo e($logs->previousPageUrl()); ?>" class="pb">&#8249;</a> <?php endif; ?>
      <?php $__currentLoopData = $logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>" class="pb <?php echo e($page === $logs->currentPage() ? 'act' : ''); ?>"><?php echo e($page); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($logs->hasMorePages()): ?> <a href="<?php echo e($logs->nextPageUrl()); ?>" class="pb">&#8250;</a> <?php else: ?> <span class="pb dis">&#8250;</span> <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\SHAFA\park-in\resources\views/admin/log.blade.php ENDPATH**/ ?>