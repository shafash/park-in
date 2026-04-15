<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title>Park In — <?php echo $__env->yieldContent('title','Dashboard'); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div class="wrap">

  
  <aside class="sidebar">
    <div class="logo-area">
      <div class="logo">Park <span class="in">In</span></div>
      <div class="badge <?php echo e(auth()->user()->role); ?>">
        <div class="bdot"></div><?php echo e(ucfirst(auth()->user()->role)); ?>

      </div>
    </div>

    <nav class="nav-wrap">
      <span class="nav-label">Menu<?php echo e(auth()->user()->role === 'admin' ? ' utama' : ''); ?></span>

      <?php if(auth()->user()->role === 'admin'): ?>
        <a href="<?php echo e(route('admin.registrasi.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.registrasi.*') ? 'act-admin' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'user'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Registrasi User
        </a>
        <a href="<?php echo e(route('admin.tarif.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.tarif.*') ? 'act-admin' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'dollar'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Crud Tarif Parkir
        </a>
        <a href="<?php echo e(route('admin.area.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.area.*') ? 'act-admin' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'map'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Crud Area Parkir
        </a>
        <a href="<?php echo e(route('admin.kendaraan.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.kendaraan.*') ? 'act-admin' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'car'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Crud Kendaraan
        </a>
        <a href="<?php echo e(route('admin.log.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.log.*') ? 'act-admin' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'log'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Log Aktivitas
        </a>

      <?php elseif(auth()->user()->role === 'petugas'): ?>
        <a href="<?php echo e(route('petugas.transaksi.index')); ?>" class="nav-item <?php echo e(request()->routeIs('petugas.transaksi.*') ? 'act-petugas' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'trx'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Transaksi Parkir
        </a>
        <a href="<?php echo e(route('petugas.struk.index')); ?>" class="nav-item <?php echo e(request()->routeIs('petugas.struk.*') ? 'act-petugas' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'print'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Cetak Struk
        </a>

      <?php elseif(auth()->user()->role === 'owner'): ?>
        <a href="<?php echo e(route('owner.rekap.index')); ?>" class="nav-item <?php echo e(request()->routeIs('owner.rekap.*') ? 'act-owner' : ''); ?>">
          <?php echo $__env->make('layouts._icon', ['name'=>'chart'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Rekap Transaksi
        </a>
      <?php endif; ?>
    </nav>

    <div class="sb-bot">
      <div class="ucard">
        <div class="av <?php echo e(auth()->user()->role); ?>"><?php echo e(auth()->user()->inisial); ?></div>
        <div>
          <div class="uname"><?php echo e(auth()->user()->nama_lengkap); ?></div>
          <div class="uinfo">
            <?php if(auth()->user()->role === 'petugas'): ?>
            <?php else: ?> <?php echo e(ucfirst(auth()->user()->role)); ?>

            <?php endif; ?>
          </div>
        </div>
      </div>
      <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Logout
        </button>
      </form>
    </div>
  </aside>

  
  <div class="main">
    <div class="topbar">
      <div>
        <div class="pg-title"><?php echo $__env->yieldContent('page-title'); ?></div>
        <div class="pg-sub"><?php echo $__env->yieldContent('page-sub'); ?></div>
      </div>
      <div class="tb-right">
        <?php echo $__env->yieldContent('topbar-right'); ?>
        <div class="clock-box" id="clk"></div>
      </div>
    </div>

    <div class="content">
      <?php if(session('success')): ?>
        <div class="alert a-ok"><?php echo e(session('success')); ?></div>
      <?php endif; ?>
      <?php if(session('error')): ?>
        <div class="alert a-err"><?php echo e(session('error')); ?></div>
      <?php endif; ?>
      <?php if($errors->any()): ?>
        <div class="alert a-err"><?php echo e($errors->first()); ?></div>
      <?php endif; ?>

      <?php echo $__env->yieldContent('content'); ?>
    </div>
  </div>

</div>

<script>
setInterval(() => document.getElementById('clk').textContent = new Date().toLocaleTimeString('id-ID'), 1000);
document.getElementById('clk').textContent = new Date().toLocaleTimeString('id-ID');

// Close modal on overlay click
document.querySelectorAll('.modal-ov').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.add('hide'); }));
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\parkir_laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>