<?php $__env->startSection('title','Crud Kendaraan'); ?>
<?php $__env->startSection('page-title','Kelola Kendaraan'); ?>
<?php $__env->startSection('page-sub','Data kendaraan terdaftar pada sistem'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts._stats_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      <?php echo $__env->make('layouts._icon',['name'=>'car'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Data Kendaraan
    </div>
    <form method="GET" class="sbar" style="flex:1;justify-content:flex-end">
      <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari Plat / Pemilik / Merek..." style="width:210px">
      <select name="jenis" onchange="this.form.submit()" style="min-width:130px">
        <option value="">Semua Jenis</option>
        <?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($j); ?>" <?php echo e($jenis == $j ? 'selected' : ''); ?>>
            <?php echo e(ucfirst($j)); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </form>
    <button class="btn btn-grn btn-sm" onclick="document.getElementById('m-tambah').classList.remove('hide')">+ &nbsp;Tambah</button>
  </div>

  <table class="tbl">
    <thead><tr>
      <th>*</th>
      <th>Foto</th>
      <th><a href="<?php echo e(request()->fullUrlWithQuery(['sort'=>'plat_nomor','order'=>$order==='asc'?'desc':'asc'])); ?>" class="sl">Plat Nomor <?php echo e($sort==='plat_nomor'?($order==='asc'?'▲':'▼'):''); ?></a></th>
      <th><a href="<?php echo e(request()->fullUrlWithQuery(['sort'=>'jenis_kendaraan','order'=>$order==='asc'?'desc':'asc'])); ?>" class="sl">Jenis <?php echo e($sort==='jenis_kendaraan'?($order==='asc'?'▲':'▼'):''); ?></a></th>
      <th>Merek / Model</th>
      <th>Warna</th>
      <th><a href="<?php echo e(request()->fullUrlWithQuery(['sort'=>'pemilik','order'=>$order==='asc'?'desc':'asc'])); ?>" class="sl">Pemilik <?php echo e($sort==='pemilik'?($order==='asc'?'▲':'▼'):''); ?></a></th>
      <th>Terdaftar</th>
      <th>Aksi</th>
    </tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $kendaraans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
      <td class="t-gray"><?php echo e($kendaraans->firstItem() + $i); ?></td>
      <td>
        <?php if($k->foto): ?>
          <img src="<?php echo e(asset('uploads/kendaraan/'.$k->foto)); ?>" alt="<?php echo e($k->plat_nomor); ?>"
               style="width:52px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--b2);cursor:pointer"
               onclick="previewFoto('<?php echo e(asset('uploads/kendaraan/'.$k->foto)); ?>','<?php echo e($k->plat_nomor); ?>')">
        <?php else: ?>
          <div style="width:52px;height:40px;border-radius:6px;background:var(--s2);border:1px dashed var(--b2);display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--gray2)">No img</div>
        <?php endif; ?>
      </td>
      <td class="fw7"><?php echo e($k->plat_nomor); ?></td>
      <?php
        $kj = $k->jenis_kendaraan ?? '';
        $k_jc = $jenisColors[$kj] ?? 'p-blu';
        $k_jl = $kj === 'lainnya' ? 'Truk' : ($kj ? ucfirst($kj) : '—');
      ?>
      <td><span class="pill <?php echo e($k_jc); ?>"><?php echo e($k_jl); ?></span></td>
      <td><?php echo e($k->merek ?: '—'); ?></td>
      <td class="t-gray" style="font-size:12px"><?php echo e($k->warna ?: '—'); ?></td>
      <td><?php echo e($k->pemilik ?: '—'); ?></td>
      <td class="t-gray" style="font-size:12px"><?php echo e($k->created_at?->format('d M Y')); ?></td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit(<?php echo e($k->id_kendaraan); ?>,'<?php echo e(addslashes($k->plat_nomor)); ?>','<?php echo e($k->jenis_kendaraan); ?>','<?php echo e(addslashes($k->merek)); ?>','<?php echo e(addslashes($k->warna)); ?>','<?php echo e(addslashes($k->pemilik)); ?>','<?php echo e($k->foto); ?>')">Edit</button>
          <form method="POST" action="<?php echo e(route('admin.kendaraan.destroy',$k->id_kendaraan)); ?>" style="display:inline" onsubmit="return confirm('Hapus kendaraan ini?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-red btn-xs">Delete</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr><td colspan="9" style="text-align:center;color:var(--gray);padding:30px">Tidak ada data.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <div class="pager">
    <span class="pager-info">Menampilkan <?php echo e($kendaraans->firstItem()); ?> - <?php echo e($kendaraans->lastItem()); ?> dari <?php echo e($kendaraans->total()); ?> kendaraan</span>
    <div class="pager-btns">
      <?php if($kendaraans->onFirstPage()): ?> <span class="pb dis">&#8249;</span> <?php else: ?> <a href="<?php echo e($kendaraans->previousPageUrl()); ?>" class="pb">&#8249;</a> <?php endif; ?>
      <?php $__currentLoopData = $kendaraans->getUrlRange(max(1,$kendaraans->currentPage()-2), min($kendaraans->lastPage(),$kendaraans->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>" class="pb <?php echo e($page === $kendaraans->currentPage() ? 'act' : ''); ?>"><?php echo e($page); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if($kendaraans->hasMorePages()): ?> <a href="<?php echo e($kendaraans->nextPageUrl()); ?>" class="pb">&#8250;</a> <?php else: ?> <span class="pb dis">&#8250;</span> <?php endif; ?>
    </div>
  </div>
</div>


<div class="modal-ov hide" id="m-tambah">
  <div class="modal" style="width:560px">
    <div class="modal-title">Tambah Kendaraan <button class="modal-close" onclick="document.getElementById('m-tambah').classList.add('hide')">✕</button></div>
    <form method="POST" action="<?php echo e(route('admin.kendaraan.store')); ?>" enctype="multipart/form-data"><?php echo csrf_field(); ?>
      <div class="form-row">
        <div class="fg"><label>Plat Nomor</label><input type="text" name="plat_nomor" id="add_plat" placeholder="B 1234 ABC (kosongkan untuk sepeda)" style="text-transform:uppercase"></div>
        <div class="fg"><label>Jenis</label><select name="jenis_kendaraan" id="add_jenis" required onchange="togglePlatForAdd(this.value)"><option value="">-- Pilih --</option><?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($j); ?>"><?php echo e(ucfirst($j)); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
      </div>
      <div class="form-row">
        <div class="fg"><label>Merek / Model</label><input type="text" name="merek" placeholder="Contoh: Honda Vario 160"></div>
        <div class="fg"><label>Warna</label><input type="text" name="warna" placeholder="Contoh: Merah"></div>
      </div>
      <div class="fg"><label>Pemilik</label><input type="text" name="pemilik" placeholder="Nama pemilik kendaraan"></div>
      <div class="fg">
        <label>Foto Kendaraan <span style="color:var(--gray2);font-size:11px">(JPG/PNG, maks 2MB)</span></label>
        <input type="file" name="foto" accept="image/*" onchange="previewAdd(this)" style="cursor:pointer">
        <img id="add_preview" src="" alt="" style="display:none;margin-top:8px;max-height:120px;border-radius:8px;border:1px solid var(--b2)">
      </div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-tambah').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>


<div class="modal-ov hide" id="m-edit">
  <div class="modal" style="width:560px">
    <div class="modal-title">Edit Kendaraan <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form" enctype="multipart/form-data"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <input type="hidden" name="hapus_foto" id="hapus_foto_flag" value="0">
      <div class="form-row">
        <div class="fg"><label>Plat Nomor</label><input type="text" name="plat_nomor" id="e_plat" style="text-transform:uppercase"></div>
        <div class="fg"><label>Jenis</label><select name="jenis_kendaraan" id="e_jenis" onchange="togglePlatForEdit(this.value)"><?php $__currentLoopData = $jenisList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($j); ?>"><?php echo e(ucfirst($j)); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
      </div>
      <div class="form-row">
        <div class="fg"><label>Merek / Model</label><input type="text" name="merek" id="e_merek"></div>
        <div class="fg"><label>Warna</label><input type="text" name="warna" id="e_warna"></div>
      </div>
      <div class="fg"><label>Pemilik</label><input type="text" name="pemilik" id="e_pemilik"></div>
      <div class="fg">
        <label>Foto Kendaraan</label>
        <div id="e_foto_wrap" style="margin-bottom:8px;display:none">
          <img id="e_foto_preview" src="" style="max-height:100px;border-radius:8px;border:1px solid var(--b2);display:block;margin-bottom:6px">
          <button type="button" class="btn btn-red btn-xs" onclick="hapusFoto()">✕ Hapus Foto</button>
        </div>
        <input type="file" name="foto" id="e_foto_input" accept="image/*" onchange="previewEdit(this)" style="cursor:pointer">
      </div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>


<div class="modal-ov hide" id="m-foto">
  <div class="modal" style="width:400px;text-align:center">
    <div class="modal-title" id="foto_modal_title">Foto Kendaraan <button class="modal-close" onclick="document.getElementById('m-foto').classList.add('hide')">✕</button></div>
    <img id="foto_modal_img" src="" style="max-width:100%;border-radius:10px">
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function previewAdd(input) {
  var p = document.getElementById('add_preview');
  if (input.files && input.files[0]) {
    p.src = URL.createObjectURL(input.files[0]);
    p.style.display = 'block';
  }
}
function previewEdit(input) {
  if (input.files && input.files[0]) {
    var wrap = document.getElementById('e_foto_wrap');
    document.getElementById('e_foto_preview').src = URL.createObjectURL(input.files[0]);
    wrap.style.display = 'block';
    document.getElementById('hapus_foto_flag').value = '0';
  }
}
function hapusFoto() {
  document.getElementById('hapus_foto_flag').value = '1';
  document.getElementById('e_foto_wrap').style.display = 'none';
  document.getElementById('e_foto_input').value = '';
}
function previewFoto(url, plat) {
  document.getElementById('foto_modal_img').src = url;
  document.getElementById('foto_modal_title').childNodes[0].textContent = plat + ' ';
  document.getElementById('m-foto').classList.remove('hide');
}
function openEdit(id, plat, jenis, merek, warna, pemilik, foto) {
  document.getElementById('e_plat').value    = plat;
  document.getElementById('e_jenis').value   = jenis;
  document.getElementById('e_merek').value   = merek;
  document.getElementById('e_warna').value   = warna;
  document.getElementById('e_pemilik').value = pemilik;
  document.getElementById('hapus_foto_flag').value = '0';
  document.getElementById('e_foto_input').value = '';

  var wrap = document.getElementById('e_foto_wrap');
  if (foto) {
    document.getElementById('e_foto_preview').src = '/uploads/kendaraan/' + foto;
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
  }
  document.getElementById('edit-form').action = '/admin/kendaraan/' + id;
  // adjust plat input requirement/placeholder based on jenis
  togglePlatForEdit(jenis);
  document.getElementById('m-edit').classList.remove('hide');
}

function togglePlatForAdd(jenis) {
  var inp = document.getElementById('add_plat');
  if (!inp) return;
  if (String(jenis).toLowerCase() === 'sepeda') {
    inp.removeAttribute('required');
    inp.placeholder = 'Kosongkan untuk sepeda — kode otomatis akan dibuat';
  } else {
    inp.setAttribute('required','required');
    inp.placeholder = 'B 1234 ABC';
  }
}

function togglePlatForEdit(jenis) {
  var inp = document.getElementById('e_plat');
  if (!inp) return;
  if (String(jenis).toLowerCase() === 'sepeda') {
    inp.removeAttribute('required');
    inp.placeholder = 'Kosongkan untuk sepeda — kode otomatis akan dibuat';
  } else {
    inp.setAttribute('required','required');
    inp.placeholder = '';
  }
}

document.addEventListener('DOMContentLoaded', function(){
  var addSel = document.getElementById('add_jenis');
  if (addSel) togglePlatForAdd(addSel.value);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\park-in\resources\views/admin/kendaraan.blade.php ENDPATH**/ ?>