<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuUtangController extends Controller
{
    /**
     * Halaman single-view (switch utang <-> piutang via JS)
     */
    public function index(Request $request)
    {
        return view('laporan_keuangan.bukuhutang');
    }

    /**
     * JSON: Buku Utang
     * GET /buku_hutang/data
     * Params: search, supplier (kode_pemasok), status, date_from, date_to, page, per_page
     */
    public function data(Request $request)
    {
        $search   = trim((string) $request->query('search', ''));
        $supplier = trim((string) $request->query('supplier', ''));
        $status   = $request->has('status') && $request->status !== '' ? (int) $request->status : null;
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');

        $page     = max(1, (int) $request->query('page', 1));
        $perPage  = max(1, min(100, (int) $request->query('per_page', 20)));
        $offset   = ($page - 1) * $perPage;

        $q = DB::table('dat_utang as u')
            ->leftJoin('dat_pemasok as ps', 'ps.kode_pemasok', '=', 'u.kode_pemasok')
            ->select([
                'u.id_utang',
                'u.kode_pemasok',
                'ps.nama_pemasok',
                'u.no_transaksi',
                'u.nominal',
                'u.status',
                'u.created_by',
                'u.tanggal',
            ])
            ->when($search !== '', function ($w) use ($search) {
                $w->where(function ($x) use ($search) {
                    $x->where('u.kode_pemasok', 'like', "%{$search}%")
                      ->orWhere('u.no_transaksi', 'like', "%{$search}%")
                      ->orWhere('ps.nama_pemasok', 'like', "%{$search}%");
                });
            })
            ->when($supplier !== '', fn($w) => $w->where('u.kode_pemasok', $supplier))
            ->when($status !== null, fn($w) => $w->where('u.status', $status))
            ->when($dateFrom && $dateTo, fn($w) => $w->whereBetween('u.tanggal', [$dateFrom, $dateTo]))
            ->when($dateFrom && !$dateTo, fn($w) => $w->where('u.tanggal', '>=', $dateFrom))
            ->when(!$dateFrom && $dateTo, fn($w) => $w->where('u.tanggal', '<=', $dateTo));

        $total = (clone $q)->count();

        $rows = $q->orderBy('u.tanggal', 'desc')
            ->orderBy('u.id_utang', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function ($r) {
                return [
                    'id_utang'     => (int) $r->id_utang,
                    'kode_pemasok' => (string) $r->kode_pemasok,
                    'nama_pemasok' => (string) ($r->nama_pemasok ?? ''),
                    'no_transaksi' => (string) $r->no_transaksi,
                    'nominal'      => (float) $r->nominal,
                    'status'       => (int) $r->status,
                    'status_label' => ((int) $r->status === 1 ? 'Lunas' : 'Belum Lunas'),
                    'created_by'   => (int) $r->created_by,
                    'tanggal'      => (string) $r->tanggal,
                ];
            });

        return response()->json([
            'ok'   => true,
            'data' => $rows,
            'meta' => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    /**
     * JSON: Buku Piutang
     * GET /buku_piutang/datapiutang
     * Params: search, pelanggan (id_pelanggan), status, date_from, date_to, page, per_page
     */
    public function dataPiutang(Request $request)
    {
        $search    = trim((string) $request->query('search', ''));
        $pelanggan = $request->query('pelanggan'); // id_pelanggan
        $status    = $request->has('status') && $request->status !== '' ? (int) $request->status : null;
        $dateFrom  = $request->query('date_from');
        $dateTo    = $request->query('date_to');

        $page     = max(1, (int) $request->query('page', 1));
        $perPage  = max(1, min(100, (int) $request->query('per_page', 20)));
        $offset   = ($page - 1) * $perPage;

        $q = DB::table('dat_piutang as p')
            ->leftJoin('dat_pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->select([
                'p.id_piutang',
                'p.id_pelanggan',
                'pl.nama_pelanggan',
                'p.no_transaksi',
                'p.nominal',
                'p.status',
                'p.created_by',
                'p.tanggal',
            ])
            ->when($search !== '', function ($w) use ($search) {
                $w->where(function ($x) use ($search) {
                    $x->where('p.no_transaksi', 'like', "%{$search}%")
                      ->orWhere('pl.nama_pelanggan', 'like', "%{$search}%");
                });
            })
            ->when($pelanggan !== null && $pelanggan !== '', fn($w) => $w->where('p.id_pelanggan', (int) $pelanggan))
            ->when($status !== null, fn($w) => $w->where('p.status', $status))
            ->when($dateFrom && $dateTo, fn($w) => $w->whereBetween('p.tanggal', [$dateFrom, $dateTo]))
            ->when($dateFrom && !$dateTo, fn($w) => $w->where('p.tanggal', '>=', $dateFrom))
            ->when(!$dateFrom && $dateTo, fn($w) => $w->where('p.tanggal', '<=', $dateTo));

        $total = (clone $q)->count();

        $rows = $q->orderBy('p.tanggal', 'desc')
            ->orderBy('p.id_piutang', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function ($r) {
                return [
                    'id_piutang'     => (int) $r->id_piutang,
                    'id_pelanggan'   => (int) $r->id_pelanggan,
                    'nama_pelanggan' => (string) ($r->nama_pelanggan ?? ''),
                    'no_transaksi'   => (string) $r->no_transaksi,
                    'nominal'        => (float) $r->nominal,
                    'status'         => (int) $r->status,
                    'status_label'   => ((int) $r->status === 1 ? 'Lunas' : 'Belum Lunas'),
                    'created_by'     => (int) $r->created_by,
                    'tanggal'        => (string) $r->tanggal,
                ];
            });

        return response()->json([
            'ok'   => true,
            'data' => $rows,
            'meta' => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    /**
     * JSON: Referensi dropdown pemasok (untuk Buku Utang)
     * GET /buku_hutang/ref/pemasok
     * Optional: q (keyword)
     */
    public function refPemasok(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rows = DB::table('dat_pemasok')
            ->select('kode_pemasok', 'nama_pemasok')
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('nama_pemasok', 'like', "%{$q}%")
                      ->orWhere('kode_pemasok', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama_pemasok')
            ->limit(200)
            ->get();

        return response()->json(['ok' => true, 'data' => $rows]);
    }

    /**
     * JSON: Referensi dropdown pelanggan (untuk Buku Piutang)
     * GET /buku_piutang/ref/pelanggan
     * Optional: q (keyword)
     */
    public function refPelanggan(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rows = DB::table('dat_pelanggan')
            ->select('id_pelanggan', 'nama_pelanggan')
            ->when($q !== '', function ($w) use ($q) {
                $w->where('nama_pelanggan', 'like', "%{$q}%");
            })
            ->orderBy('nama_pelanggan')
            ->limit(200)
            ->get();

        return response()->json(['ok' => true, 'data' => $rows]);
    }
}
