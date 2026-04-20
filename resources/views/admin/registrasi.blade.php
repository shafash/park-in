@extends('layouts.app')
@section('title','Registrasi User')
@section('page-title','Registrasi User')
@section('page-sub','Kelola akun petugas dan owner')

@section('content')
@include('layouts._stats_admin')

<div class="two-col">
  {{-- FORM TAMBAH --}}
  <div class="panel">
    <div class="ph">
      <div class="pt" style="color:var(--grn)">
        @include('layouts._icon',['name'=>'plus']) Tambah User Baru
      </div>
    </div>
    <div class="pb-body">
      <form method="POST" action="{{ route('admin.registrasi.store') }}">
        @csrf
        <div class="fg"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" placeholder="Nama lengkap user" value="{{ old('nama_lengkap') }}" required></div>
        <div class="fg"><label>Username</label><input type="text" name="username" placeholder="username_unik" value="{{ old('username') }}" required></div>
        <div class="fg"><label>Email</label><input type="email" name="email" placeholder="email@domain.com"></div>
        <div class="fg"><label>Password</label><input type="password" name="password" placeholder="Min. 8 karakter" required></div>
        <div class="fg">
          <label>Role</label>
          <select name="role" id="add_role" required onchange="toggleArea('add_area_wrap', this.value)">
            <option value="">-- Pilih Role --</option>
            <option value="petugas" {{ old('role')==='petugas'?'selected':'' }}>Petugas</option>
            <option value="owner"   {{ old('role')==='owner'?'selected':'' }}>Owner</option>
          </select>
        </div>
        <div class="fg">
          <label>Area Parkir</label>
          <select name="id_area" class="form-control">
            <option value="">-- Pilih Area --</option>
            @foreach($areas as $area)
              <option value="{{ $area->id_area }}">
                {{ $area->nama_area }}
              </option>
            @endforeach
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

  {{-- TABEL USER --}}
  <div class="panel">
    <div class="ph">
      <div class="pt">Daftar User Terdaftar</div>
      <span style="font-size:12px;color:var(--gray)">{{ $users->count() }} user</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Nama</th><th>Role</th><th>Area</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      @foreach($users as $u)
      @php
        $rp = match($u->role){ 'admin'=>'p-blu','petugas'=>'p-grn','owner'=>'p-pur', default=>'p-blu' };
        $sp = $u->status_aktif ? 'p-grn' : 'p-red';
        $sl = $u->status_aktif ? 'Aktif' : 'Non Aktif';
      @endphp
      <tr>
        <td class="fw7">{{ $u->nama_lengkap }}</td>
        <td><span class="pill {{ $rp }}">{{ ucfirst($u->role) }}</span></td>
        <td class="t-gray" style="font-size:12px">
          {{ $u->area ? $u->area->nama_area : '—' }}
        </td>
        <td><span class="pill {{ $sp }}">{{ $sl }}</span></td>
        <td>
          <div class="tbl-acts">
            <button class="btn btn-out btn-xs" onclick="openEdit({{ $u->id_user }},'{{ addslashes($u->nama_lengkap) }}','{{ $u->role }}',{{ $u->status_aktif }},{{ $u->id_area ?? 'null' }})">Edit</button>
            @if($u->id_user !== auth()->id() && $u->role !== 'admin')
            <form method="POST" action="{{ route('admin.registrasi.destroy', $u->id_user) }}" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-red btn-xs">Delete</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
    @if($users->hasPages())
    <div class="pager">
      <span class="pager-info">Hal {{ $users->currentPage() }}/{{ $users->lastPage() }} — {{ $users->total() }} entri</span>
      <div class="pager-btns">
        @if($users->onFirstPage()) <span class="pb dis">&#8249;</span> @else <a href="{{ $users->previousPageUrl() }}" class="pb">&#8249;</a> @endif
        @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
          <a href="{{ $url }}" class="pb {{ $page === $users->currentPage() ? 'act' : '' }}">{{ $page }}</a>
        @endforeach
        @if($users->hasMorePages()) <a href="{{ $users->nextPageUrl() }}" class="pb">&#8250;</a> @else <span class="pb dis">&#8250;</span> @endif
      </div>
    </div>
    @endif
  </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-ov hide" id="m-edit">
  <div class="modal">
    <div class="modal-title">Edit User <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form">
      @csrf @method('PUT')
      <div class="fg"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" id="e_nama" required></div>
      <div class="fg">
        <label>Role</label>
        <select name="role" id="e_role" onchange="toggleArea('e_area_wrap', this.value)">
          <option value="petugas">Petugas</option>
          <option value="owner">Owner</option>
        </select>
      </div>
      <div class="fg" id="e_area_wrap">
        <label>Area Parkir</label>
        <select name="id_area" id="e_area">
          <option value="">-- Pilih Area --</option>
          @foreach($areas as $area)
            <option value="{{ $area->id_area }}">{{ $area->nama_area }}</option>
          @endforeach
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
@endsection

@push('scripts')
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
@endpush
