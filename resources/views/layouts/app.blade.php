<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Park In — @yield('title','Dashboard')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('styles')
</head>
<body>
<div class="wrap">

  {{-- SIDEBAR --}}
  <aside class="sidebar">
    <div class="logo-area">
      <div class="logo">Park <span class="in">In</span></div>
      <div class="badge {{ auth()->user()->role }}">
        <div class="bdot"></div>{{ ucfirst(auth()->user()->role) }}
      </div>
    </div>

    <nav class="nav-wrap">
      <span class="nav-label">Menu{{ auth()->user()->role === 'admin' ? ' utama' : '' }}</span>

      @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.registrasi.index') }}" class="nav-item {{ request()->routeIs('admin.registrasi.*') ? 'act-admin' : '' }}">
          @include('layouts._icon', ['name'=>'user']) Registrasi User
        </a>
        <a href="{{ route('admin.tarif.index') }}" class="nav-item {{ request()->routeIs('admin.tarif.*') ? 'act-admin' : '' }}">
          @include('layouts._icon', ['name'=>'dollar']) Crud Tarif Parkir
        </a>
        <a href="{{ route('admin.area.index') }}" class="nav-item {{ request()->routeIs('admin.area.*') ? 'act-admin' : '' }}">
          @include('layouts._icon', ['name'=>'map']) Crud Area Parkir
        </a>
        <a href="{{ route('admin.kendaraan.index') }}" class="nav-item {{ request()->routeIs('admin.kendaraan.*') ? 'act-admin' : '' }}">
          @include('layouts._icon', ['name'=>'car']) Crud Kendaraan
        </a>
        <a href="{{ route('admin.log.index') }}" class="nav-item {{ request()->routeIs('admin.log.*') ? 'act-admin' : '' }}">
          @include('layouts._icon', ['name'=>'log']) Log Aktivitas
        </a>

      @elseif(auth()->user()->role === 'petugas')
        <a href="{{ route('petugas.transaksi.index') }}" class="nav-item {{ request()->routeIs('petugas.transaksi.*') ? 'act-petugas' : '' }}">
          @include('layouts._icon', ['name'=>'trx']) Transaksi Parkir
        </a>
        <a href="{{ route('petugas.struk.index') }}" class="nav-item {{ request()->routeIs('petugas.struk.*') ? 'act-petugas' : '' }}">
          @include('layouts._icon', ['name'=>'print']) Cetak Struk
        </a>

      @elseif(auth()->user()->role === 'owner')
        <a href="{{ route('owner.rekap.index') }}" class="nav-item {{ request()->routeIs('owner.rekap.*') ? 'act-owner' : '' }}">
          @include('layouts._icon', ['name'=>'chart']) Rekap Transaksi
        </a>
      @endif
    </nav>

    <div class="sb-bot">
      <div class="ucard">
        <div class="av {{ auth()->user()->role }}">{{ auth()->user()->inisial }}</div>
        <div>
          <div class="uname">{{ auth()->user()->nama_lengkap }}</div>
          <div class="uinfo">
            @if(auth()->user()->role === 'petugas')
            @else {{ ucfirst(auth()->user()->role) }}
            @endif
          </div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Logout
        </button>
      </form>
    </div>
  </aside>

  {{-- MAIN --}}
  <div class="main">
    <div class="topbar">
      <div>
        <div class="pg-title">@yield('page-title')</div>
        <div class="pg-sub">@yield('page-sub')</div>
      </div>
      <div class="tb-right">
        @yield('topbar-right')
        <div class="clock-box" id="clk"></div>
      </div>
    </div>

    <div class="content">
      @if(session('success'))
        <div class="alert a-ok">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert a-err">{{ session('error') }}</div>
      @endif
      @if($errors->any())
        <div class="alert a-err">{{ $errors->first() }}</div>
      @endif

      @yield('content')
    </div>
  </div>

</div>{{-- /wrap --}}

<script>
setInterval(() => document.getElementById('clk').textContent = new Date().toLocaleTimeString('id-ID'), 1000);
document.getElementById('clk').textContent = new Date().toLocaleTimeString('id-ID');

// Close modal on overlay click
document.querySelectorAll('.modal-ov').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.add('hide'); }));
</script>
@stack('scripts')
</body>
</html>
