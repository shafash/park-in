@extends('layouts.app')
@section('title','Crud Kendaraan')
@section('page-title','Kelola Kendaraan')
@section('page-sub','Data kendaraan terdaftar pada sistem')

@section('content')
@include('layouts._stats_admin')

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      @include('layouts._icon',['name'=>'car']) Data Kendaraan
    </div>
    <form method="GET" class="sbar" style="flex:1;justify-content:flex-end">
      <input type="text" name="q" value="{{ $q }}" placeholder="Cari Plat / Pemilik / Merek..." style="width:210px">
      <select name="jenis" onchange="this.form.submit()" style="min-width:130px">
        <option value="">Semua Jenis</option>
        @foreach($jenisList as $j)
          <option value="{{ $j }}" {{ $jenis == $j ? 'selected' : '' }}>
            {{ ucfirst($j) }}
          </option>
        @endforeach
      </select>
    </form>
    <button class="btn btn-grn btn-sm" onclick="document.getElementById('m-tambah').classList.remove('hide')">+ &nbsp;Tambah</button>
  </div>

  <table class="tbl">
    <thead><tr>
      <th>*</th>
      <th>Foto</th>
      <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'plat_nomor','order'=>$order==='asc'?'desc':'asc']) }}" class="sl">Plat Nomor {{ $sort==='plat_nomor'?($order==='asc'?'▲':'▼'):'' }}</a></th>
      <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'jenis_kendaraan','order'=>$order==='asc'?'desc':'asc']) }}" class="sl">Jenis {{ $sort==='jenis_kendaraan'?($order==='asc'?'▲':'▼'):'' }}</a></th>
      <th>Merek / Model</th>
      <th>Warna</th>
      <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'pemilik','order'=>$order==='asc'?'desc':'asc']) }}" class="sl">Pemilik {{ $sort==='pemilik'?($order==='asc'?'▲':'▼'):'' }}</a></th>
      <th>Terdaftar</th>
      <th>Aksi</th>
    </tr></thead>
    <tbody>
    @forelse($kendaraans as $i => $k)
    <tr>
      <td class="t-gray">{{ $kendaraans->firstItem() + $i }}</td>
      <td>
        @if($k->foto)
          <img src="{{ asset('uploads/kendaraan/'.$k->foto) }}" alt="{{ $k->plat_nomor }}"
               style="width:52px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--b2);cursor:pointer"
               onclick="previewFoto('{{ asset('uploads/kendaraan/'.$k->foto) }}','{{ $k->plat_nomor }}')">
        @else
          <div style="width:52px;height:40px;border-radius:6px;background:var(--s2);border:1px dashed var(--b2);display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--gray2)">No img</div>
        @endif
      </td>
      <td class="fw7">{{ $k->plat_nomor }}</td>
      <td><span class="pill {{ $k->jenisPill }}">{{ $k->jenisLabel }}</span></td>
      <td>{{ $k->merek ?: '—' }}</td>
      <td class="t-gray" style="font-size:12px">{{ $k->warna ?: '—' }}</td>
      <td>{{ $k->pemilik ?: '—' }}</td>
      <td class="t-gray" style="font-size:12px">{{ $k->created_at?->format('d M Y') }}</td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit({{ $k->id_kendaraan }},'{{ addslashes($k->plat_nomor) }}','{{ $k->jenis_kendaraan }}','{{ addslashes($k->merek) }}','{{ addslashes($k->warna) }}','{{ addslashes($k->pemilik) }}','{{ $k->foto }}')">Edit</button>
          <form method="POST" action="{{ route('admin.kendaraan.destroy',$k->id_kendaraan) }}" style="display:inline" onsubmit="return confirm('Hapus kendaraan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-red btn-xs">Delete</button>
          </form>
        </div>
      </td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;color:var(--gray);padding:30px">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
  </table>

  <div class="pager">
    <span class="pager-info">Menampilkan {{ $kendaraans->firstItem() }} - {{ $kendaraans->lastItem() }} dari {{ $kendaraans->total() }} kendaraan</span>
    <div class="pager-btns">
      @if($kendaraans->onFirstPage()) <span class="pb dis">&#8249;</span> @else <a href="{{ $kendaraans->previousPageUrl() }}" class="pb">&#8249;</a> @endif
      @foreach($kendaraans->getUrlRange(max(1,$kendaraans->currentPage()-2), min($kendaraans->lastPage(),$kendaraans->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}" class="pb {{ $page === $kendaraans->currentPage() ? 'act' : '' }}">{{ $page }}</a>
      @endforeach
      @if($kendaraans->hasMorePages()) <a href="{{ $kendaraans->nextPageUrl() }}" class="pb">&#8250;</a> @else <span class="pb dis">&#8250;</span> @endif
    </div>
  </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-ov hide" id="m-tambah">
  <div class="modal" style="width:560px">
    <div class="modal-title">Tambah Kendaraan <button class="modal-close" onclick="document.getElementById('m-tambah').classList.add('hide')">✕</button></div>
    <form method="POST" action="{{ route('admin.kendaraan.store') }}" enctype="multipart/form-data">@csrf
      <div class="form-row">
        <div class="fg"><label>Plat Nomor</label><input type="text" name="plat_nomor" placeholder="B 1234 ABC" required style="text-transform:uppercase"></div>
        <div class="fg"><label>Jenis</label><select name="jenis_kendaraan" required><option value="">-- Pilih --</option>@foreach($jenisList as $j) <option value="{{ $j }}">{{ ucfirst($j) }}</option> @endforeach</select></div>
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

{{-- MODAL EDIT --}}
<div class="modal-ov hide" id="m-edit">
  <div class="modal" style="width:560px">
    <div class="modal-title">Edit Kendaraan <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form" enctype="multipart/form-data">@csrf @method('PUT')
      <input type="hidden" name="hapus_foto" id="hapus_foto_flag" value="0">
      <div class="form-row">
        <div class="fg"><label>Plat Nomor</label><input type="text" name="plat_nomor" id="e_plat" required style="text-transform:uppercase"></div>
        <div class="fg"><label>Jenis</label><select name="jenis_kendaraan" id="e_jenis"><option value="mobil">Mobil</option><option value="motor">Motor</option><option value="lainnya">Truk</option></select></div>
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

{{-- MODAL PREVIEW FOTO --}}
<div class="modal-ov hide" id="m-foto">
  <div class="modal" style="width:400px;text-align:center">
    <div class="modal-title" id="foto_modal_title">Foto Kendaraan <button class="modal-close" onclick="document.getElementById('m-foto').classList.add('hide')">✕</button></div>
    <img id="foto_modal_img" src="" style="max-width:100%;border-radius:10px">
  </div>
</div>
@endsection

@push('scripts')
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
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
@endpush
