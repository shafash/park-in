@extends('layouts.app')
@section('title','Crud Tarif Parkir')
@section('page-title','Tarif Parkir')
@section('page-sub','Kelola tarif parkir')

@section('content')
@include('layouts._stats_admin')

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
      <tr>
        <th>*</th>
        <th>Jenis Kendaraan</th>
        <th>Tarif Awal</th>
        <th>Tarif / Jam</th>
        <th>Batas Durasi (jam)</th>
        <th>Denda / Jam</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    @forelse($tarifs as $i => $t)
    <tr>
      <td class="t-gray">{{ $i+1 }}</td>
      <td class="fw7">{{ ucwords($t->jenis_kendaraan) }}</td>
      <td class="t-gray">Rp. {{ number_format($t->tarif_awal ?? 0,0,',','.') }}</td>
      <td class="t-grn fw7">{{ $t->rupiah }}</td>
      <td class="t-gray">{{ $t->batas_durasi_jam ?? 0 }}j</td>
      <td class="t-gray">Rp. {{ number_format($t->denda_per_jam ?? 0,0,',','.') }}</td>
      <td>
        <div class="tbl-acts">
          <button class="btn btn-out btn-xs" onclick="openEdit({{ $t->id_tarif }},'{{ $t->jenis_kendaraan }}',{{ $t->tarif_awal ?? 0 }},{{ $t->tarif_per_jam }},{{ $t->batas_durasi_jam ?? 8 }},{{ $t->denda_per_jam ?? 0 }})">Edit</button>
          <form id="form-hapus-tarif-{{ $t->id_tarif }}" method="POST"
                action="{{ route('admin.tarif.destroy', $t->id_tarif) }}"
                style="display:none">
            @csrf @method('DELETE')
          </form>

          <button type="button"
                  data-modal="hapus"
                  data-form-id="form-hapus-tarif-{{ $t->id_tarif }}"
                  data-label="Tarif"
                  data-nama="{{ ucfirst($t->jenis_kendaraan) }} — {{ $t->rupiah }}/jam"
                  data-warn="Hapus hanya jika tarif belum dipakai dalam transaksi."
                  class="btn btn-red btn-xs">
            Delete
          </button>
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
      <div class="fg"><label>Jenis Kendaraan</label><input type="text" name="jenis_kendaraan" placeholder="Contoh: Mobil" required></div>
      <div class="fg"><label>Tarif Awal (Rp)</label><input type="number" name="tarif_awal" placeholder="Contoh: 2000" min="0" required></div>
      <div class="fg"><label>Tarif per Jam (Rp)</label><input type="number" name="tarif_per_jam" placeholder="Contoh: 3000" min="100" required></div>
      
      <div class="fg"><label>Batas Durasi (jam)</label><input type="number" name="batas_durasi_jam" placeholder="Contoh: 8" min="0" value="8"></div>
      <div class="fg"><label>Denda per Jam (Rp)</label><input type="number" name="denda_per_jam" placeholder="Contoh: 5000" min="0" value="0"></div>
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
      @csrf @method('PUT')
      <div class="fg"><label>Jenis Kendaraan</label><input type="text" name="jenis_kendaraan" id="e_jenis" required></div>
      <div class="fg"><label>Tarif Awal (Rp)</label><input type="number" name="tarif_awal" id="e_awal" min="0" required></div>
      <div class="fg"><label>Tarif per Jam (Rp)</label><input type="number" name="tarif_per_jam" id="e_tarif" min="100" required></div>
      
      <div class="fg"><label>Batas Durasi (jam)</label><input type="number" name="batas_durasi_jam" id="e_batas" min="0" value="8"></div>
      <div class="fg"><label>Denda per Jam (Rp)</label><input type="number" name="denda_per_jam" id="e_denda" min="0" value="0"></div>
      <div class="modal-foot">
        <button type="button" class="btn btn-out btn-xs" onclick="document.getElementById('m-edit').classList.add('hide')">Batal</button>
        <button type="submit" class="btn btn-grn btn-xs">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(id,j,awal,t,batas,denda){
  document.getElementById('e_jenis').value = j;
  document.getElementById('e_awal').value = awal;
  document.getElementById('e_tarif').value = t;
  document.getElementById('e_batas').value = batas ?? 8;
  document.getElementById('e_denda').value = denda ?? 0;
  document.getElementById('edit-form').action='/admin/tarif/'+id;
  document.getElementById('m-edit').classList.remove('hide');
}
</script>
@endpush
