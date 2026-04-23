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
        <a href="{{ route('admin.registrasi.index') }}"
           class="nav-item {{ request()->routeIs('admin.registrasi.*') ? 'act-admin' : '' }}">
          @include('layouts._icon',['name'=>'user']) Registrasi User
        </a>
        <a href="{{ route('admin.tarif.index') }}"
           class="nav-item {{ request()->routeIs('admin.tarif.*') ? 'act-admin' : '' }}">
          @include('layouts._icon',['name'=>'dollar']) Crud Tarif Parkir
        </a>
        <a href="{{ route('admin.area.index') }}"
           class="nav-item {{ request()->routeIs('admin.area.*') ? 'act-admin' : '' }}">
          @include('layouts._icon',['name'=>'map']) Crud Area Parkir
        </a>
        <a href="{{ route('admin.kendaraan.index') }}"
           class="nav-item {{ request()->routeIs('admin.kendaraan.*') ? 'act-admin' : '' }}">
          @include('layouts._icon',['name'=>'car']) Crud Kendaraan
        </a>
        <a href="{{ route('admin.log.index') }}"
           class="nav-item {{ request()->routeIs('admin.log.*') ? 'act-admin' : '' }}">
          @include('layouts._icon',['name'=>'log']) Log Aktivitas
        </a>

      @elseif(auth()->user()->role === 'petugas')
        <a href="{{ route('petugas.transaksi.index') }}"
           class="nav-item {{ request()->routeIs('petugas.transaksi.*') ? 'act-petugas' : '' }}">
          @include('layouts._icon',['name'=>'trx']) Transaksi Parkir
        </a>
        <a href="{{ route('petugas.struk.index') }}"
           class="nav-item {{ request()->routeIs('petugas.struk.*') ? 'act-petugas' : '' }}">
          @include('layouts._icon',['name'=>'print']) Cetak Struk
        </a>

      @elseif(auth()->user()->role === 'owner')
        <a href="{{ route('owner.rekap.index') }}"
           class="nav-item {{ request()->routeIs('owner.rekap.*') ? 'act-owner' : '' }}">
          @include('layouts._icon',['name'=>'chart']) Rekap Transaksi
        </a>
      @endif
    </nav>

    <div class="sb-bot">
      <div class="ucard">
        <div class="av {{ auth()->user()->role }}">{{ auth()->user()->inisial }}</div>
        <div>
          <div class="uname">{{ auth()->user()->nama_lengkap }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Logout
        </button>
      </form>
    </div>
  </aside>

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

</div>

<div id="modal-keluar" class="modal-ov hide" role="dialog" aria-modal="true" aria-labelledby="mk-title">
  <div class="modal" style="width:420px">

    <div style="height:3px;background:linear-gradient(90deg,#ff9900,#ff4444);
                margin:-28px -28px 22px;border-radius:16px 16px 0 0"></div>

    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
      <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,68,68,.12);
                  display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff4444" stroke-width="2">
          <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
      </div>
      <div>
        <div id="mk-title" style="font-size:15px;font-weight:800;margin-bottom:4px">
          Proses Kendaraan Keluar?
        </div>
        <div style="font-size:12px;color:var(--gray2);line-height:1.6">
          Biaya akan dihitung otomatis berdasarkan durasi parkir.
        </div>
      </div>
    </div>

    <div style="background:var(--s2);border-radius:10px;padding:14px;margin-bottom:16px;
                border:1px solid var(--bdr)">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <span style="font-size:11px;color:var(--gray2)">Plat Nomor</span>
        <span id="mk-plat" style="font-size:15px;font-weight:800;letter-spacing:1.5px"></span>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <span style="font-size:11px;color:var(--gray2)">Jenis</span>
        <span id="mk-jenis" class="pill"></span>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <span style="font-size:11px;color:var(--gray2)">Waktu masuk</span>
        <span id="mk-masuk" style="font-size:12px;font-weight:700"></span>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <span style="font-size:11px;color:var(--gray2)">Durasi parkir</span>
        <span id="mk-durasi" style="font-size:12px;font-weight:700;color:var(--ora)"></span>
      </div>

      <div style="background:rgba(137,233,0,.08);border:1px solid rgba(137,233,0,.25);
                  border-radius:8px;padding:10px 12px;
                  display:flex;align-items:center;justify-content:space-between">
        <span style="font-size:11px;color:var(--grn)">Estimasi biaya</span>
        <span id="mk-est" style="font-size:17px;font-weight:800;color:var(--grn)"></span>
      </div>
    </div>

    <form method="POST" id="form-modal-keluar">
      @csrf
      @method('POST')
      <div style="display:flex;gap:8px">
        <button type="button" class="btn btn-out" style="flex:1;justify-content:center"
                onclick="tutupModal('modal-keluar')">
          Batal
        </button>
        <button type="submit" class="btn btn-red" style="flex:1;justify-content:center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Proses Keluar
        </button>
      </div>
    </form>

  </div>
</div>

<div id="modal-hapus" class="modal-ov hide" role="dialog" aria-modal="true" aria-labelledby="mh-title">
  <div class="modal" style="width:400px">

    <div style="height:3px;background:linear-gradient(90deg,#ff4444,#cc0000);
                margin:-28px -28px 22px;border-radius:16px 16px 0 0"></div>

    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
      <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,68,68,.12);
                  display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff4444" stroke-width="2">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
          <path d="M10 11v6M14 11v6"/>
          <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
        </svg>
      </div>
      <div>
        <div id="mh-title" style="font-size:15px;font-weight:800;margin-bottom:4px">
          Hapus Data Ini?
        </div>
        <div style="font-size:12px;color:var(--gray2);line-height:1.6">
          Tindakan ini <strong style="color:var(--red)">tidak bisa dibatalkan</strong>.
          Data yang dihapus tidak dapat dikembalikan.
        </div>
      </div>
    </div>

    <div style="background:var(--s2);border-radius:10px;padding:14px;margin-bottom:16px;
                border:1px solid var(--bdr)">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <span style="font-size:11px;color:var(--gray2)">Tipe data</span>
        <span id="mh-label" style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;
              background:rgba(255,68,68,.12);color:var(--red);border:1px solid rgba(255,68,68,.25)"></span>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:11px;color:var(--gray2)">Yang dihapus</span>
        <span id="mh-nama" style="font-size:13px;font-weight:700;max-width:220px;
              text-align:right;word-break:break-word"></span>
      </div>

      <div id="mh-warn-wrap" style="display:none;margin-top:12px;padding-top:10px;
           border-top:1px solid var(--bdr)">
        <div style="display:flex;align-items:center;gap:7px">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
               stroke="#ff9900" stroke-width="2" style="flex-shrink:0">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
          <span id="mh-warn-text" style="font-size:11px;color:var(--ora)"></span>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button type="button" class="btn btn-out" style="flex:1;justify-content:center"
              onclick="tutupModal('modal-hapus')">
        Batal
      </button>
      <button type="button" class="btn btn-red" id="mh-confirm-btn"
              style="flex:1;justify-content:center">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" style="flex-shrink:0">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
        </svg>
        Ya, Hapus
      </button>
    </div>

  </div>
</div>
<script>
function updateClock() {
  const el = document.getElementById('clk');
  if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
}
updateClock();
setInterval(updateClock, 1000);

function tutupModal(id) {
  const el = document.getElementById(id);
  if (el) el.classList.add('hide');
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.modal-ov').forEach(function (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) overlay.classList.add('hide');
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-ov:not(.hide)').forEach(function (m) {
        m.classList.add('hide');
      });
    }
  });

  document.querySelectorAll('.alert').forEach(function (alert) {
    setTimeout(function () {
      alert.style.transition = 'opacity .4s ease';
      alert.style.opacity    = '0';
      setTimeout(function () { alert.style.display = 'none'; }, 400);
    }, 4000);
  });

  document.querySelectorAll('[data-modal="keluar"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id     = btn.dataset.id;
      const plat   = btn.dataset.plat   || '—';
      const jenis  = btn.dataset.jenis  || '—';
      const pill   = btn.dataset.jenisPill || 'p-grn';
      const masuk  = btn.dataset.masuk  || '—';
      const durasi = btn.dataset.durasi || '—';
      const est    = btn.dataset.est    || 'Dihitung saat keluar';

      document.getElementById('mk-plat').textContent   = plat;
      document.getElementById('mk-masuk').textContent  = masuk;
      document.getElementById('mk-durasi').textContent = durasi;
      document.getElementById('mk-est').textContent    = est;

      const jenisEl = document.getElementById('mk-jenis');
      jenisEl.textContent  = jenis;
      jenisEl.className    = 'pill ' + pill;

      const form = document.getElementById('form-modal-keluar');
      form.action = '/petugas/transaksi/keluar/' + id;

      document.getElementById('modal-keluar').classList.remove('hide');
    });
  });

  document.querySelectorAll('[data-modal="hapus"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const formId = btn.dataset.formId;
      const label  = btn.dataset.label || 'Data';
      const nama   = btn.dataset.nama  || '—';
      const warn   = btn.dataset.warn  || '';

      document.getElementById('mh-label').textContent = label;
      document.getElementById('mh-nama').textContent  = nama;

      const warnWrap = document.getElementById('mh-warn-wrap');
      const warnText = document.getElementById('mh-warn-text');
      if (warn) {
        warnText.textContent  = warn;
        warnWrap.style.display = 'block';
      } else {
        warnWrap.style.display = 'none';
      }

      const confirmBtn = document.getElementById('mh-confirm-btn');
      confirmBtn.onclick = function () {
        const targetForm = document.getElementById(formId);
        if (targetForm) {
          targetForm.submit();
        }
      };

      document.getElementById('modal-hapus').classList.remove('hide');
    });
  });

});
</script>

@stack('scripts')
</body>
</html>