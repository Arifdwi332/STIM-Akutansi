<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    public function index()
    {
        return view('laporan_keuangan.index');
    }
public function getLabaRugi(Request $request)
{
    $q       = trim($request->input('search', ''));
    $page    = max(1, (int) $request->input('page', 1));
    $perPage = min(100, max(1, (int) $request->input('per_page', 20)));

   
    $trxRows = DB::table('dat_detail_transaksi as ddt')
        ->leftJoin('mst_akun as a1', 'a1.kode_akun', '=', 'ddt.kode_akun')
        ->where('ddt.jenis_laporan', 1) 
        ->selectRaw("
            ddt.id_detail as id_row,
            'trx' as sumber,
            COALESCE(a1.nama_akun, CAST(ddt.kode_akun AS CHAR)) as nama_akun,
            ddt.jml_debit  as debet,
            ddt.jml_kredit as kredit
        ")
        ->when($q !== '', function($w) use ($q) {
            $like = '%'.$q.'%';
            $w->where(function($x) use ($like) {
                $x->where('a1.nama_akun', 'like', $like)
                  ->orWhere('ddt.kode_akun', 'like', $like);
            });
        })
        ->orderBy('ddt.id_detail')
        ->get();

    $jurRows = DB::table('dat_detail_jurnal as ddj')
        ->leftJoin('mst_akun as a2', 'a2.id', '=', 'ddj.id_akun')
        ->where('ddj.jenis_laporan', 1)
        ->selectRaw("
            ddj.id_detail as id_row,
            'jur' as sumber,
            COALESCE(a2.nama_akun, CAST(ddj.id_akun AS CHAR)) as nama_akun,
            ddj.jml_debit  as debet,
            ddj.jml_kredit as kredit
        ")
        ->when($q !== '', function($w) use ($q) {
            $like = '%'.$q.'%';
            $w->where(function($x) use ($like) {
                $x->where('a2.nama_akun', 'like', $like);
            });
        })
        ->orderBy('ddj.id_detail')
        ->get();

    $all = $trxRows->concat($jurRows)
        ->map(function ($r) {
            $r->debet  = (float) $r->debet;
            $r->kredit = (float) $r->kredit;
            return $r;
        })
        ->sortBy([['sumber', 'asc'], ['id_row', 'asc']])
        ->values();

    $total  = $all->count();
    $offset = ($page - 1) * $perPage;
    $rows   = $all->slice($offset, $perPage)->values();

    return response()->json([
        'ok'    => true,
        'data'  => $rows,
        'total' => $total,
        'page'  => $page,
    ]);
}


public function getNeraca(Request $request)
{
    $q       = trim($request->input('search', ''));
    $page    = max(1, (int) $request->input('page', 1));
    $perPage = min(100, max(1, (int) $request->input('per_page', 20)));

   
    $trxRows = DB::table('dat_detail_transaksi as ddt')
        ->leftJoin('mst_akun as a1', 'a1.kode_akun', '=', 'ddt.kode_akun')
        ->where('ddt.jenis_laporan', 2)
        ->selectRaw("
            ddt.id_detail as id_row,
            'trx' as sumber,
            COALESCE(a1.nama_akun, CAST(ddt.kode_akun AS CHAR)) as nama_akun,
            ddt.jml_debit  as debet,
            ddt.jml_kredit as kredit
        ")
        ->when($q !== '', function($w) use ($q) {
            $like = '%'.$q.'%';
            $w->where(function($x) use ($like) {
                $x->where('a1.nama_akun', 'like', $like)
                  ->orWhere('ddt.kode_akun', 'like', $like);
            });
        })
        ->orderBy('ddt.id_detail')
        ->get();

    $jurRows = DB::table('dat_detail_jurnal as ddj')
        ->leftJoin('mst_akun as a2', 'a2.id', '=', 'ddj.id_akun')
        ->where('ddj.jenis_laporan', 2) 
        ->selectRaw("
            ddj.id_detail as id_row,
            'jur' as sumber,
            COALESCE(a2.nama_akun, CAST(ddj.id_akun AS CHAR)) as nama_akun,
            ddj.jml_debit  as debet,
            ddj.jml_kredit as kredit
        ")
        ->when($q !== '', function($w) use ($q) {
            $like = '%'.$q.'%';
            $w->where(function($x) use ($like) {
                $x->where('a2.nama_akun', 'like', $like);
            });
        })
        ->orderBy('ddj.id_detail')
        ->get();

    $all = $trxRows->concat($jurRows)
        ->map(function ($r) {
            $r->debet  = (float) $r->debet;
            $r->kredit = (float) $r->kredit;
            return $r;
        })
        ->sortBy([['sumber', 'asc'], ['id_row', 'asc']])
        ->values();

    $total  = $all->count();
    $offset = ($page - 1) * $perPage;
    $rows   = $all->slice($offset, $perPage)->values();

    return response()->json([
        'ok'    => true,
        'data'  => $rows,
        'total' => $total,
        'page'  => $page,
    ]);
}

}
