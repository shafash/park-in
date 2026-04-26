<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Park In — Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="login-pg">
  <div class="login-card">
    <div class="login-logo">Park <span style="color:var(--grn)">In</span></div>
    <div class="login-sub">Sistem Manajemen Parkir</div>

    @if($errors->any())
      <div class="alert a-err">{{ $errors->first() }}</div>
    @endif
    @if(session('error'))
      <div class="alert a-err">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="fg">
        <label>Username</label>
        <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
      </div>
      <div class="fg">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-grn" style="width:100%;justify-content:center;padding:13px;font-size:15px;margin-top:6px">
        Masuk ke Sistem
      </button>
    </form>

  </div>
</div>
</body>
</html>
