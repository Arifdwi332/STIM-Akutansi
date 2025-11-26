<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\FakturController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BepController;
use App\Http\Controllers\BukuUtangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PPHController;
use Illuminate\Support\Facades\Auth;
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





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ================== AUTH (TANPA MIDDLEWARE) ==================

// register tetap bebas diakses
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('mstuser.register');
Route::post('/register', [AuthController::class, 'register'])->name('mstuser.register.store');

// [change] route login harus bernama "login" supaya middleware auth redirect ke sini
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('mstuser.login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// ================== PROTECTED ROUTES (BUTUH LOGIN) ==================
// [change] semua route di bawah ini hanya bisa diakses jika sudah login
Route::middleware('umkm.auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

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

    Route::get('buku_besar/get_jurnal', [BukuBesarController::class, 'getJurnal'])->name('getJurnal');
    Route::get('buku_besar/get_buku_besar', [BukuBesarController::class, 'getBukuBesar'])->name('getBukuBesar');
    Route::get('buku_besar/list_pemasok', [BukuBesarController::class, 'listPemasok'])->name('listPemasok');
    Route::get('buku_besar/list_pelanggan', [BukuBesarController::class, 'listPelanggan'])->name('listPelanggan');
    Route::post('buku_besar/store_pemasok', [BukuBesarController::class, 'storePemasok'])->name('storePersediaan');
    Route::post('/buku-besar/reset-data', [BukuBesarController::class, 'resetData'])->name('buku-besar.reset-data');
    Route::post('/buku-besar/reset-transaksi', [BukuBesarController::class, 'resetTransaksi'])->name('buku-besar.reset-transaksi');

    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('index');
        Route::get('/barang', [TransaksiController::class, 'barangList'])->name('barang');      
        Route::get('/pelanggan', [TransaksiController::class, 'pelangganList'])->name('pelanggan');
        Route::get('/next_no', [TransaksiController::class, 'nextNo'])->name('next_no');         

        Route::post('/store', [TransaksiController::class, 'store'])->name('store');     
        Route::post('/pelanggan/store', [TransaksiController::class, 'storePelanggan'])->name('pelanggan.store');
        Route::post('/pemasok/store', [TransaksiController::class, 'storePemasok'])->name('pemasok.store');   
        Route::post('/barang/store', [TransaksiController::class, 'storeBarang'])->name('barang.store');  
        Route::get('/parties', [TransaksiController::class, 'getParties'])->name('parties');
        Route::get('/barang', [TransaksiController::class, 'barangList'])->name('barang');
        Route::get('/transaksi', [TransaksiController::class, 'datatableTransaksi'])->name('dt.transaksi');
        Route::get('/utang/suppliers',    [TransaksiController::class, 'suppliers']);
        Route::get('/utang/by-supplier', [TransaksiController::class, 'bySupplier'])->name('utang');
        Route::get('/utang/nominal', [TransaksiController::class, 'nominal'])->name('nominal');

        Route::get('/piutang/customers', [TransaksiController::class, 'customers']);
        Route::get('/piutang/by-customer', [TransaksiController::class, 'byCustomer']);
        Route::get('/piutang/nominalpiutang', [TransaksiController::class, 'nominalpiutang']);

        Route::get('/inventaris', [TransaksiController::class, 'datatableInventaris'])->name('dt.inventaris');
        Route::get('/barang-by-pemasok', [TransaksiController::class, 'getBarangByPemasok'])->name('barangByPemasok');
        Route::get('/barang-semua', [TransaksiController::class, 'getBarangSemua'])->name('barangSemua');
        Route::get('/kasbank', [TransaksiController::class, 'kasbank'])->name('kasbank');
        Route::post('/inventaris/update-barang', [TransaksiController::class, 'updateBarang'])->name('updateBarang');
    });

    Route::prefix('laporan_keuangan')->name('laporan_keuangan.')->group(function () {
        Route::get('/', [LaporanKeuanganController::class, 'index'])->name('index');
        Route::get('/get_laba_rugi',[LaporanKeuanganController::class, 'getLabaRugi'])->name('get_laba_rugi');
        Route::get('/get_neraca', [LaporanKeuanganController::class, 'getNeraca'])->name('get_neraca');
        Route::get('/bukbes', [LaporanKeuanganController::class, 'bukbes'])->name('bukbes');
        Route::get('/jurnal', [LaporanKeuanganController::class, 'jurnal'])->name('jurnal');
    });

    Route::prefix('faktur')->name('faktur.')->group(function () {
        Route::get('/', [FakturController::class, 'index'])->name('index');
        Route::get('/dt/transaksi', [FakturController::class, 'datatableTransaksi'])->name('dt.transaksi');
        Route::get('/{no}/cetak', [FakturController::class, 'print'])->name('cetak');
        Route::get('/{no}/export/pdf', [FakturController::class, 'exportPdf'])->name('export.pdf');
    });

    Route::prefix('bep')->name('bep.')->group(function () {
        Route::get('/', [BepController::class, 'index'])->name('index');
    });

    Route::get('/buku_hutang', [BukuUtangController::class, 'index'])->name('buku_hutang.index');

    // JSON: Buku Utang
    Route::prefix('buku_hutang')->group(function () {
        Route::get('/data', [BukuUtangController::class, 'data'])->name('buku_hutang.data');
        Route::get('/ref/pemasok', [BukuUtangController::class, 'refPemasok'])->name('buku_hutang.ref_pemasok');
    });

    // JSON: Buku Piutang
    Route::prefix('buku_piutang')->group(function () {
        Route::get('/datapiutang', [BukuUtangController::class, 'dataPiutang'])->name('buku_piutang.data');
        Route::get('/ref/pelanggan', [BukuUtangController::class, 'refPelanggan'])->name('buku_piutang.ref_pelanggan');
    });

    // PPH
    Route::prefix('pph')->group(function () {
        Route::get('/', [PPHController::class, 'index'])->name('pph.index');
    });

}); // end middleware auth

// Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Route::get('/buku_besar', [BukuBesarController::class, 'index'])->name('buku_besar.index');
// Route::get('/buku_besar/mst_akun', [BukuBesarController::class, 'listMstAkun'])->name('buku_besar.mst_akun');
// Route::post('/buku_besar/sub_akun', [BukuBesarController::class, 'storeSubAkun'])->name('buku_besar.sub_akun');
// Route::get('/buku_besar/jurnal', [BukuBesarController::class,'jurnalData'])->name('buku_besar.jurnal');
// Route::get('/buku_besar/detail', [BukuBesarController::class,'bukuBesarData'])->name('buku_besar.detail');

// Route::post('/storeakun', [BukuBesarController::class, 'storeakun'])->name('mst_akun.store');
// Route::get('/buku_besar/list_akun_flat', [BukuBesarController::class, 'listAkunFlat'])->name('buku_besar.list_akun_flat');
// Route::get('/buku_besar/sub_akun_list', [BukuBesarController::class, 'subAkunList'])->name('buku_besar.sub_akun_list');
// Route::post('/buku_besar/saldo-awal', [BukuBesarController::class, 'storeSaldoAwal'])->name('buku_besar.saldo_awal.store');
// Route::post('/buku_besar/storetransaksi', [BukuBesarController::class, 'storetransaksi'])->name('transaksi.store');


// Route::get('buku_besar/get_jurnal', [BukuBesarController::class, 'getJurnal'])->name('getJurnal');
// Route::get('buku_besar/get_buku_besar', [BukuBesarController::class, 'getBukuBesar'])->name('getBukuBesar');
// Route::get('buku_besar/list_pemasok', [BukuBesarController::class, 'listPemasok'])->name('listPemasok');
// Route::get('buku_besar/list_pelanggan', [BukuBesarController::class, 'listPelanggan'])->name('listPelanggan');
// Route::post('buku_besar/store_pemasok', [BukuBesarController::class, 'storePemasok'])->name('storePersediaan');
// Route::post('/buku-besar/reset-data', [BukuBesarController::class, 'resetData'])->name('buku-besar.reset-data');
// Route::post('/buku-besar/reset-transaksi', [BukuBesarController::class, 'resetTransaksi'])->name('buku-besar.reset-transaksi');

// Route::prefix('inventaris')->name('inventaris.')->group(function () {
//     Route::get('/', [TransaksiController::class, 'index'])->name('index');
//     Route::get('/barang', [TransaksiController::class, 'barangList'])->name('barang');      
//     Route::get('/pelanggan', [TransaksiController::class, 'pelangganList'])->name('pelanggan');
//     Route::get('/next_no', [TransaksiController::class, 'nextNo'])->name('next_no');         

//     Route::post('/store', [TransaksiController::class, 'store'])->name('store');     
//     Route::post('/pelanggan/store', [TransaksiController::class, 'storePelanggan'])->name('pelanggan.store');
//     Route::post('/pemasok/store', [TransaksiController::class, 'storePemasok'])->name('pemasok.store');   
//     Route::post('/barang/store', [TransaksiController::class, 'storeBarang'])->name('barang.store');  
//     Route::get('/parties', [TransaksiController::class, 'getParties'])->name('parties');
//     Route::get('/barang', [TransaksiController::class, 'barangList'])->name('barang');
//     Route::get('/transaksi', [TransaksiController::class, 'datatableTransaksi'])->name('dt.transaksi');;
//     Route::get('/utang/suppliers',    [TransaksiController::class, 'suppliers']);
//     Route::get('/utang/by-supplier', [TransaksiController::class, 'bySupplier'])->name('utang');
//     Route::get('/utang/nominal', [TransaksiController::class, 'nominal'])->name('nominal');

//     Route::get('/piutang/customers', [TransaksiController::class, 'customers']);
//     Route::get('/piutang/by-customer', [TransaksiController::class, 'byCustomer']);
//     Route::get('/piutang/nominalpiutang', [TransaksiController::class, 'nominalpiutang']);


//     Route::get('/inventaris', [TransaksiController::class, 'datatableInventaris'])->name('dt.inventaris');
//     Route::get('/barang-by-pemasok', [TransaksiController::class, 'getBarangByPemasok'])->name('barangByPemasok');
//     Route::get('/barang-semua', [TransaksiController::class, 'getBarangSemua'])->name('barangSemua');
//     Route::get('/kasbank', [TransaksiController::class, 'kasbank'])->name('kasbank');
//     Route::post('/inventaris/update-barang', [TransaksiController::class, 'updateBarang'])->name('updateBarang');

// });

// Route::prefix('laporan_keuangan')->name('laporan_keuangan.')->group(function () {
//     Route::get('/', [LaporanKeuanganController::class, 'index'])->name('index');
//     Route::get('/get_laba_rugi',[LaporanKeuanganController::class, 'getLabaRugi'])->name('get_laba_rugi');

//     Route::get('/get_neraca', [LaporanKeuanganController::class, 'getNeraca'])->name('get_neraca');
//     Route::get('/bukbes', [LaporanKeuanganController::class, 'bukbes'])->name('bukbes');
//     Route::get('/jurnal', [LaporanKeuanganController::class, 'jurnal'])->name('jurnal');
// });

// Route::prefix('faktur')->name('faktur.')->group(function () {
//     Route::get('/', [FakturController::class, 'index'])->name('index');

//     Route::get('/dt/transaksi', [FakturController::class, 'datatableTransaksi'])->name('dt.transaksi');

//     Route::get('/{no}/cetak', [FakturController::class, 'print'])->name('cetak');
//     Route::get('/{no}/export/pdf', [FakturController::class, 'exportPdf'])->name('export.pdf');
// });

// Route::prefix('bep')->name('bep.')->group(function () {
//     Route::get('/', [BepController::class, 'index'])->name('index');
// });


// Route::get('/buku_hutang', [BukuUtangController::class, 'index'])->name('buku_hutang.index');

// // JSON: Buku Utang
// Route::prefix('buku_hutang')->group(function () {
//     Route::get('/data', [BukuUtangController::class, 'data'])->name('buku_hutang.data');
//     // referensi dropdown pemasok
//     Route::get('/ref/pemasok', [BukuUtangController::class, 'refPemasok'])->name('buku_hutang.ref_pemasok');
// });

// // JSON: Buku Piutang
// Route::prefix('buku_piutang')->group(function () {
//     Route::get('/datapiutang', [BukuUtangController::class, 'dataPiutang'])->name('buku_piutang.data');
//     // referensi dropdown pelanggan
//     Route::get('/ref/pelanggan', [BukuUtangController::class, 'refPelanggan'])->name('buku_piutang.ref_pelanggan');
// });

// Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('mstuser.register');
// Route::post('/register', [AuthController::class, 'register'])->name('mstuser.register.store');

// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('mstuser.login');
// Route::post('/login', [AuthController::class, 'login'])->name('mstuser.login.process');

