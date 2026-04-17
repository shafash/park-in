<?php $__env->startSection('title','Crud Area Parkir'); ?>
<?php $__env->startSection('page-title','Area Parkir'); ?>
<?php $__env->startSection('page-sub','Kelola akun petugas dan owner'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts._stats_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      <?php echo $__env->make('layouts._icon',['name'=>'map'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Kelola Area Parkir
    </div>
    <button class="btn btn-grn btn-sm" onclick="document.getElementById('m-tambah').classList.remove('hide')">
      + &nbsp;Area Parkir
    </button>
  </div>
  <table class="tbl">
    <thead><tr><th>*</th><th>Nama Area</th><th>Alamat</th><th>Kapasitas</th><th>Terisi</th><th>Okupansi</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
      <td class="t-gray"><?php echo e($i+1); ?></td>
      <td class="fw7"><?php echo e($a->nama_area); ?></td>
      <td class="t-gray" style="font-size:12px"><?php echo e($a->alamat ?: '—'); ?></td>
      <td><?php echo e($a->kapasitas); ?></td>
      <td><?php echo e($a->terisi); ?></td>
      <td class="fw7" style="color:<?php echo e($a->okupansiColor); ?>"><?php echo e($a->okupansi); ?>%</td>
      <td><span class="pill <?php echo e($a->status ? 'p-grn' : 'p-red'); ?>"><?php echo e($a->status ? 'Aktif' : 'Non Aktif'); ?></span></td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit(<?php echo e($a->id_area); ?>,'<?php echo e(addslashes($a->nama_area)); ?>','<?php echo e(addslashes($a->alamat)); ?>',<?php echo e($a->kapasitas); ?>,<?php echo e($a->status); ?>)">Edit</button>
          <form method="POST" action="<?php echo e(route('admin.area.destroy',$a->id_area)); ?>" style="display:inline" onsubmit="return confirm('Hapus area ini?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-red btn-xs">Delete</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="8" style="text-align:center;color:var(--gray);padding:30px">Belum ada area.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="modal-ov hide" id="m-tambah">
  <div class="modal">
    <div class="modal-title">Tambah Area Parkir <button class="modal-close" onclick="document.getElementById('m-tambah').classList.add('hide')">✕</button></div>
    <form method="POST" action="<?php echo e(route('admin.area.store')); ?>"><?php echo csrf_field(); ?>
      <div class="fg"><label>Nama Area</label><input type="text" name="nama_area" placeholder="Contoh: Zona A Lantai 1" required></div>
      <div class="fg"><label>Alamat</label><input type="text" name="alamat" placeholder="Jl. ..."></div>
      <div class="fg"><label>Kapasitas Slot</label><input type="number" name="kapasitas" placeholder="Jumlah slot" min="1" required></div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px"><input type="checkbox" name="status" id="add_stat" checked style="width:auto"><label for="add_stat">Aktif</label></div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-tambah').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>

<div class="modal-ov hide" id="m-edit">
  <div class="modal">
    <div class="modal-title">Edit Area <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="fg"><label>Nama Area</label><input type="text" name="nama_area" id="e_nama" required></div>
      <div class="fg"><label>Alamat</label><input type="text" name="alamat" id="e_alamat"></div>
      <div class="fg"><label>Kapasitas</label><input type="number" name="kapasitas" id="e_kap" min="1" required></div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px"><input type="checkbox" name="status" id="e_stat" style="width:auto"><label for="e_stat">Aktif</label></div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openEdit(id,nama,alamat,kap,stat){
  document.getElementById('e_nama').value=nama; document.getElementById('e_alamat').value=alamat;
  document.getElementById('e_kap').value=kap; document.getElementById('e_stat').checked=stat==1;
  document.getElementById('edit-form').action='/admin/area/'+id;
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\park-in\resources\views/admin/area.blade.php ENDPATH**/ ?>