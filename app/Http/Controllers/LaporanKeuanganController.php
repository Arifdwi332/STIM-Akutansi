<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MstAkunModel;

class LaporanKeuanganController extends Controller
{
    public function index()
    {
        return view('laporan_keuangan.index');
    }
      public function bukbes()
    {
        return view('laporan_keuangan.bukbes');
    }
       public function jurnal()
    {
        return view('laporan_keuangan.jurnal');
    }
 public function getLabaRugi(Request $request)
{
    $q       = trim($request->input('search', ''));
    $page    = max(1, (int) $request->input('page', 1));
    $perPage = min(100, max(1, (int) $request->input('per_page', 20)));
    $userId = $this->userId;

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
                  // [changes] juga boleh cari lewat a1.kode_akun
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
                  // [changes] support cari kode akun juga
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

    // ===== [changes] Akumulasi per KODE_AKUN (nama akun muncul sekali)
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

    // ===== [changes] (opsional) filter lagi setelah akumulasi biar konsisten
    if ($q !== '') {
        $grouped = $grouped->filter(function($r) use ($q) {
            return mb_stripos((string)$r->nama_akun, $q) !== false
                || mb_stripos((string)$r->kode_akun, $q) !== false;
        })->values();
    }

    // ===== [changes] Urutkan: Pendapatan dulu, lalu nama akun
    $grouped = $grouped->sortBy([
        fn($r) => $r->kategori_akun !== 'Pendapatan',
        'nama_akun'
    ])->values();

    // ===== Pagination
    $total  = $grouped->count();
    $offset = ($page - 1) * $perPage;
    $rows   = $grouped->slice($offset, $perPage)->values();

    // ===== Response
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
        $userId = $this->userId;
        
        $query = MstAkunModel::query()
            ->select('id', 'nama_akun', 'kategori_akun', 'saldo_berjalan', 'kode_akun')
            ->where('created_by', $userId)
            ->when($q !== '', function ($w) use ($q) {
                $like = '%'.$q.'%';
                $w->where('nama_akun', 'like', $like)
                ->orWhere('kategori_akun', 'like', $like);
            })
            ->orderBy('kategori_akun')
            ->orderBy('kode_akun', 'asc');

        $all = $query->get()
            ->map(function ($r) {
                return [
                    'id'            => $r->id,
                    'nama_akun'     => $r->nama_akun,
                    'kategori_akun' => $r->kategori_akun,
                    'saldo'         => (float) $r->saldo_berjalan,
                ];
            });

        // Pisahkan berdasarkan kategori utama
        $grouped = [
            'aset'       => $all->filter(fn($r) => str_contains(strtolower($r['kategori_akun']), 'aset'))->values(),
            'liabilitas' => $all->filter(fn($r) => str_contains(strtolower($r['kategori_akun']), 'liabilitas'))->values(),
            'ekuitas'    => $all->filter(fn($r) => str_contains(strtolower($r['kategori_akun']), 'ekuitas'))->values(),
        ];

        $total  = $all->count();
        $offset = ($page - 1) * $perPage;

        // Slice hasil per page
        $rows = $all->slice($offset, $perPage)->values();

        return response()->json([
            'ok'    => true,
            'data'  => $grouped,
            'total' => $total,
            'page'  => $page,
        ]);
    }

}
