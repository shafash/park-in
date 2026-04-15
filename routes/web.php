<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RegistrasiController;
use App\Http\Controllers\Admin\TarifController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\KendaraanController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Petugas\TransaksiController;
use App\Http\Controllers\Petugas\StrukController;
use App\Http\Controllers\Owner\RekapController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', fn() => redirect()->route('admin.registrasi.index'));

    // Registrasi User
    Route::get('/registrasi',          [RegistrasiController::class, 'index'])->name('registrasi.index');
    Route::post('/registrasi',         [RegistrasiController::class, 'store'])->name('registrasi.store');
    Route::put('/registrasi/{user}',   [RegistrasiController::class, 'update'])->name('registrasi.update');
    Route::delete('/registrasi/{user}',[RegistrasiController::class, 'destroy'])->name('registrasi.destroy');

    // CRUD Tarif
    Route::get('/tarif',           [TarifController::class, 'index'])->name('tarif.index');
    Route::post('/tarif',          [TarifController::class, 'store'])->name('tarif.store');
    Route::put('/tarif/{tarif}',   [TarifController::class, 'update'])->name('tarif.update');
    Route::delete('/tarif/{tarif}',[TarifController::class, 'destroy'])->name('tarif.destroy');

    // CRUD Area Parkir
    Route::get('/area',          [AreaController::class, 'index'])->name('area.index');
    Route::post('/area',         [AreaController::class, 'store'])->name('area.store');
    Route::put('/area/{area}',   [AreaController::class, 'update'])->name('area.update');
    Route::delete('/area/{area}',[AreaController::class, 'destroy'])->name('area.destroy');

    // CRUD Kendaraan
    Route::get('/kendaraan',               [KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::post('/kendaraan',              [KendaraanController::class, 'store'])->name('kendaraan.store');
    Route::put('/kendaraan/{kendaraan}',   [KendaraanController::class, 'update'])->name('kendaraan.update');
    Route::delete('/kendaraan/{kendaraan}',[KendaraanController::class, 'destroy'])->name('kendaraan.destroy');

    // Log Aktivitas
    Route::get('/log',           [LogController::class, 'index'])->name('log.index');
    Route::get('/log/export',    [LogController::class, 'export'])->name('log.export');
});

/*
|--------------------------------------------------------------------------
| Petugas Routes
|--------------------------------------------------------------------------
*/
Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/', fn() => redirect()->route('petugas.transaksi.index'));

    // AJAX: cari kendaraan by plat
    Route::get('/transaksi/cari-plat', [TransaksiController::class, 'cariPlat'])->name('transaksi.cari-plat');

    // Transaksi + SSP
    Route::get('/transaksi',              [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/masuk',        [TransaksiController::class, 'masukForm'])->name('transaksi.masuk');
    Route::post('/transaksi/masuk',       [TransaksiController::class, 'masukStore'])->name('transaksi.masuk.store');
    Route::get('/transaksi/keluar/{id}',  [TransaksiController::class, 'keluarForm'])->name('transaksi.keluar');
    Route::post('/transaksi/keluar/{id}', [TransaksiController::class, 'keluarStore'])->name('transaksi.keluar.store');

    // Cetak Struk
    Route::get('/struk',       [StrukController::class, 'index'])->name('struk.index');
    Route::get('/struk/{id}',  [StrukController::class, 'show'])->name('struk.show');
    Route::get('/struk/{id}/print', [StrukController::class, 'print'])->name('struk.print');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', fn() => redirect()->route('owner.rekap.index'));
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
});
