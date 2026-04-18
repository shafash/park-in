<?php $__env->startSection('title','Registrasi User'); ?>
<?php $__env->startSection('page-title','Registrasi User'); ?>
<?php $__env->startSection('page-sub','Kelola akun petugas dan owner'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts._stats_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="two-col">
  
  <div class="panel">
    <div class="ph">
      <div class="pt" style="color:var(--grn)">
        <?php echo $__env->make('layouts._icon',['name'=>'plus'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Tambah User Baru
      </div>
    </div>
    <div class="pb-body">
      <form method="POST" action="<?php echo e(route('admin.registrasi.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="fg"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" placeholder="Nama lengkap user" value="<?php echo e(old('nama_lengkap')); ?>" required></div>
        <div class="fg"><label>Username</label><input type="text" name="username" placeholder="username_unik" value="<?php echo e(old('username')); ?>" required></div>
        <div class="fg"><label>Email</label><input type="email" name="email" placeholder="email@domain.com"></div>
        <div class="fg"><label>Password</label><input type="password" name="password" placeholder="Min. 8 karakter" required></div>
        <div class="fg">
          <label>Role</label>
          <select name="role" id="add_role" required onchange="toggleArea('add_area_wrap', this.value)">
            <option value="">-- Pilih Role --</option>
            <option value="petugas" <?php echo e(old('role')==='petugas'?'selected':''); ?>>Petugas</option>
            <option value="owner"   <?php echo e(old('role')==='owner'?'selected':''); ?>>Owner</option>
          </select>
        </div>
        <div class="fg">
          <label>Area Parkir</label>
          <select name="area_ids[]" multiple id="area_select" class="form-control">
            <option value="">-- Pilih Area --</option>
            <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($area->id_area); ?>">
                <?php echo e($area->nama_area); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
          <input type="checkbox" name="status_aktif" id="add_aktif" checked style="width:auto">
          <label for="add_aktif">Aktif</label>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" class="btn btn-grn" style="flex:1;justify-content:center;padding:12px;font-size:14px">Simpan</button>
          <button type="reset" class="btn btn-out" style="padding:12px 22px">Reset</button>
        </div>
      </form>
    </div>
  </div>

  
  <div class="panel">
    <div class="ph">
      <div class="pt">Daftar User Terdaftar</div>
      <span style="font-size:12px;color:var(--gray)"><?php echo e($users->count()); ?> user</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Nama</th><th>Role</th><th>Area</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $rp = match($u->role){ 'admin'=>'p-blu','petugas'=>'p-grn','owner'=>'p-pur', default=>'p-blu' };
        $sp = $u->status_aktif ? 'p-grn' : 'p-red';
        $sl = $u->status_aktif ? 'Aktif' : 'Non Aktif';
      ?>
      <tr>
        <td class="fw7"><?php echo e($u->nama_lengkap); ?></td>
        <td><span class="pill <?php echo e($rp); ?>"><?php echo e(ucfirst($u->role)); ?></span></td>
        <td class="t-gray" style="font-size:12px">
          <?php echo e($u->area ? $u->area->nama_area : '—'); ?>

        </td>
        <td><span class="pill <?php echo e($sp); ?>"><?php echo e($sl); ?></span></td>
        <td>
          <div class="tbl-acts">
            <button class="btn btn-out btn-xs" onclick="openEdit(<?php echo e($u->id_user); ?>,'<?php echo e(addslashes($u->nama_lengkap)); ?>','<?php echo e($u->role); ?>',<?php echo e($u->status_aktif); ?>,<?php echo e($u->id_area ?? 'null'); ?>)">Edit</button>
            <?php if($u->id_user !== auth()->id() && $u->role !== 'admin'): ?>
            <form method="POST" action="<?php echo e(route('admin.registrasi.destroy', $u->id_user)); ?>" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn btn-red btn-xs">Delete</button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>


<div class="modal-ov hide" id="m-edit">
  <div class="modal">
    <div class="modal-title">Edit User <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="fg"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" id="e_nama" required></div>
      <div class="fg">
        <label>Role</label>
        <select name="role" id="e_role" onchange="toggleArea('e_area_wrap', this.value)">
          <option value="petugas">Petugas</option>
          <option value="owner">Owner</option>
        </select>
      </div>
      <div class="fg" id="e_area">
        <label>Area Parkir</label>
        <select name="id_area" id="e_area">
          <option value="">-- Pilih Area --</option>
          <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($area->id_area); ?>"><?php echo e($area->nama_area); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="fg"><label>Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah"></div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
        <input type="checkbox" name="status_aktif" id="e_aktif" style="width:auto">
        <label for="e_aktif">Aktif</label>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn btn-out" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button>
        <button type="submit" class="btn btn-grn">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#area_select').select2({
        placeholder: "-- Pilih Area --",
        width: '100%'
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleArea(wrapId, role) {
  var wrap = document.getElementById(wrapId);
  if (wrap) wrap.style.display = (role === 'petugas') ? 'flex' : 'none';
}
// Init on page load
toggleArea('add_area_wrap', document.getElementById('add_role').value);

function openEdit(id, nama, role, aktif, idArea) {
  document.getElementById('e_nama').value   = nama;
  document.getElementById('e_role').value   = role;
  document.getElementById('e_aktif').checked = aktif == 1;
  document.getElementById('e_area').value   = idArea || '';
  toggleArea('e_area_wrap', role);
  document.getElementById('edit-form').action = '/admin/registrasi/' + id;
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\park-in\resources\views/admin/registrasi.blade.php ENDPATH**/ ?>