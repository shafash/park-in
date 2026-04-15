<?php $__env->startSection('title','Rekap Transaksi'); ?>
<?php $__env->startSection('page-title','Rekap Transaksi'); ?>
<?php $__env->startSection('page-sub', $subLabel); ?>

<?php $__env->startSection('topbar-right'); ?>

<div style="display:flex;background:var(--s2);border:1px solid var(--b2);border-radius:9px;overflow:hidden">
  <?php $__currentLoopData = ['harian'=>'Harian','bulanan'=>'Bulanan','tahunan'=>'Tahunan','custom'=>'Custom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('owner.rekap.index',['filter'=>$k])); ?>"
       style="padding:9px 16px;font-size:13px;font-weight:600;color:<?php echo e($filter===$k?'#111':'var(--gray)'); ?>;background:<?php echo e($filter===$k?'var(--pur)':'transparent'); ?>;text-decoration:none;transition:all .15s;white-space:nowrap">
      <?php echo e($v); ?>

    </a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<a href="<?php echo e(request()->fullUrlWithQuery(['export'=>1])); ?>" class="btn btn-out btn-sm">&#8595; Export</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<?php if($filter === 'custom'): ?>
<div class="panel" style="margin-bottom:20px">
  <form method="GET" action="<?php echo e(route('owner.rekap.index')); ?>"
        style="padding:14px 22px;display:flex;gap:12px;align-items:center;flex-wrap:wrap">
    <input type="hidden" name="filter" value="custom">
    <span class="t-gray" style="font-size:13px">Dari:</span>
    <input type="date" name="dari" value="<?php echo e($df->format('Y-m-d')); ?>"
           style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 12px;color:var(--wht);font-size:13px;outline:none">
    <span class="t-gray" style="font-size:13px">Sampai:</span>
    <input type="date" name="sampai" value="<?php echo e($dt->format('Y-m-d')); ?>"
           style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 12px;color:var(--wht);font-size:13px;outline:none">
    <button type="submit" class="btn btn-grn btn-sm">Tampilkan</button>
  </form>
</div>
<?php endif; ?>


<div class="stats">
  <div class="sc" style="--acc:var(--grn)">
    <div class="sc-lbl">Total Pendapatan</div>
    <div class="sc-val" style="font-size:22px">Rp. <?php echo e(number_format($totalRev,0,',','.')); ?></div>
    <div class="sc-sub">Terdaftar di sistem</div>
  </div>
  <div class="sc" style="--acc:var(--pur)">
    <div class="sc-lbl">Total Kendaraan</div>
    <div class="sc-val"><?php echo e($totalKend); ?></div>
    <div class="sc-sub">Lokasi parkir</div>
  </div>
  <div class="sc" style="--acc:var(--ora)">
    <div class="sc-lbl">Rata-Rata / Transaksi</div>
    <div class="sc-val" style="font-size:22px">Rp. <?php echo e(number_format($avgBiaya,0,',','.')); ?></div>
    <div class="sc-sub">Tipe kendaraan</div>
  </div>
  <div class="sc" style="--acc:var(--blu)">
    <div class="sc-lbl">Lokasi Teratas</div>
    <div class="sc-val" style="font-size:16px;color:var(--blu)"><?php echo e($topArea); ?></div>
    <div class="sc-sub">Aktivitas tercatat</div>
  </div>
</div>


<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--pur)">
      <?php echo $__env->make('layouts._icon',['name'=>'chart'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Data Rekap Transaksi
    </div>
  </div>

  
  <form method="GET" action="<?php echo e(route('owner.rekap.index')); ?>"
        style="padding:14px 22px;display:flex;gap:14px;flex-wrap:wrap;border-bottom:1px solid var(--bdr);align-items:flex-end">
    <input type="hidden" name="filter"  value="<?php echo e($filter); ?>">
    <input type="hidden" name="dari"    value="<?php echo e($df->format('Y-m-d')); ?>">
    <input type="hidden" name="sampai"  value="<?php echo e($dt->format('Y-m-d')); ?>">

    <div style="display:flex;flex-direction:column;gap:5px">
      <span style="font-size:11px;color:var(--gray)">Area</span>
      <select name="area" onchange="this.form.submit()"
              style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 13px;color:var(--wht);font-size:13px;outline:none;min-width:160px">
        <option value="0">Semua Area</option>
        <?php $__currentLoopData = $areaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($a->id_area); ?>" <?php echo e($fa==$a->id_area?'selected':''); ?>><?php echo e($a->nama_area); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:5px">
      <span style="font-size:11px;color:var(--gray)">Jenis Kendaraan</span>
      <select name="jenis" onchange="this.form.submit()"
              style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 13px;color:var(--wht);font-size:13px;outline:none;min-width:150px">
        <option value="">Semua Jenis</option>
        <option value="motor"   <?php echo e($fj==='motor'?'selected':''); ?>>Motor</option>
        <option value="mobil"   <?php echo e($fj==='mobil'?'selected':''); ?>>Mobil</option>
        <option value="lainnya" <?php echo e($fj==='lainnya'?'selected':''); ?>>Lainnya</option>
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:5px">
      <span style="font-size:11px;color:var(--gray)">Urutkan</span>
      <select name="sort" onchange="this.form.submit()"
              style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:9px 13px;color:var(--wht);font-size:13px;outline:none;min-width:130px">
        <option value="waktu_masuk" <?php echo e($fsort==='waktu_masuk'?'selected':''); ?>>Terbaru</option>
        <option value="biaya_total" <?php echo e($fsort==='biaya_total'?'selected':''); ?>>Total Biaya</option>
      </select>
    </div>

    <div style="display:flex;align-items:flex-end">
      <a href="<?php echo e(route('owner.rekap.index',['filter'=>$filter])); ?>" class="btn btn-out btn-sm">Reset Filter</a>
    </div>
  </form>

  
  <div class="chart-area">
    <div class="chart-lbl">Grafik Pendapatan Harian</div>
    <div class="bars">
      <?php $__currentLoopData = $chart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php $pct = $chartMax > 0 ? round($c['val']/$chartMax*100) : 2; ?>
      <div class="bc"><div class="bf" style="height:<?php echo e($pct); ?>%"></div></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div style="display:flex">
      <?php $__currentLoopData = $chart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bd" style="flex:1"><?php echo e($c['day']); ?></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  
  <?php $AC = ['var(--grn)','var(--pur)','var(--blu)','var(--ora)']; ?>
  <div class="area-cards">
    <?php $__empty_1 = true; $__currentLoopData = $perArea->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="ac">
      <div class="ac-name"><?php echo e(strtoupper($pa->area->nama_area ?? '—')); ?></div>
      <div class="ac-rev" style="color:<?php echo e($AC[$i] ?? 'var(--grn)'); ?>">
        Rp. <?php echo e(number_format($pa->tot, 0, ',', '.')); ?>

      </div>
      <div class="ac-cnt"><?php echo e($pa->jml); ?> kendaraan</div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="ac"><div class="ac-name" style="color:var(--gray)">Tidak ada data</div></div>
    <?php endif; ?>
  </div>

  
  <table class="tbl">
    <thead>
      <tr>
        <th>Id Transaksi</th><th>Tanggal</th><th>Plat Nomor</th>
        <th>Jenis</th><th>Area</th><th>Masuk</th><th>Keluar</th>
        <th>Durasi</th><th>Total</th>
      </tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $rekap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
      $jc = match($t->kendaraan->jenis_kendaraan??'') { 'motor'=>'p-grn','mobil'=>'p-grn','lainnya'=>'p-ora', default=>'p-grn' };
      $jl = match($t->kendaraan->jenis_kendaraan??'') { 'lainnya'=>'Truk', default=>ucfirst($t->kendaraan->jenis_kendaraan??'') };
    ?>
    <tr>
      <td class="t-gray" style="font-size:12px">TRX - <?php echo e(str_pad($t->id_parkir,4,'0',STR_PAD_LEFT)); ?></td>
      <td style="font-size:12px"><?php echo e($t->waktu_masuk->format('d M Y')); ?></td>
      <td class="fw7"><?php echo e($t->kendaraan->plat_nomor ?? '—'); ?></td>
      <td><span class="pill <?php echo e($jc); ?>"><?php echo e($jl); ?></span></td>
      <td class="t-gray" style="font-size:12px"><?php echo e($t->area->nama_area ?? '—'); ?></td>
      <td style="font-size:12px"><?php echo e($t->waktu_masuk->format('H:i')); ?></td>
      <td style="font-size:12px"><?php echo e($t->waktu_keluar ? $t->waktu_keluar->format('H:i') : '—'); ?></td>
      <td style="font-size:12px"><?php echo e($t->durasiLabel); ?></td>
      <td class="fw7 t-grn"><?php echo e($t->biayaRupiah); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="9" style="text-align:center;color:var(--gray);padding:30px">Tidak ada data untuk periode ini.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  
  <div class="pager">
    <span class="pager-info">Menampilkan <?php echo e($rekap->firstItem() ?? 0); ?> - <?php echo e($rekap->lastItem() ?? 0); ?> dari <?php echo e($rekap->total()); ?> kendaraan</span>
    <div class="pager-btns">
      <?php if($rekap->onFirstPage()): ?> <span class="pb dis">&#8249;</span> <?php else: ?> <a href="<?php echo e($rekap->previousPageUrl()); ?>" class="pb">&#8249;</a> <?php endif; ?>
      <?php $__currentLoopData = $rekap->getUrlRange(max(1,$rekap->currentPage()-2), min($rekap->lastPage(),$rekap->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>" class="pb <?php echo e($page === $rekap->currentPage() ? 'act' : ''); ?>"><?php echo e($page); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($rekap->hasMorePages()): ?> <a href="<?php echo e($rekap->nextPageUrl()); ?>" class="pb">&#8250;</a> <?php else: ?> <span class="pb dis">&#8250;</span> <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\parkir_laravel\resources\views/owner/rekap.blade.php ENDPATH**/ ?>