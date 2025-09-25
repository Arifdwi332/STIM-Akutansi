<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\LaporanKeuanganController;
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
Route::post('/buku_besar/storetransaksi', [BukuBesarController::class, 'storetransaksi'])->name('transaksi.store');


Route::get('buku_besar/get_jurnal',     [BukuBesarController::class, 'getJurnal'])->name('getJurnal');
Route::get('buku_besar/get_buku_besar', [BukuBesarController::class, 'getBukuBesar'])->name('getBukuBesar');



Route::prefix('inventaris')->name('inventaris.')->group(function () {
    Route::get('/',                [InventarisController::class, 'index'])->name('index');

    Route::get('/barang',          [InventarisController::class, 'barangList'])->name('barang');      
    Route::get('/pelanggan',       [InventarisController::class, 'pelangganList'])->name('pelanggan');
    Route::get('/next_no',         [InventarisController::class, 'nextNo'])->name('next_no');         

    Route::post('/store',          [InventarisController::class, 'store'])->name('store');     
    Route::post('/pelanggan/store', [InventarisController::class, 'storePelanggan'])->name('pelanggan.store');
    Route::post('/pemasok/store', [InventarisController::class, 'storePemasok'])->name('pemasok.store');   
    Route::get('/parties', [InventarisController::class, 'getParties'])->name('parties');
    Route::get('/barang', [InventarisController::class, 'barangList'])->name('barang');
    Route::get('/transaksi', [InventarisController::class, 'datatableTransaksi'])
    ->name('dt.transaksi');

    Route::get('/inventaris', [InventarisController::class, 'datatableInventaris'])->name('dt.inventaris');
    Route::get('/barang-by-pemasok', [InventarisController::class, 'getBarangByPemasok'])->name('barangByPemasok');
    Route::get('/barang-semua', [InventarisController::class, 'getBarangSemua'])->name('barangSemua');
});

Route::prefix('laporan_keuangan')->name('laporan_keuangan.')->group(function () {
    Route::get('/', [LaporanKeuanganController::class, 'index'])->name('index');
    Route::get('/get_laba_rugi', [LaporanKeuanganController::class, 'getLabaRugi'])->name('get_laba_rugi');

    Route::get('/get_neraca', [LaporanKeuanganController::class, 'getNeraca'])->name('get_neraca');
});


