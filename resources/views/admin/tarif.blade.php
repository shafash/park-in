@extends('layouts.app')
@section('title','Crud Tarif Parkir')
@section('page-title','Tarif Parkir')
@section('page-sub','Kelola akun petugas dan owner')

@section('content')
@include('layouts._stats_admin')
@php $MAKS=['motor'=>15000,'mobil'=>30000,'lainnya'=>60000]; $DENDA=['motor'=>3000,'mobil'=>5000,'lainnya'=>10000]; @endphp

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      @include('layouts._icon',['name'=>'dollar']) Kelola Tarif Parkir
    </div>
    <button class="btn btn-grn btn-sm" onclick="document.getElementById('m-tambah').classList.remove('hide')">
      + &nbsp;Tarif Parkir
    </button>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>*</th><th>Jenis Kendaraan</th><th>Tarif / Jam</th><th>Tarif maks / Hari</th><th>Denda / Jam lebih</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    @forelse($tarifs as $i => $t)
    <tr>
      <td class="t-gray">{{ $i+1 }}</td>
      <td class="fw7">{{ $t->jenis_kendaraan === 'lainnya' ? 'Truk' : ucfirst($t->jenis_kendaraan) }}</td>
      <td class="t-grn fw7">{{ $t->rupiah }}</td>
      <td class="t-gray">Rp. {{ number_format($MAKS[$t->jenis_kendaraan]??30000,0,',','.') }}</td>
      <td class="t-ora fw7">Rp. {{ number_format($DENDA[$t->jenis_kendaraan]??5000,0,',','.') }}</td>
      <td><span class="pill p-grn">Aktif</span></td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit({{ $t->id_tarif }},'{{ $t->jenis_kendaraan }}',{{ $t->tarif_per_jam }})">Edit</button>
          <form method="POST" action="{{ route('admin.tarif.destroy',$t->id_tarif) }}" style="display:inline" onsubmit="return confirm('Hapus tarif ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-red btn-xs">Delete</button>
          </form>
        </div>
      </td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center;color:var(--gray);padding:30px">Belum ada tarif.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="modal-ov hide" id="m-tambah">
  <div class="modal">
    <div class="modal-title">Tambah Tarif <button class="modal-close" onclick="document.getElementById('m-tambah').classList.add('hide')">✕</button></div>
    <form method="POST" action="{{ route('admin.tarif.store') }}">
      @csrf
      <div class="fg"><label>Jenis Kendaraan</label><select name="jenis_kendaraan" required><option value="">-- Pilih --</option><option value="mobil">Mobil</option><option value="motor">Motor</option><option value="lainnya">Truk / Lainnya</option></select></div>
      <div class="fg"><label>Tarif per Jam (Rp)</label><input type="number" name="tarif_per_jam" placeholder="Contoh: 3000" min="100" required></div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-tambah').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>

<div class="modal-ov hide" id="m-edit">
  <div class="modal">
    <div class="modal-title">Edit Tarif <button class="modal-close" onclick="document.getElementById('m-edit').classList.add('hide')">✕</button></div>
    <form method="POST" id="edit-form">
      @csrf @method('PUT')
      <div class="fg"><label>Jenis Kendaraan</label><select name="jenis_kendaraan" id="e_jenis"><option value="mobil">Mobil</option><option value="motor">Motor</option><option value="lainnya">Truk / Lainnya</option></select></div>
      <div class="fg"><label>Tarif per Jam (Rp)</label><input type="number" name="tarif_per_jam" id="e_tarif" min="100" required></div>
      <div class="modal-foot"><button type="button" class="btn btn-out" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button><button type="submit" class="btn btn-grn">Simpan</button></div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(id,j,t){
  document.getElementById('e_jenis').value=j;
  document.getElementById('e_tarif').value=t;
  document.getElementById('edit-form').action='/admin/tarif/'+id;
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
@endpush
