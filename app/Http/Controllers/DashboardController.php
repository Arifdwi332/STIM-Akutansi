<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
  public function index()
{
    $userId = $this->userId; 
    $kasSaldo = DB::table('mst_akun')
        ->where('id', 1)
        ->where('created_by', $userId)   
        ->value('saldo_berjalan');

    return view('dashboard.index', [
        'kasSaldo' => $kasSaldo ?? 0,
    ]);
}

public function getLabaRugi(Request $request)
{
    $q       = trim($request->input('search', ''));
    $page    = max(1, (int) $request->input('page', 1));
    $perPage = min(100, max(1, (int) $request->input('per_page', 20)));
    $userId  = $this->userId;

    // ===== Ambil data dari transaksi
    $trxRows = DB::table('dat_detail_transaksi as ddt')
        ->leftJoin('mst_akun as a1', 'a1.kode_akun', '=', 'ddt.kode_akun')
        ->where('ddt.jenis_laporan', 1)
        ->where('a1.created_by', $userId)
        ->selectRaw("
            ddt.id_detail as id_row,
            'trx' as sumber,
            COALESCE(a1.nama_akun, CAST(ddt.kode_akun AS CHAR)) as nama_akun,
            a1.kategori_akun as kategori_akun,
            ddt.kode_akun as kode_akun,
            ddt.jml_debit  as debet,
            ddt.jml_kredit as kredit
        ")
        ->when($q !== '', function($w) use ($q) {
            $like = '%'.$q.'%';
            $w->where(function($x) use ($like) {
                $x->where('a1.nama_akun', 'like', $like)
                  ->orWhere('ddt.kode_akun', 'like', $like)
                  ->orWhere('a1.kode_akun', 'like', $like);
            });
        })
        ->get();

    // ===== Ambil data dari jurnal
    $jurRows = DB::table('dat_detail_jurnal as ddj')
        ->leftJoin('mst_akun as a2', 'a2.id', '=', 'ddj.id_akun')
        ->where('ddj.jenis_laporan', 1)
        ->where('a2.created_by', $userId)
        ->where('ddj.created_by', $userId)
        ->selectRaw("
            ddj.id_detail as id_row,
            'jur' as sumber,
            COALESCE(a2.nama_akun, CAST(ddj.id_akun AS CHAR)) as nama_akun,
            a2.kategori_akun as kategori_akun,
            a2.kode_akun as kode_akun,
            ddj.jml_debit  as debet,
            ddj.jml_kredit as kredit
        ")
        ->when($q !== '', function($w) use ($q) {
            $like = '%'.$q.'%';
            $w->where(function($x) use ($like) {
                $x->where('a2.nama_akun', 'like', $like)
                  ->orWhere('a2.kode_akun', 'like', $like);
            });
        })
        ->get();

    // ===== Gabungkan & casting angka
    $all = $trxRows->concat($jurRows)->map(function ($r) {
        $r->debet  = (float) $r->debet;
        $r->kredit = (float) $r->kredit;
        return $r;
    });

    // ===== Akumulasi per KODE_AKUN
    $grouped = $all->groupBy('kode_akun')->map(function ($rows, $kode) {
        $first = $rows->first();
        return (object)[
            'id_row'        => null,
            'sumber'        => 'akumulasi',
            'nama_akun'     => $first->nama_akun ?: (string)$kode,
            'kategori_akun' => $first->kategori_akun,
            'kode_akun'     => (string)$kode,
            'debet'         => $rows->sum('debet'),
            'kredit'        => $rows->sum('kredit'),
        ];
    })->values();

    // ===== Filter lagi setelah akumulasi (kalau ada search)
    if ($q !== '') {
        $grouped = $grouped->filter(function($r) use ($q) {
            return mb_stripos((string)$r->nama_akun, $q) !== false
                || mb_stripos((string)$r->kode_akun, $q) !== false;
        })->values();
    }

    // ===== Urutkan: murni berdasarkan KODE_AKUN (numeric)
    $grouped = $grouped->sortBy(function ($r) {
        $kodeNum = (int) preg_replace('/\D/', '', (string) $r->kode_akun);
        return $kodeNum;
    }, SORT_NUMERIC)->values();

    // =========================
    //  HITUNG LABA BERSIH
    //  (SAMA DENGAN LOGIKA JS)
    // =========================
    $items = $grouped->map(function ($r) {
        $debet  = (float) $r->debet;
        $kredit = (float) $r->kredit;
        $kat    = mb_strtolower((string) $r->kategori_akun);
        $nama   = mb_strtolower((string) $r->nama_akun);
        $kode   = (string) $r->kode_akun;

        // klasifikasi jenis akun
        $isPendapatan = ($kat === 'pendapatan');
        $isHpp = (
            $kode === '5104' ||
            (int)$kode === 5104 ||
            preg_match('/hpp|harga pokok/i', $nama)
        );
        $isPenjualan = $isPendapatan && (
            preg_match('/(penjualan|sales)/i', $nama) ||
            preg_match('/^(40|41)\d{2,}$/', $kode)
        );

        if ($isPendapatan) {
            $jenis = $isPenjualan ? 'penjualan' : 'pendapatan_lain';
        } else {
            $jenis = $isHpp ? 'hpp' : 'beban';
        }

        // nilai basis (positif)
        $nilai = in_array($jenis, ['penjualan', 'pendapatan_lain'])
            ? ($kredit - $debet)   // pendapatan
            : ($debet - $kredit);  // HPP / beban

        return (object)[
            'jenis' => $jenis,
            'nilai' => max(0, $nilai),
        ];
    });

    $totalPenjualan    = $items->where('jenis', 'penjualan')->sum('nilai');
    $totalHpp          = $items->where('jenis', 'hpp')->sum('nilai');
    $totalPendLain     = $items->where('jenis', 'pendapatan_lain')->sum('nilai');
    $totalPendapatan   = $totalPenjualan + $totalPendLain - $totalHpp;
    $totalBeban        = $items->where('jenis', 'beban')->sum('nilai');
    $labaBersih        = $totalPendapatan - $totalBeban;

    // ===== Pagination
    $total  = $grouped->count();
    $offset = ($page - 1) * $perPage;
    $rows   = $grouped->slice($offset, $perPage)->values();

    // ===== Response
    return response()->json([
        'ok'              => true,
        'data'            => $rows,
        'total'           => $total,
        'page'            => $page,

        // ringkasan untuk dashboard / keperluan lain
        'total_penjualan'  => $totalPenjualan,
        'total_hpp'        => $totalHpp,
        'total_pend_lain'  => $totalPendLain,
        'total_pendapatan' => $totalPendapatan,
        'total_beban'      => $totalBeban,
        'laba_bersih'      => $labaBersih,
    ]);
}
}
