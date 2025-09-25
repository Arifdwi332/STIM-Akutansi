<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\PelangganModel;
use App\Models\PemasokModel;
use App\Models\MstAkunModel;

use App\Http\Controllers\JsonResponse;
use App\Models\DatBarangModel;
use Carbon\Carbon;  

class InventarisController extends Controller
{
    public function index()
    {
        return view('inventaris.index'); 
    }
  
    public function pelangganList()
    {
        $rows = DB::table('mst_pelanggan')
            ->select('id', 'nama')
            ->where('status_aktif', 1)
            ->orderBy('nama')
            ->get();

        return response()->json($rows);
    }

    
   
     public function storePelanggan(Request $request)
    {
        $rules = [
            'nama_pelanggan' => 'required|string|max:150',
            'alamat'         => 'nullable|string',
            'no_telp'        => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'npwp'           => 'nullable|string|max:50',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'ok'      => false,
                'message' => $v->errors()->first(),
            ], 422);
        }

        $row = PelangganModel::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat'         => $request->alamat,
            'no_hp'          => $request->no_telp,   
            'email'          => $request->email,
            'npwp'           => $request->npwp,
            'saldo_piutang'  => 0,
        ]);

        return response()->json([
            'ok'   => true,
            'data' => $row,
        ]);
    }

    public function storePemasok(Request $request)
    {
        $rules = [
            'nama_pemasok' => 'required|string|max:150',
            'alamat'       => 'nullable|string',
            'no_hp'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:150',
            'npwp'         => 'nullable|string|max:50',
            'nama_barang'  => 'required|string|max:150',
            'satuan_ukur'  => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0', 
            'harga_jual'   => 'required|numeric|min:0',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'ok'      => false,
                'message' => $v->errors()->first(),
            ], 422);
        }

        $lastPemasok = PemasokModel::orderBy('id_pemasok', 'desc')->first();
        $nextNumber = $lastPemasok ? ((int)substr($lastPemasok->kode_pemasok, 3)) + 1 : 1;
        $kodePemasok = 'SUP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $pemasok = PemasokModel::create([
            'kode_pemasok' => $kodePemasok,
            'nama_pemasok' => $request->nama_pemasok,
            'alamat'       => $request->alamat,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'npwp'         => $request->npwp,
            'saldo_utang'  => 0,
        ]);

        $barang = DatBarangModel::create([
            'kode_pemasok' => $pemasok->kode_pemasok, 
            'nama_barang'  => $request->nama_barang,
            'satuan_ukur'  => $request->satuan_ukur,
            'hpp'          => $request->harga_satuan,
            'harga_jual'   => $request->harga_jual,
            'stok_awal'    => 0,
            'stok_akhir'   => 0,
        ]);

        return response()->json([
            'ok'   => true,
            'data' => [
                'pemasok' => $pemasok,
                'barang'  => $barang,
            ],
        ]);
    }



   public function getParties(Request $request)
    {
        $tipe = $request->query('tipe');  
        if ($tipe === 'Inventaris') {
            $rows = PemasokModel::orderBy('nama_pemasok')
                    ->get(['id_pemasok as id', 'nama_pemasok as nama']);
        } else {
            $rows = PelangganModel::orderBy('nama_pelanggan')
                    ->get(['id_pelanggan as id', 'nama_pelanggan as nama']);
        }
        return response()->json(['ok' => true, 'data' => $rows]);
    }
   public function barangList()
    {
        $rows = DatBarangModel::query()
            ->select('id_barang as id','nama_barang as nama','satuan_ukur','harga_jual','hpp','harga_satuan')
            ->orderBy('nama_barang')
            ->get();

        return response()->json(['ok' => true, 'data' => $rows]);
    }

 private function insertJurnalTunaiPenjualan(string $noTransaksi, float $totalItem, int $jenisCode, int $tipePembayaran): void
{
    // ======================
    // CASE 1: Penjualan (1) + Tunai (1)
    // ======================
    if ($jenisCode === 1 && $tipePembayaran === 1) {
        $rows = [
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 101,
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 102,
                'nama_akun'     => 'null',
                'jml_debit'     => 0,
                'jml_kredit'    => (float) $totalItem,
                'jenis_laporan' => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('dat_detail_transaksi')->insert($rows);

        foreach ($rows as $r) {
            $affected = 0;
            if ($r['jml_debit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->decrement('saldo_berjalan', (float) $r['jml_kredit']);
            }
            if ($affected === 0) {
                throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
            }
        }
    }

    // ======================
    // CASE 2: Penjualan (1) + Kredit (2)
    // ======================
    if ($jenisCode === 1 && $tipePembayaran === 2) {
        $rows = [
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 105,
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2, 
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 102,
                'nama_akun'     => 'null',
                'jml_debit'     => 0,
                'jml_kredit'    => (float) $totalItem,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('dat_detail_transaksi')->insert($rows);

        foreach ($rows as $r) {
            $affected = 0;
            if ($r['jml_debit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->decrement('saldo_berjalan', (float) $r['jml_kredit']);
            }
            if ($affected === 0) {
                throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
            }
        }
    }

    // ======================
    // CASE 3: Inventaris (2) + Tunai (1)
    // ======================
    if ($jenisCode === 2 && $tipePembayaran === 1) {
        $rows = [
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 106,
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 101,
                'nama_akun'     => 'null',
                'jml_debit'     => 0,
                'jml_kredit'    => (float) $totalItem,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('dat_detail_transaksi')->insert($rows);

        foreach ($rows as $r) {
            $affected = 0;
            if ($r['jml_debit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->decrement('saldo_berjalan', (float) $r['jml_kredit']);
            }
            if ($affected === 0) {
                throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
            }
        }
    }

    // ======================
    // CASE 4: Inventaris (2) + Kredit (2)
    // ======================
    if ($jenisCode === 2 && $tipePembayaran === 2) {
        $rows = [
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 106,
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 105,
                'nama_akun'     => 'null',
                'jml_debit'     => 0,
                'jml_kredit'    => (float) $totalItem,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('dat_detail_transaksi')->insert($rows);

        foreach ($rows as $r) {
            $affected = 0;
            if ($r['jml_debit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                    ->lockForUpdate()->decrement('saldo_berjalan', (float) $r['jml_kredit']);
            }
            if ($affected === 0) {
                throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
            }
        }
    }
}



public function store(Request $request)
{
    $request->validate([
        'tipe'              => ['required', Rule::in(['Penjualan','Inventaris'])],
        'tipe_pembayaran'    => ['required','integer','in:1,2'],
        'tanggal'           => ['required','string'],
        'pelanggan_id'      => ['nullable','integer'],
        'party_id'          => ['nullable','integer'],
        'no_transaksi'      => ['nullable','string','max:50'],
        'biaya_lain'        => ['nullable','numeric'],
        'diskon_persen'     => ['nullable','numeric','min:0','max:100'],
        'pajak_persen'      => ['nullable','numeric','min:0','max:100'],
        'items'             => ['required','array','min:1'],
        'items.*.barang_id' => ['required','integer','exists:dat_barang,id_barang'],
        'items.*.qty'       => ['required','numeric','min:0.0001'],
        'items.*.satuan'    => ['nullable','string','max:50'],
        'items.*.harga'     => ['required','numeric','min:0'],
    ]);

    // Parse tanggal (dd/mm/YYYY atau format lain yang valid)
    try {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->tanggal)) {
            $tglSql = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
        } else {
            $tglSql = Carbon::parse($request->tanggal)->format('Y-m-d');
        }
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'message' => 'Format tanggal tidak valid'], 422);
    }

    // Normalisasi item
    $items = collect($request->items)->map(function($it){
        $qty   = (float) $it['qty'];
        $hargajual = (float) $it['hargajual'];
        return [
            'barang_id' => (int) $it['barang_id'],
            'qty'       => $qty,
            'satuan'    => $it['satuan'] ?? null,
            'harga'     => $hargajual,
            'total'     => (float) round($qty * $hargajual),         
        ];
    });
    $tipePembayaran = (int) $request->input('tipe_pembayaran', 1); 
    $tipePembayaran = ($tipePembayaran === 2) ? 2 : 1;      
    $subtotal      = (float) $items->sum('total');
    $biayaLain     = (float) ($request->biaya_lain ?? 0);
    $diskonPersen  = (float) ($request->diskon_persen ?? 0);
    $pajakPersen   = (float) ($request->pajak_persen ?? 11);

    $afterDisc     = (float) round($subtotal * (1 - ($diskonPersen / 100)));
    $pajakNominal  = (float) round($afterDisc * ($pajakPersen / 100));
    $grandTotal    = max(0, $afterDisc + $pajakNominal + $biayaLain);

    $idKontak      = $request->input('party_id', $request->input('pelanggan_id')); 
    $jenisCode = $request->tipe === 'Penjualan' ? 1 : 2;
    $prefix    = $jenisCode === 1 ? 'P' : 'S';

    $noTransaksi = trim((string) $request->no_transaksi);
    $valid = preg_match('/^[PS]\d{7}$/', $noTransaksi);

if (!$valid) {
    $lastNo = DB::table('dat_transaksi')
        ->where('no_transaksi', 'like', $prefix.'%')
        ->orderByDesc('id_transaksi')
        ->value('no_transaksi');

    $seq = 0;
    if ($lastNo && preg_match('/\d+$/', $lastNo, $m)) {
        $seq = (int) $m[0];
    }
    $noTransaksi = $prefix . str_pad($seq + 1, 7, '0', STR_PAD_LEFT);
}


   DB::beginTransaction();
try {
    $jenisCode  = ($request->tipe === 'Penjualan') ? 1 : 2; // 1=Penjualan, 2=Inventaris
    $prefix     = $jenisCode === 1 ? 'P' : 'S';

    // generate no_transaksi kalau kosong
    $noTransaksi = $request->no_transaksi;
    if (!$noTransaksi) {
        $lastNo = DB::table('dat_transaksi')
            ->where('no_transaksi', 'like', $prefix.'%')
            ->orderByDesc('id_transaksi')
            ->value('no_transaksi');

        $seq = 0;
        if ($lastNo && preg_match('/\d+$/', $lastNo, $m)) $seq = (int) $m[0];
        $noTransaksi = $prefix . str_pad($seq + 1, 7, '0', STR_PAD_LEFT);
    }

    // ===== INSERT PER ITEM KE dat_transaksi =====
    $rows = [];
    $runningPajak = 0;
    $runningBiaya = 0;
    $subtotalSafe = max(1, $subtotal); // hindari bagi nol

    foreach ($items->values() as $idx => $it) {
        $isLast = ($idx === $items->count() - 1);

        $base           = (float) $it['total'];                                // harga*qty per item (sebelum diskon/pajak)
        $afterDiscItem  = (float) round($base * (1 - ($diskonPersen / 100)));  // diskon per item
        $share          = $base / $subtotalSafe;                                // proporsi item thd subtotal

        // pajak & biaya_lain proporsional; perbaiki di item terakhir supaya totalnya pas
        $pajakItem  = (int) round($afterDiscItem * ($pajakPersen / 100));
        $biayaItem  = (int) round($biayaLain * $share);

        if ($isLast) {
            $pajakItem = (int) ($pajakNominal - $runningPajak);
            $biayaItem = (int) ($biayaLain - $runningBiaya);
        }

        $totalItem = (int) ($afterDiscItem + $pajakItem + $biayaItem);

        $rows[] = [
            'id_kontak'         => $idKontak,                 
            'id_barang'         => (int) $it['barang_id'],   
            'id_pajak'          => null,                    
            'jenis_transaksi'   => (string) $jenisCode,    
            'tipe_pembayaran'    => (int) $tipePembayaran, //metode pembayaran 1 = tunai else ... 
            'no_transaksi'      => $noTransaksi,              
            'tgl'               => $tglSql,
            'jml_barang'        => (float) $it['qty'],
            'metode_pembayaran' => null,                      
            'hpp'               => 0,                       
            'pajak'             => $pajakItem,              
            'total'             => $totalItem,              
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
        $runningPajak += $pajakItem;
        $runningBiaya += $biayaItem;
    }

    DB::table('dat_transaksi')->insert($rows);
    $this->insertJurnalTunaiPenjualan($noTransaksi, (float) $totalItem, (int) $jenisCode, (int) $tipePembayaran);

    DB::table('dat_barang')
        ->where('id_barang', (int) $it['barang_id'])
        ->increment('stok_akhir', (float) $it['qty']);
    DB::commit();
    return response()->json([
        'ok' => true,
        'message' => 'Transaksi tersimpan',
        'no_transaksi' => $noTransaksi
    ]);
} catch (\Throwable $e) {
    DB::rollBack();
    return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
}

}

public function datatableTransaksi()
{
    // agregat per no_transaksi
    $agg = DB::table('dat_transaksi as t')
        ->select(
            't.no_transaksi',
            DB::raw('MIN(t.tgl) as tgl'),
            DB::raw('MAX(t.jenis_transaksi) as jenis_transaksi'),
            DB::raw('MAX(t.id_kontak) as id_kontak'),
            DB::raw('SUM(t.jml_barang) as qty'),
            DB::raw('SUM(t.total) as total')
        )
        ->groupBy('t.no_transaksi');

    // bungkus jadi subquery biar gampang join kontak
    $x = DB::query()->fromSub($agg, 'x')
        ->leftJoin('dat_pelanggan as pl', function($j){
            $j->on('pl.id_pelanggan', '=', 'x.id_kontak')
              ->where('x.jenis_transaksi', '=', 1);
        })
        ->leftJoin('dat_pemasok as ps', function($j){
            $j->on('ps.id_pemasok', '=', 'x.id_kontak')
              ->where('x.jenis_transaksi', '=', 2);
        })
        // ambil satu nama barang pertama sebagai "deskripsi"
        ->select([
            'x.no_transaksi',
            'x.tgl',
            'x.jenis_transaksi',
            DB::raw('COALESCE(pl.nama_pelanggan, ps.nama_pemasok) as nama_kontak'),
            'x.qty',
            'x.total',
            DB::raw("(SELECT b.nama_barang 
                      FROM dat_transaksi t2 
                      JOIN dat_barang b ON b.id_barang = t2.id_barang
                      WHERE t2.no_transaksi = x.no_transaksi
                      ORDER BY t2.id_transaksi ASC
                      LIMIT 1) as deskripsi")
        ])
        ->orderByDesc('x.tgl')
        ->orderByDesc('x.no_transaksi')
        ->get();

    // siapkan untuk DataTables
    $rows = $x->map(function($r){
        return [
            'tgl'           => $r->tgl,
            'tipe_label'    => ((int)$r->jenis_transaksi === 1 ? 'Penjualan' : 'Inventaris'),
            'no_transaksi'  => $r->no_transaksi,
            'nama_kontak'   => $r->nama_kontak ?: '-',
            'deskripsi'     => $r->deskripsi ?: '-',
            'qty'           => (float) $r->qty,
            'total'         => (float) $r->total,
        ];
    });

    return response()->json(['data' => $rows]);
}

// CHANGES: Data untuk tab "Inventaris" (jenis_transaksi = 2)
public function datatableInventaris()
{
    $rows = DB::table('dat_transaksi as t')
        ->leftJoin('dat_barang as b', 'b.id_barang', '=', 't.id_barang')
        ->leftJoin('dat_pemasok as ps', function($j){
            $j->on('ps.id_pemasok', '=', 't.id_kontak')
              ->where('t.jenis_transaksi', '=', 2);
        })
        ->where('t.jenis_transaksi', 2)
        ->select([
            'b.nama_barang',
            'ps.nama_pemasok',
            't.jml_barang',
            'b.satuan_ukur',
            't.total',
            't.no_transaksi',
            't.tgl',
        ])
        ->orderByDesc('t.tgl')
        ->orderByDesc('t.no_transaksi')
        ->get()
        ->map(function($r){
            return [
                'nama_barang'  => $r->nama_barang ?: '-',
                'pemasok'      => $r->nama_pemasok ?: '-',
                'stok'         => (float) $r->jml_barang,   // tampilkan qty baris transaksi
                'satuan'       => $r->satuan_ukur ?: '-',
                'total'        => (float) $r->total,
            ];
        });

    return response()->json(['data' => $rows]);
}

public function getBarangByPemasok(Request $request)
{
    $pemasokId = $request->input('pemasok_id');

    $pemasokId = 'SUP' . str_pad($pemasokId, 3, '0', STR_PAD_LEFT);

    $barang = DatBarangModel::where('kode_pemasok', $pemasokId)->get();

    return response()->json([
        'ok' => true,
        'data' => $barang
    ]);
}

public function getBarangSemua(Request $request)
{
    // Tanpa filter & tanpa parameter pencarian
    $barang = DatBarangModel::orderBy('nama_barang')->get();

    return response()->json([
        'ok'   => true,
        'data' => $barang
    ]);
}


}
