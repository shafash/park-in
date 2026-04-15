@extends('layouts.app')
@section('title','Crud Area Parkir')
@section('page-title','Area Parkir')
@section('page-sub','Kelola akun petugas dan owner')

@section('content')
@include('layouts._stats_admin')

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      @include('layouts._icon',['name'=>'map']) Kelola Area Parkir
    </div>
    <button class="btn btn-grn btn-sm" onclick="document.getElementById('m-tambah').classList.remove('hide')">
      + &nbsp;Area Parkir
    </button>
  </div>
  <table class="tbl">
    <thead><tr><th>*</th><th>Nama Area</th><th>Alamat</th><th>Kapasitas</th><th>Terisi</th><th>Okupansi</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($areas as $i => $a)
    <tr>
      <td class="t-gray">{{ $i+1 }}</td>
      <td class="fw7">{{ $a->nama_area }}</td>
      <td class="t-gray" style="font-size:12px">{{ $a->alamat ?: '—' }}</td>
      <td>{{ $a->kapasitas }}</td>
      <td>{{ $a->terisi }}</td>
      <td class="fw7" style="color:{{ $a->okupansiColor }}">{{ $a->okupansi }}%</td>
      <td><span class="pill {{ $a->status ? 'p-grn' : 'p-red' }}">{{ $a->status ? 'Aktif' : 'Non Aktif' }}</span></td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit({{ $a->id_area }},'{{ addslashes($a->nama_area) }}','{{ addslashes($a->alamat) }}',{{ $a->kapasitas }},{{ $a->status }})">Edit</button>
          <form method="POST" action="{{ route('admin.area.destroy',$a->id_area) }}" style="display:inline" onsubmit="return confirm('Hapus area ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-red btn-xs">Delete</button>
          </form>
        </div>
      </td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center;color:var(--gray);padding:30px">Belum ada area.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="modal-ov hide" id="m-tambah">
  <div class="modal">
    <div class="modal-title">Tambah Area Parkir <button class="modal-close" onclick="document.getElementById('m-tambah').classList.add('hide')">✕</button></div>
    <form method="POST" action="{{ route('admin.area.store') }}">@csrf
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
    <form method="POST" id="edit-form">@csrf @method('PUT')
      <div class="fg"><label>Nama Area</label><input type="text" name="nama_area" id="e_nama" required></div>
      <div class="fg"><label>Alamat</label><input type="text" name="alamat" id="e_alamat"></div>
      <div class="fg"><label>Kapasitas</label><input type="number" name="kapasitas" id="e_kap" min="1" required></div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px"><input type="checkbox" name="status" id="e_stat" style="width:auto"><label for="e_stat">Aktif</label></div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(id,nama,alamat,kap,stat){
  document.getElementById('e_nama').value=nama; document.getElementById('e_alamat').value=alamat;
  document.getElementById('e_kap').value=kap; document.getElementById('e_stat').checked=stat==1;
  document.getElementById('edit-form').action='/admin/area/'+id;
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
@endpush
