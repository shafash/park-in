@extends('layouts.app')
@section('title','Log Aktivitas')
@section('page-title','Log Aktivitas')
@section('page-sub','Kelola akun petugas dan owner')

@section('content')
@include('layouts._stats_admin')

<div class="panel">
  <div class="ph">
    <div class="pt" style="color:var(--grn)">
      @include('layouts._icon',['name'=>'log']) Log Aktivitas Sistem
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <form method="GET" style="display:flex">
        <select name="role" onchange="this.form.submit()" style="background:var(--s2);border:1px solid var(--b2);border-radius:8px;padding:8px 14px;color:var(--wht);font-size:13px;outline:none;min-width:130px">
          <option value="">Semua Role</option>
          <option value="admin"   {{ $role==='admin'?'selected':'' }}>Admin</option>
          <option value="petugas" {{ $role==='petugas'?'selected':'' }}>Petugas</option>
          <option value="owner"   {{ $role==='owner'?'selected':'' }}>Owner</option>
        </select>
      </form>
      <a href="{{ route('admin.log.export', ['role'=>$role]) }}" class="btn btn-out btn-sm">Export CSV</a>
    </div>
  </div>

  @forelse($logs as $log)
  @php
    $dc = match($log->user->role ?? '') { 'admin'=>'var(--blu)', 'petugas'=>'var(--grn)', 'owner'=>'var(--pur)', default=>'var(--gray)' };
    $wkt = $log->waktu_aktivitas->format('H:i') . ' WIB — ' . $log->waktu_aktivitas->format('d M Y');
  @endphp
  <div style="display:flex;align-items:flex-start;gap:14px;padding:15px 22px;border-bottom:1px solid var(--bdr)">
    <div style="width:8px;height:8px;border-radius:50%;background:{{ $dc }};flex-shrink:0;margin-top:5px"></div>
    <div>
      <div style="font-size:14px">
        <strong>{{ $log->user->nama_lengkap ?? '—' }}</strong>
        <span class="t-gray"> ({{ ucfirst($log->user->role ?? '—') }})</span>
        — {{ $log->aktivitas }}
      </div>
      <div style="font-size:12px;color:var(--gray2);margin-top:3px">{{ $wkt }}</div>
    </div>
  </div>
  @empty
  <div style="text-align:center;color:var(--gray);padding:34px">Belum ada aktivitas.</div>
  @endforelse

  @if($logs->hasPages())
  <div class="pager">
    <span class="pager-info">Hal {{ $logs->currentPage() }}/{{ $logs->lastPage() }} — {{ $logs->total() }} entri</span>
    <div class="pager-btns">
      @if($logs->onFirstPage()) <span class="pb dis">&#8249;</span> @else <a href="{{ $logs->previousPageUrl() }}" class="pb">&#8249;</a> @endif
      @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}" class="pb {{ $page === $logs->currentPage() ? 'act' : '' }}">{{ $page }}</a>
      @endforeach
      @if($logs->hasMorePages()) <a href="{{ $logs->nextPageUrl() }}" class="pb">&#8250;</a> @else <span class="pb dis">&#8250;</span> @endif
    </div>
  </div>
  @endif
</div>
@endsection
