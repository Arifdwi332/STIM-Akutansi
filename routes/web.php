<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuBesarController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});



Route::get('/buku_besar', [BukuBesarController::class, 'index'])->name('buku_besar.index');

Route::get('/buku_besar/mst_akun', [BukuBesarController::class, 'listMstAkun'])->name('buku_besar.mst_akun');

Route::post('/buku_besar/sub_akun', [BukuBesarController::class, 'storeSubAkun'])->name('buku_besar.sub_akun');

Route::get('/buku_besar/jurnal', [BukuBesarController::class,'jurnalData'])->name('buku_besar.jurnal');
Route::get('/buku_besar/detail', [BukuBesarController::class,'bukuBesarData'])->name('buku_besar.detail');

Route::post('/storeakun', [BukuBesarController::class, 'storeakun'])->name('mst_akun.store');
Route::get('/buku_besar/list_akun_flat', [BukuBesarController::class, 'listAkunFlat'])->name('buku_besar.list_akun_flat');
Route::get('/buku_besar/sub_akun_list', [BukuBesarController::class, 'subAkunList'])->name('buku_besar.sub_akun_list');
Route::post('/buku_besar/saldo-awal', [BukuBesarController::class, 'storeSaldoAwal'])->name('buku_besar.saldo_awal.store');
