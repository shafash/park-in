
<?php $__env->startSection('title','Crud Tarif Parkir'); ?>
<?php $__env->startSection('page-title','Tarif Parkir'); ?>
<?php $__env->startSection('page-sub','Kelola tarif parkir'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts._stats_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      <?php echo $__env->make('layouts._icon',['name'=>'dollar'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Kelola Tarif Parkir
    </div>
    <button class="btn btn-grn btn-sm" onclick="document.getElementById('m-tambah').classList.remove('hide')">
      + &nbsp;Tarif Parkir
    </button>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>*</th><th>Jenis Kendaraan</th><th>Tarif Awal</th><th>Tarif / Jam</th><th>Tarif maks / Hari</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $tarifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
      <td class="t-gray"><?php echo e($i+1); ?></td>
      <td class="fw7"><?php echo e(ucwords($t->jenis_kendaraan)); ?></td>
      <td class="t-gray">Rp. <?php echo e(number_format($t->tarif_awal ?? 0,0,',','.')); ?></td>
      <td class="t-grn fw7"><?php echo e($t->rupiah); ?></td>
      <td class="t-gray">Rp. <?php echo e(number_format($t->tarif_maks_per_hari ?? 0,0,',','.')); ?></td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit(<?php echo e($t->id_tarif); ?>,'<?php echo e($t->jenis_kendaraan); ?>',<?php echo e($t->tarif_awal ?? 0); ?>,<?php echo e($t->tarif_per_jam); ?>,<?php echo e($t->tarif_maks_per_hari ?? 0); ?>)">Edit</button>
          <form id="form-hapus-tarif-<?php echo e($t->id_tarif); ?>" method="POST"
                action="<?php echo e(route('admin.tarif.destroy', $t->id_tarif)); ?>"
                style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          </form>

          <button type="button"
                  data-modal="hapus"
                  data-form-id="form-hapus-tarif-<?php echo e($t->id_tarif); ?>"
                  data-label="Tarif"
                  data-nama="<?php echo e(ucfirst($t->jenis_kendaraan)); ?> — <?php echo e($t->rupiah); ?>/jam"
                  data-warn="Hapus hanya jika tarif belum dipakai dalam transaksi."
                  class="btn btn-red btn-xs">
            Delete
          </button>
        </div>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:30px">Belum ada tarif.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="modal-ov hide" id="m-tambah">
  <div class="modal">
    <div class="modal-title">Tambah Tarif <button class="modal-close" onclick="document.getElementById('m-tambah').classList.add('hide')">✕</button></div>
    <form method="POST" action="<?php echo e(route('admin.tarif.store')); ?>">
      <?php echo csrf_field(); ?>
      <div class="fg"><label>Jenis Kendaraan</label><input type="text" name="jenis_kendaraan" placeholder="Contoh: Mobil" required></div>
      <div class="fg"><label>Tarif Awal (Rp)</label><input type="number" name="tarif_awal" placeholder="Contoh: 2000" min="0" required></div>
      <div class="fg"><label>Tarif per Jam (Rp)</label><input type="number" name="tarif_per_jam" placeholder="Contoh: 3000" min="100" required></div>
      <div class="fg"><label>Tarif Maks / Hari</label><input type="number" name="tarif_maks_per_hari" placeholder="Contoh: 50000" min="0" required></div>
      <div class="modal-foot">
        <button type="button" class="btn btn-out"
          onclick="document.getElementById('m-edit').classList.add('hide')">
          Batal
        </button>
        <button type="submit" class="btn btn-grn">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-ov hide" id="m-edit">
  <div class="modal">
    <div class="modal-title">Edit Tarif <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="fg"><label>Jenis Kendaraan</label><input type="text" name="jenis_kendaraan" id="e_jenis" required></div>
      <div class="fg"><label>Tarif Awal (Rp)</label><input type="number" name="tarif_awal" id="e_awal" min="0" required></div>
      <div class="fg"><label>Tarif per Jam (Rp)</label><input type="number" name="tarif_per_jam" id="e_tarif" min="100" required></div>
      <div class="fg"><label>Tarif Maks / Hari</label><input type="number" name="tarif_maks_per_hari" id="e_maks" min="0" required></div>
      <div class="modal-foot">
        <button type="button" class="btn btn-out btn-xs" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button>
        <button type="submit" class="btn btn-grn btn-xs">Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openEdit(id,j,awal,t,maks){
  document.getElementById('e_jenis').value=j;
  document.getElementById('e_awal').value=awal;
  document.getElementById('e_tarif').value=t;
  document.getElementById('e_maks').value=maks;
  document.getElementById('edit-form').action='/admin/tarif/'+id;
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\park-in\resources\views/admin/tarif.blade.php ENDPATH**/ ?>