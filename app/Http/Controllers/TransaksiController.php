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

class TransaksiController extends Controller
{
    public function index()
    {
        return view('transaksi.index'); 
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
            'harga_satuan' => $request->harga_satuan,
            'harga_jual'   => $request->harga_jual,
            'stok_awal'    => 0,
            'stok_akhir'   => 0,
        ]);
// dd($barang);    
        return response()->json([
            'ok'   => true,
            'data' => [
                'pemasok' => $pemasok,
                'barang'  => $barang,
            ],
        ]);
    }
 public function storeBarang(Request $request)
{
    $rules = [
        'pemasok_id'   => 'required|integer|exists:dat_pemasok,id_pemasok',
        'nama_barang2'  => 'required|string|max:150',
        'satuan_ukur2'  => 'required|string|max:50',
        'harga_satuan2' => 'required|numeric|min:0',
        'harga_jual2'   => 'required|numeric|min:0',
    ];

    $v = Validator::make($request->all(), $rules, [
        'kode_pemasok.exists' => 'Kode pemasok tidak ditemukan.',
    ]);


    if ($v->fails()) {
        return response()->json(['ok' => false, 'message' => $v->errors()->first()], 422);
    }

    // Opsional: pastikan id & kode cocok
    $match = DB::table('dat_pemasok')
        ->where('id_pemasok', $request->pemasok_id)
        ->where('kode_pemasok', $request->kode_pemasok)
        ->exists();

    if (!$match) {
        return response()->json([
            'ok' => false,
            'message' => 'Pemasok tidak valid (id & kode tidak cocok).'
        ], 422);
    }
    $barang = DatBarangModel::create([
        'kode_pemasok' => $request->kode_pemasok,   
        'nama_barang'  => $request->nama_barang2,
        'satuan_ukur'  => $request->satuan_ukur2,
        'harga_satuan' => $request->harga_satuan2,
        'harga_jual'   => $request->harga_jual2,
        'stok_awal'    => 0,
        'stok_akhir'   => 0,
    ]);
   

    return response()->json([
        'ok'      => true,
        'message' => 'Barang berhasil ditambahkan.',
        'data'    => ['barang' => $barang],
    ], 201);
}





   public function getParties(Request $request)
    {
        $tipe = $request->query('tipe');  
        if ($tipe === 'Inventaris') {
            $rows = PemasokModel::orderBy('nama_pemasok')
                    ->get(['id_pemasok as id', 'nama_pemasok as nama', 'kode_pemasok']);
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
                'kode_akun'     => 1101, //kas
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 4101,//penjualan
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
                    ->lockForUpdate()
                    ->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                // cek dulu kalau kode akun = 101 (kas)
                if ((int)$r['kode_akun'] === 1101) {
                    $akun = MstAkunModel::where('kode_akun', 1101)->lockForUpdate()->first();
                    if (!$akun) {
                        throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
                    }
                    $saldoBaru = (float)$akun->saldo_berjalan - (float)$r['jml_kredit'];
                    if ($saldoBaru < 0) {
                        throw new \RuntimeException(
                            "Saldo kas tidak mencukupi untuk transaksi {$noTransaksi}. " .
                            "Saldo saat ini: {$akun->saldo_berjalan}, pengurangan: {$r['jml_kredit']}."
                        );
                    }
                    // baru decrement kalau aman
                    $affected = $akun->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                } else {
                    $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                        ->lockForUpdate()
                        ->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                }
            }

            if ($affected === 0) {
                throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
            }
        }
            $akunSaldo = MstAkunModel::where('kode_akun', 3201)->lockForUpdate()->first();
            if (!$akunSaldo) {
                throw new \RuntimeException("Kode akun 3201 tidak ditemukan di mst_akun.");
            }
            $akunSaldo->increment('saldo_berjalan', (float) $totalItem);
    }

    // ======================
    // CASE 2: Penjualan (1) + Kredit (2)
    // ======================
    if ($jenisCode === 1 && $tipePembayaran === 2) {
        $rows = [
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 1103, //piutang usaha
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2, 
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 4101,//penjualan
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
                    ->lockForUpdate()
                    ->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                // cek dulu kalau kode akun = 1101 (kas)
                if ((int)$r['kode_akun'] === 1101) {
                    $akun = MstAkunModel::where('kode_akun', 1101)->lockForUpdate()->first();
                    if (!$akun) {
                        throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
                    }
                    $saldoBaru = (float)$akun->saldo_berjalan - (float)$r['jml_kredit'];
                    if ($saldoBaru < 0) {
                        throw new \RuntimeException(
                            "Saldo kas tidak mencukupi untuk transaksi {$noTransaksi}. " .
                            "Saldo saat ini: {$akun->saldo_berjalan}, pengurangan: {$r['jml_kredit']}."
                        );
                    }
                    // baru decrement kalau aman
                    $affected = $akun->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                } else {
                    $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                        ->lockForUpdate()
                        ->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                }
            }

            if ($affected === 0) {
                throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
            }
        }
            $akunSaldo = MstAkunModel::where('kode_akun', 3201)->lockForUpdate()->first();
            if (!$akunSaldo) {
                throw new \RuntimeException("Kode akun 3201 tidak ditemukan di mst_akun.");
            }
            $akunSaldo->increment('saldo_berjalan', (float) $totalItem);
    }

    // ======================
    // CASE 3: Inventaris (2) + Tunai (1)
    // ======================
    if ($jenisCode === 2 && $tipePembayaran === 1) {
        $rows = [
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 1104,//persediaan
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 1101,
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
                    ->lockForUpdate()
                    ->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                // cek dulu kalau kode akun = 101 (kas)
                if ((int)$r['kode_akun'] === 1101) {
                    $akun = MstAkunModel::where('kode_akun', 1101)->lockForUpdate()->first();
                    if (!$akun) {
                        throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
                    }
                    $saldoBaru = (float)$akun->saldo_berjalan - (float)$r['jml_kredit'];
                    if ($saldoBaru < 0) {
                        throw new \RuntimeException(
                            "Saldo kas tidak mencukupi untuk transaksi {$noTransaksi}. " .
                            "Saldo saat ini: {$akun->saldo_berjalan}, pengurangan: {$r['jml_kredit']}."
                        );
                    }
                    // baru decrement kalau aman
                    $affected = $akun->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                } else {
                    $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                        ->lockForUpdate()
                        ->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                }
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
                'kode_akun'     => 1104,
                'nama_akun'     => 'null',
                'jml_debit'     => (float) $totalItem,
                'jml_kredit'    => 0,
                'jenis_laporan' => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'no_transaksi'  => $noTransaksi,
                'kode_akun'     => 2101,
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
                    ->lockForUpdate()
                    ->increment('saldo_berjalan', (float) $r['jml_debit']);
            } elseif ($r['jml_kredit'] > 0) {
                // cek dulu kalau kode akun = 101 (kas)
                if ((int)$r['kode_akun'] === 1101) {
                    $akun = MstAkunModel::where('kode_akun', 1101)->lockForUpdate()->first();
                    if (!$akun) {
                        throw new \RuntimeException("Kode akun {$r['kode_akun']} tidak ditemukan di mst_akun.");
                    }
                    $saldoBaru = (float)$akun->saldo_berjalan - (float)$r['jml_kredit'];
                    if ($saldoBaru < 0) {
                        throw new \RuntimeException(
                            "Saldo kas tidak mencukupi untuk transaksi {$noTransaksi}. " .
                            "Saldo saat ini: {$akun->saldo_berjalan}, pengurangan: {$r['jml_kredit']}."
                        );
                    }
                    // baru decrement kalau aman
                    $affected = $akun->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                } else {
                    $affected = MstAkunModel::where('kode_akun', $r['kode_akun'])
                        ->lockForUpdate()
                        ->decrement('saldo_berjalan', (float)$r['jml_kredit']);
                }
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
        'tipe'                 => ['required', Rule::in(['Penjualan','Inventaris'])],
        'tipe_pembayaran'      => ['required','integer','in:1,2'],
        'tanggal'              => ['required','string'],
        'pelanggan_id'         => ['nullable','integer'],
        'party_id'             => ['nullable','integer'],
        'no_transaksi'         => ['required_if:tipe,Inventaris','nullable','string','max:50'],
        'biaya_lain'           => ['nullable','numeric'],
        'diskon_nominal'       => ['nullable','numeric','min:0'],
        'pajak_persen'         => ['nullable','numeric','min:0','max:100'],
        'apply_pajak'          => ['required','boolean'],
        'items'                => ['required','array','min:1'],
        'items.*.barang_id'    => ['required','integer','exists:dat_barang,id_barang'],
        'items.*.qty'          => ['required','numeric','min:0.0001'],
        'items.*.satuan'       => ['nullable','string','max:50'],
        'items.*.harga'        => ['required','numeric','min:0'],
        'items.*.subtotal'     => ['nullable','numeric','min:0'],
        'items.*.harga_mentah' => ['nullable','numeric','min:0'],
        'kode_pemasok'         => ['nullable','string','max:50'],
    ]);

    try {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->tanggal)) {
            $tglSql = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
        } else {
            $tglSql = Carbon::parse($request->tanggal)->format('Y-m-d');
        }
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'message' => 'Format tanggal tidak valid'], 422);
    }

    // ============================
    // Normalisasi item & subtotal
    // ============================
    $items = collect($request->items)->map(function($it) use ($request) {
        $qty   = (float) $it['qty'];
        $harga = ($request->tipe === 'Penjualan')
            ? (float) ($it['hargajual'] ?? $it['harga'])
            : (float) $it['harga'];

        $hargaMentah = isset($it['harga_mentah'])
            ? (float) $it['harga_mentah']
            : $harga;

        $subtotal = (float) round($qty * $harga);

        return [
            'barang_id'    => (int) $it['barang_id'],
            'qty'          => $qty,
            'satuan'       => $it['satuan'] ?? null,
            'harga'        => $harga,
            'harga_mentah' => $hargaMentah,
            'subtotal'     => $subtotal,
            'total'        => (float) round($qty * $harga),
        ];
    });

    $tipePembayaran = (int) $request->input('tipe_pembayaran', 1);
    $tipePembayaran = ($tipePembayaran === 2) ? 2 : 1;

    $subtotal       = (float) $items->sum('total');
    $biayaLain      = (float) ($request->biaya_lain ?? 0);
    $diskonNominal  = (float) ($request->diskon_nominal ?? 0);
    $pajakPersen    = (float) ($request->pajak_persen ?? 11);
    $applyPajak     = $request->boolean('apply_pajak');

    $afterDisc    = $subtotal - $diskonNominal;
    $pajakNominal = $applyPajak ? (float) round($afterDisc * ($pajakPersen / 100)) : 0.0;
    $grandTotal   = max(0, $afterDisc + $pajakNominal + $biayaLain);

    $idKontak  = $request->input('party_id', $request->input('pelanggan_id'));
    $jenisCode = $request->tipe === 'Penjualan' ? 1 : 2; // 1=Penjualan, 2=Inventaris
    $prefix    = $jenisCode === 1 ? 'P' : 'S';

    // ============================
    // Nomor Transaksi
    // ============================
    $noTransaksi = trim((string) $request->no_transaksi);

    if ($request->tipe === 'Inventaris') {
        if ($noTransaksi === '') {
            return response()->json(['ok' => false, 'message' => 'No transaksi wajib diisi untuk pembelian'], 422);
        }
        if (DB::table('dat_transaksi')->where('no_transaksi', $noTransaksi)->exists()) {
            return response()->json(['ok' => false, 'message' => 'No transaksi sudah digunakan'], 422);
        }
    } else {
        $valid = preg_match('/^P\d{7}$/', $noTransaksi);
        if (!$valid) {
            $lastNo = DB::table('dat_transaksi')
                ->where('no_transaksi', 'like', 'P%')
                ->orderByDesc('id_transaksi')
                ->value('no_transaksi');

            $seq = 0;
            if ($lastNo && preg_match('/\d+$/', $lastNo, $m)) {
                $seq = (int) $m[0];
            }
            $noTransaksi = 'P' . str_pad($seq + 1, 7, '0', STR_PAD_LEFT);
        }
    }

    DB::beginTransaction();
    try {
        // ============================
        // Simpan Transaksi & Update Stok
        // ============================
        $rows           = [];
        $runningPajak   = 0;
        $runningBiaya   = 0;
        $subtotalSafe   = max(1, $subtotal);
        $totalHppMentah = 0.0;

        foreach ($items->values() as $idx => $it) {
            $isLast = ($idx === $items->count() - 1);

            $barang = DatBarangModel::where('id_barang', (int) $it['barang_id'])
                ->lockForUpdate()
                ->first();

            if (!$barang) {
                throw new \RuntimeException("Barang ID {$it['barang_id']} tidak ditemukan.");
            }

               $base = (float) $it['total'];
            $afterDiscItem = $base - $diskonNominal;
            $share = $subtotalSafe > 0 ? ($base / $subtotalSafe) : 0.0;
            $pajakItem = $applyPajak ? (int) round($afterDiscItem * ($pajakPersen / 100)) : 0;
            $biayaItem = (int) round($biayaLain * $share);
            $hargaSatuan = (float) ($barang->harga_satuan ?? 0);
            $hargaSatuanTotal   = (float) round($hargaSatuan * (float) $it['qty']); 
            if ($isLast) {
                $pajakItem = $applyPajak ? (int) ($pajakNominal - $runningPajak) : 0;
                $biayaItem = (int) ($biayaLain - $runningBiaya);
            }

            $totalItem = (int) ($afterDiscItem + $pajakItem + $biayaItem);
            $hargaMentahSrc = $hargaSatuan;
            $hppRow = (float) round($hargaMentahSrc * (float) $it['qty']);
            $totalHppMentah += $hppRow;  
            

            $totalItem = (int) ($afterDiscItem + $pajakItem + $biayaItem);

            $rows[] = [
                'id_kontak'         => $idKontak,
                'id_barang'         => (int) $it['barang_id'],
                'id_pajak'          => null,
                'jenis_transaksi'   => (string) $jenisCode,
                'tipe_pembayaran'   => (int) $tipePembayaran,
                'no_transaksi'      => $noTransaksi,
                'tgl'               => $tglSql,
                'jml_barang'        => (float) $it['qty'],
                'metode_pembayaran' => null,
                'hpp'               => $hppRow,                   // [change] hapus duplikasi 'hpp' => 0
                'harga_mentah'      => (float) $it['harga_mentah'],
                'pajak'             => $pajakItem,
                'subtotal'          => (float) $it['subtotal'],
                'total'             => $totalItem,
                'biaya_lain'        => (float) $biayaLain,
                'diskon'            => (float) $diskonNominal,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $runningPajak += $pajakItem;
            $runningBiaya += $biayaItem;

            // Update stok
            if ($jenisCode === 1) {
                // Penjualan: stok berkurang
                if ($barang->stok_akhir < $it['qty']) {
                    throw new \RuntimeException("Stok barang {$barang->nama_barang} tidak mencukupi.");
                }
                $barang->decrement('stok_akhir', (float) $it['qty']);
            } else {
                // Pembelian: stok bertambah
                $barang->increment('stok_akhir', (float) $it['qty']);
            }
        }

        DB::table('dat_transaksi')->insert($rows);

        // ============================
        // Insert ke Jurnal & Buku Besar
        // ============================
        $keterangan = $request->tipe === 'Penjualan' ? 'Penjualan Barang' : 'Pembelian Inventaris';

        if ($jenisCode === 1) {
            // PENJUALAN
            if ($tipePembayaran === 1) {
                $akunDebet      = 1;   // Kas
                $akunKredit     = 15;  // Pendapatan Penjualan
                $tambahAkun17   = true;
            } else {
                $akunDebet      = 20;  // Piutang Usaha
                $akunKredit     = 15;  // Pendapatan Penjualan
                $tambahAkun17   = true;
            }
        } else {
            // PEMBELIAN (INVENTARIS)
            if ($tipePembayaran === 1) {
                $akunDebet      = 6;   // Persediaan / Inventaris
                $akunKredit     = 1;   // Kas
                $tambahAkun17   = false;
            } else {
                $akunDebet      = 6;   // Persediaan / Inventaris
                $akunKredit     = 5;   // Utang Usaha
                $tambahAkun17   = false;
            }
        }

        // Pengaruh saldo kredit (khusus pembelian kredit → utang bertambah)
        $kreditMenambahSaldo = ($jenisCode === 2 && $tipePembayaran === 2);

        // Nominal jurnal (aturan kamu): Penjualan → subtotal, Pembelian → grandTotal
        $nominalJurnal = ($jenisCode === 1) ? (float) ($subtotal ?? 0) : (float) ($grandTotal ?? 0);

        // ============================
        // Jenis Laporan (Detail Jurnal)
        // ============================
        // Default mengikuti jenis transaksi (1=P&L, 2=Neraca)
        $jenisLaporanDebet  = $jenisCode;
        $jenisLaporanKredit = 1;

        // [change] Override: Penjualan Kredit → DEBET (Piutang) harus Neraca (2)
        if ($jenisCode === 1 && $tipePembayaran === 2) {
            $jenisLaporanDebet = 2;
        }

        // Jurnal utama
        $this->insertJurnalSimple(
            $tglSql,
            $nominalJurnal,
            $keterangan,
            $akunDebet,
            $akunKredit,
            $jenisLaporanDebet,   // [change] bisa 2 bila penjualan kredit
            $jenisLaporanKredit,  // tetap 1 (Pendapatan)
            $noTransaksi,
            $request->tipe,
            $kreditMenambahSaldo
        );

        // Jurnal HPP (Penjualan saja)
        if ($jenisCode === 1 && $totalHppMentah > 0) {
            $this->insertJurnalSimple(
                $tglSql,
                (float) $totalHppMentah,
                'HPP Penjualan ' . $noTransaksi,
                3,     // Beban HPP
                6,     // Persediaan/Inventaris
                1,     // P&L
                1,     // P&L
                $noTransaksi,
                'HPP',
                false
            );
        }

        // Tambah saldo berjalan akun 17 (sesuai aturan kamu)
        if (!empty($tambahAkun17)) {
            DB::table('mst_akun')
                ->where('id', 17)
                ->lockForUpdate()
                ->increment('saldo_berjalan', (float) $grandTotal);
        }

        
         if ($hppRow > 0) {
             DB::table('mst_akun')
            ->where('id', 17)
            ->lockForUpdate()
            ->decrement('saldo_berjalan', $hppRow);
         }

        // Pembelian Kredit → Utang
        if ($jenisCode === 2 && $tipePembayaran === 2) {
            $kodePemasok = $request->input('kode_pemasok');
            if (!$kodePemasok && $idKontak) {
                $kodePemasok = DB::table('dat_pemasok')
                    ->where('id_pemasok', $idKontak)
                    ->value('kode_pemasok');
            }

            DB::table('dat_utang')->insert([
                'kode_pemasok' => (string) ($kodePemasok ?? ''),
                'no_transaksi' => $noTransaksi,
                'nominal'      => (float) $grandTotal,
                'created_by'   => (int) (auth()->id() ?? 0),
                'tanggal'      => $tglSql,
                'status'       => 0,
            ]);
        }

        // Penjualan Kredit → Piutang
        if ($jenisCode === 1 && $tipePembayaran === 2) {
            $idPelanggan = (int) ($request->input('pelanggan_id') ?? $idKontak ?? 0);

            DB::table('dat_piutang')->insert([
                'id_pelanggan' => $idPelanggan,
                'no_transaksi' => $noTransaksi,
                'nominal'      => (float) $grandTotal,
                'status'       => 0, // Belum Lunas
                'created_by'   => (int) (auth()->id() ?? 0),
                'tanggal'      => $tglSql,
            ]);
        }

        DB::commit();

        return response()->json([
            'ok'           => true,
            'message'      => 'Transaksi tersimpan dan jurnal dibuat',
            'no_transaksi' => $noTransaksi
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
    }
}

// =============================
// HELPER: INSERT JURNAL SIMPLE
// =============================
private function insertJurnalSimple(
    string $tanggal,
    float $nominal,
    ?string $keterangan,
    int $akunDebet,
    int $akunKredit,
    int $jenisLaporanDebet = 2,
    int $jenisLaporanKredit = 1,
    string $noReferensi = 'tes',
    string $modulSumber = 'tes',
    bool $kreditMenambahSaldo = false
): void {

    DB::transaction(function () use (
        $tanggal, $nominal, $keterangan, $akunDebet, $akunKredit,
        $jenisLaporanDebet, $jenisLaporanKredit, $noReferensi,
        $modulSumber, $kreditMenambahSaldo,
    ) {
        $now     = now();
        $periode = Carbon::parse($tanggal)->format('Y-m');

        $akunRows = DB::table('mst_akun')
            ->whereIn('id', [$akunDebet, $akunKredit])
            ->lockForUpdate()
            ->get(['id','saldo_berjalan'])
            ->keyBy('id');

        $currDebet  = (float)($akunRows[$akunDebet]->saldo_berjalan ?? 0);
        $currKredit = (float)($akunRows[$akunKredit]->saldo_berjalan ?? 0);

        $saldoDebetAfter  = $currDebet + $nominal;
        // [note] Jika $kreditMenambahSaldo=true → saldo kredit ditambah; jika tidak → dikurang
        $saldoKreditAfter = $kreditMenambahSaldo ? ($currKredit + $nominal) : ($currKredit - $nominal);

        $idJurnal = DB::table('dat_header_jurnal')->insertGetId([
            'tgl_transaksi' => $tanggal,
            'no_referensi'  => $noReferensi,
            'keterangan'    => $keterangan,
            'modul_sumber'  => $modulSumber,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        DB::table('dat_detail_jurnal')->insert([
            [
                'id_jurnal'      => $idJurnal,
                'id_akun'        => $akunDebet,
                'jml_debit'      => $nominal,
                'jml_kredit'     => 0,
                'jenis_laporan'  => $jenisLaporanDebet,
                'saldo_berjalan' => $saldoDebetAfter,
                'tanggal'        => $tanggal,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'id_jurnal'      => $idJurnal,
                'id_akun'        => $akunKredit,
                'jml_debit'      => 0,
                'jml_kredit'     => $nominal,
                'jenis_laporan'  => $jenisLaporanKredit,
                'saldo_berjalan' => $saldoKreditAfter,
                'tanggal'        => $tanggal,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ]);

        foreach ([
            $akunDebet  => ['debit' => $nominal, 'kredit' => 0],
            $akunKredit => ['debit' => 0,        'kredit' => $nominal],
        ] as $akunId => $val) {
            $bukbes = DB::table('dat_buku_besar')
                ->where('id_akun', $akunId)
                ->where('periode', $periode)
                ->lockForUpdate()
                ->first();

            if ($bukbes) {
                DB::table('dat_buku_besar')
                    ->where('id_bukbes', $bukbes->id_bukbes)
                    ->update([
                        'ttl_debit'   => (float)$bukbes->ttl_debit  + (float)$val['debit'],
                        'ttl_kredit'  => (float)$bukbes->ttl_kredit + (float)$val['kredit'],
                        'saldo_akhir' => (float)$bukbes->saldo_akhir + ((float)$val['debit'] - (float)$val['kredit']),
                        'updated_at'  => $now,
                    ]);
            } else {
                DB::table('dat_buku_besar')->insert([
                    'id_akun'     => $akunId,
                    'periode'     => $periode,
                    'ttl_debit'   => (float)$val['debit'],
                    'ttl_kredit'  => (float)$val['kredit'],
                    'saldo_akhir' => (float)$val['debit'] - (float)$val['kredit'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        DB::table('mst_akun')
            ->where('id', $akunDebet)
            ->update(['saldo_berjalan' => $saldoDebetAfter, 'updated_at' => $now]);

        DB::table('mst_akun')
            ->where('id', $akunKredit)
            ->update(['saldo_berjalan' => $saldoKreditAfter, 'updated_at' => $now]);
    });
}

public function datatableTransaksi(Request $r) // [CHANGES] terima Request
{
    // [CHANGES] Ambil parameter filter dari querystring
    $tglAwal = $r->query('tgl_awal');
    $tglAkhir = $r->query('tgl_akhir');
    $tipeParam = $r->query('tipe'); // contoh: "Penjualan" / "Inventaris" / 1 / 2

    // [CHANGES] Map tipeParam -> jenis_transaksi (1=Penjualan, 2=Inventaris)
    $jenisFilter = null;
    if ($tipeParam !== null && $tipeParam !== '') {
        if (is_numeric($tipeParam)) {
            $jenisFilter = (int) $tipeParam;
        } else {
            $t = strtolower(trim($tipeParam));
            if (in_array($t, ['penjualan', 'jual'], true))     $jenisFilter = 1;
            if (in_array($t, ['inventaris', 'pembelian', 'beli'], true)) $jenisFilter = 2;
        }
        if (!in_array($jenisFilter, [1, 2], true)) $jenisFilter = null;
    }

    // [CHANGES] Bangun base query agar filter diaplikasikan SEBELUM agregasi
    $base = DB::table('dat_transaksi as t')
        ->select(
            't.no_transaksi',
            DB::raw('MIN(t.tgl) as tgl'),
            DB::raw('MAX(t.jenis_transaksi) as jenis_transaksi'),
            DB::raw('MAX(t.id_kontak) as id_kontak'),
            DB::raw('SUM(t.jml_barang) as qty'),
            DB::raw('SUM(t.total) as total')
        )
        ->whereIn('t.jenis_transaksi', [1, 2]);

    // [CHANGES] Terapkan filter tanggal (inklusif)
    if (!empty($tglAwal)) {
        $base->whereDate('t.tgl', '>=', $tglAwal);
    }
    if (!empty($tglAkhir)) {
        $base->whereDate('t.tgl', '<=', $tglAkhir);
    }

    // [CHANGES] Terapkan filter tipe jika ada
    if (!is_null($jenisFilter)) {
        $base->where('t.jenis_transaksi', $jenisFilter);
    }

    // Lanjut agregasi (tetap seperti sebelumnya)
    $agg = $base->groupBy('t.no_transaksi');

    $x = DB::query()->fromSub($agg, 'x')
        ->leftJoin('dat_pelanggan as pl', function($j){
            $j->on('pl.id_pelanggan', '=', 'x.id_kontak')
              ->where('x.jenis_transaksi', '=', 1);
        })
        ->leftJoin('dat_pemasok as ps', function($j){
            $j->on('ps.id_pemasok', '=', 'x.id_kontak')
              ->where('x.jenis_transaksi', '=', 2);
        })
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
public function datatableInventaris()
{
    $rows = DatBarangModel::query()
        ->leftJoin('dat_pemasok as ps', 'ps.kode_pemasok', '=', 'dat_barang.kode_pemasok')
        ->select([
            'dat_barang.id_barang',
            'dat_barang.nama_barang',
            'ps.nama_pemasok',
            'dat_barang.stok_akhir',       
            'dat_barang.satuan_ukur',
            'dat_barang.harga_satuan',
            'dat_barang.harga_jual',   
            'dat_barang.created_at',
            'dat_barang.updated_at',  
        ])
        ->orderBy('dat_barang.updated_at', 'desc')
        ->get()
        ->map(function($r){
            return [
                'id_barang'     => $r->id_barang,
                'nama_barang'   => $r->nama_barang ?: '-',
                'pemasok'       => $r->nama_pemasok ?: '-',
                'stok'          => (float) $r->stok_akhir,
                'satuan'        => $r->satuan_ukur ?: '-',
                'harga_satuan'  => (float) $r->harga_satuan,  
                'total'         => (float) $r->harga_jual,  

                'created_at'    => $r->created_at 
                                    ? $r->created_at->format('d/m/Y H:i') 
                                    : null,
                'updated_at'    => $r->updated_at 
                                    ? $r->updated_at->format('d/m/Y H:i') 
                                    : null,
            ];
        });

    return response()->json(['data' => $rows]);
}
public function getBarangByPemasok(Request $request)
{
    $pemasokId = $request->input('pemasok_id');

    $pemasok = PemasokModel::find($pemasokId);

    if (!$pemasok) {
        return response()->json([
            'ok' => false,
            'message' => 'Pemasok tidak ditemukan'
        ], 404);
    }

    $barang = DatBarangModel::where('kode_pemasok', $pemasok->kode_pemasok)->get();

    return response()->json([
        'ok' => true,
        'data' => $barang
    ]);
}



public function getBarangSemua(Request $request)
{
    $barang = DatBarangModel::orderBy('nama_barang')->get();

    return response()->json([
        'ok'   => true,
        'data' => $barang
    ]);
}

  public function kasbank()
    {
        return view('transaksi.kasbank'); 
    }

public function updateBarang(Request $r)
{
    $barang = DatBarangModel::find($r->id_barang);
    if (!$barang) {
        return response()->json(['ok' => false, 'message' => 'Barang tidak ditemukan']);
    }

    $barang->update([
        'nama_barang'   => $r->nama_barang,
        'satuan_ukur'   => $r->satuan,
        'harga_satuan'  => (int) preg_replace('/\D/', '', $r->harga_satuan), // pastikan angka bersih
        'harga_jual'    => (int) preg_replace('/\D/', '', $r->harga_jual),
    ]);

    return response()->json(['ok' => true, 'message' => 'Data barang berhasil diperbarui']);
}

public function suppliers()
{
    $rows = \DB::table('dat_utang as u')
        ->leftJoin('dat_pemasok as p', 'p.kode_pemasok', '=', 'u.kode_pemasok')
        ->whereNotNull('u.kode_pemasok')
        ->where('u.kode_pemasok', '!=', '')
        ->where('u.status', 0) // hanya yang belum lunas
        ->select(
            'u.kode_pemasok',
            \DB::raw('COALESCE(p.nama_pemasok, "") as nama_pemasok')
        )
        ->distinct()
        ->orderBy('u.kode_pemasok')
        ->get();

    return response()->json(['ok' => true, 'data' => $rows]);
}

public function bySupplier(Request $r)
{
    $r->validate(['kode_pemasok' => 'required|string|max:50']);

    $rows = \DB::table('dat_utang')
        ->select(
            'no_transaksi',
            \DB::raw('SUM(nominal) AS nominal'),
            \DB::raw('MIN(tanggal) AS tanggal')
        )
        ->where('kode_pemasok', $r->kode_pemasok)
        ->where('status', 0) // hanya yang belum lunas
        ->groupBy('no_transaksi')
        ->orderByDesc(\DB::raw('MIN(tanggal)'))
        ->limit(50)
        ->get();

    return response()->json(['ok' => true, 'data' => $rows]);
}
public function nominal(Request $r)
{
    $r->validate([
        'kode_pemasok' => 'required|string|max:50',
        'no_transaksi' => 'required|string|max:50',
    ]);

    $nominal = \DB::table('dat_utang')
        ->where('kode_pemasok', $r->kode_pemasok)
        ->where('no_transaksi', $r->no_transaksi)
        ->where('status', 0) // hanya yang belum lunas
        ->sum('nominal');

    return response()->json(['ok' => true, 'nominal' => (float) $nominal]);
}

public function customers()
{
    $rows = \DB::table('dat_piutang as dp')
        ->leftJoin('dat_pelanggan as p', 'p.id_pelanggan', '=', 'dp.id_pelanggan')
        ->where('dp.status', 0) // hanya yang belum lunas
        ->select(
            'dp.id_pelanggan',
            \DB::raw('COALESCE(p.nama_pelanggan,"") as nama_pelanggan'),
            \DB::raw('SUM(dp.nominal) as total'),
            \DB::raw('MIN(dp.tanggal) as terakhir')
        )
        ->groupBy('dp.id_pelanggan', 'p.nama_pelanggan')
        ->orderBy('p.nama_pelanggan')
        ->get();

    return response()->json(['ok' => true, 'data' => $rows]);
}

public function byCustomer(Request $r)
{
    $r->validate(['id_pelanggan' => 'required|integer']);

    $rows = \DB::table('dat_piutang')
        ->where('id_pelanggan', $r->id_pelanggan)
        ->where('status', 0)
        ->select(
            'no_transaksi',
            \DB::raw('SUM(nominal) as nominal'),
            \DB::raw('MIN(tanggal) as tanggal')
        )
        ->groupBy('no_transaksi')
        ->orderByDesc(\DB::raw('MIN(tanggal)'))
        ->limit(50)
        ->get();

    return response()->json(['ok' => true, 'data' => $rows]);
}

public function nominalpiutang(Request $r)
{
    $r->validate([
        'id_pelanggan' => 'required|integer',
        'no_transaksi' => 'required|string|max:50',
    ]);

    $nominal = \DB::table('dat_piutang')
        ->where('id_pelanggan', $r->id_pelanggan)
        ->where('no_transaksi', $r->no_transaksi)
        ->where('status', 0)
        ->sum('nominal');

    return response()->json(['ok' => true, 'nominal' => (float)$nominal]);
}



}
