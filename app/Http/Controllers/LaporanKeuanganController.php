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

    // Ambil data dari transaksi
    $trxRows = DB::table('dat_detail_transaksi as ddt')
        ->leftJoin('mst_akun as a1', 'a1.kode_akun', '=', 'ddt.kode_akun')
        ->where('ddt.jenis_laporan', 1)
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
                  ->orWhere('ddt.kode_akun', 'like', $like);
            });
        })
        ->get();

    // Ambil data dari jurnal
    $jurRows = DB::table('dat_detail_jurnal as ddj')
        ->leftJoin('mst_akun as a2', 'a2.id', '=', 'ddj.id_akun')
        ->where('ddj.jenis_laporan', 1)
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
                $x->where('a2.nama_akun', 'like', $like);
            });
        })
        ->get();

    // Gabungkan data transaksi dan jurnal
    $all = $trxRows->concat($jurRows)
        ->map(function ($r) {
            $r->debet  = (float) $r->debet;
            $r->kredit = (float) $r->kredit;
            return $r;
        });

    // 🔹 Akumulasi khusus untuk kode_akun = 6101
    $akun6101 = $all->where('kode_akun', '6101');
    $sum6101 = null;
    if ($akun6101->isNotEmpty()) {
        $first = $akun6101->first();
        $sum6101 = (object)[
            'id_row'        => null,
            'sumber'        => 'akumulasi',
            'nama_akun'     => $first->nama_akun, // pakai nama asli akun
            'kategori_akun' => $first->kategori_akun,
            'kode_akun'     => '6101',
            'debet'         => $akun6101->sum('debet'),
            'kredit'        => $akun6101->sum('kredit'),
        ];
    }

    // 🔹 Gabungkan ulang data selain kode_akun 6101 + hasil akumulasinya
    $all = $all
        ->reject(fn($r) => $r->kode_akun === '6101')
        ->when($sum6101, fn($col) => $col->push($sum6101))
        ->sortBy([
            ['kategori_akun', fn($v) => $v !== 'Pendapatan'],
            ['nama_akun', 'asc']
        ])
        ->values();

    // Pagination
    $total  = $all->count();
    $offset = ($page - 1) * $perPage;
    $rows   = $all->slice($offset, $perPage)->values();

    // Response JSON
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

        // Ambil data langsung dari model
        $query = MstAkunModel::query()
            ->select('id', 'nama_akun', 'kategori_akun', 'saldo_berjalan')
            ->when($q !== '', function ($w) use ($q) {
                $like = '%'.$q.'%';
                $w->where('nama_akun', 'like', $like)
                ->orWhere('kategori_akun', 'like', $like);
            })
            ->orderBy('kategori_akun')
            ->orderBy('nama_akun');

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
