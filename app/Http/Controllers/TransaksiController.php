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
        'tipe'              => ['required', Rule::in(['Penjualan','Inventaris'])],
        'tipe_pembayaran'   => ['required','integer','in:1,2'],
        'tanggal'           => ['required','string'],
        'pelanggan_id'      => ['nullable','integer'],
        'party_id'          => ['nullable','integer'],
        'no_transaksi'      => ['nullable','string','max:50'],
        'biaya_lain'        => ['nullable','numeric'],
        'diskon_persen'     => ['nullable','numeric','min:0','max:100'],
        'pajak_persen'      => ['nullable','numeric','min:0','max:100'],
        'apply_pajak'       => ['required','boolean'],
        'items'             => ['required','array','min:1'],
        'items.*.barang_id' => ['required','integer','exists:dat_barang,id_barang'],
        'items.*.qty'       => ['required','numeric','min:0.0001'],
        'items.*.satuan'    => ['nullable','string','max:50'],
        'items.*.harga'     => ['required','numeric','min:0'],
        'items.*.subtotal'  => ['nullable','numeric','min:0'],
        'items.*.harga_mentah' => ['nullable','numeric','min:0'],

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
        $qty = (float) $it['qty'];
        $harga = ($request->tipe === 'Penjualan')
            ? (float) $it['hargajual']
            : (float) $it['harga'];

              $hargaMentah = isset($it['harga_mentah'])
        ? (float) $it['harga_mentah']
        : $harga;

        $subtotal = (float) round($qty * $harga);       
        return [
            'barang_id' => (int) $it['barang_id'],
            'qty'       => $qty,
            'satuan'    => $it['satuan'] ?? null,
            'harga'     => $harga,
            'harga_mentah'  => $hargaMentah,
            'subtotal'  => $subtotal,
            'total'     => (float) round($qty * $harga),
        ];
    });

    $tipePembayaran = (int) $request->input('tipe_pembayaran', 1);
    $tipePembayaran = ($tipePembayaran === 2) ? 2 : 1;
    $subtotal      = (float) $items->sum('total');
    $biayaLain     = (float) ($request->biaya_lain ?? 0);
    $diskonPersen  = (float) ($request->diskon_persen ?? 0);
    $pajakPersen   = (float) ($request->pajak_persen ?? 11);
    $afterDisc     = (float) round($subtotal * (1 - ($diskonPersen / 100)));
    $applyPajak    = $request->boolean('apply_pajak');
    $pajakNominal  = $applyPajak ? (float) round($afterDisc * ($pajakPersen / 100)) : 0.0;
    $grandTotal    = max(0, $afterDisc + $pajakNominal + $biayaLain);

    $idKontak      = $request->input('party_id', $request->input('pelanggan_id'));
    $jenisCode     = $request->tipe === 'Penjualan' ? 1 : 2;
    $prefix        = $jenisCode === 1 ? 'P' : 'S';

    // ============================
    // Generate Nomor Transaksi
    // ============================
    $noTransaksi = trim((string) $request->no_transaksi);
    $valid = preg_match('/^[PS]\d{7}$/', $noTransaksi);

    if (!$valid) {
        $lastNo = DB::table('dat_transaksi')
            ->where('no_transaksi', 'like', $prefix . '%')
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
        // ============================
        // Simpan Transaksi & Update Stok
        // ============================
        $rows = [];
        $runningPajak = 0;
        $runningBiaya = 0;
        $subtotalSafe = max(1, $subtotal);

        foreach ($items->values() as $idx => $it) {
            $isLast = ($idx === $items->count() - 1);

            $base = (float) $it['total'];
            $afterDiscItem = (float) round($base * (1 - ($diskonPersen / 100)));
            $share = $subtotalSafe > 0 ? ($base / $subtotalSafe) : 0.0;
            $pajakItem = $applyPajak ? (int) round($afterDiscItem * ($pajakPersen / 100)) : 0;
            $biayaItem = (int) round($biayaLain * $share);

            if ($isLast) {
                $pajakItem = $applyPajak ? (int) ($pajakNominal - $runningPajak) : 0;
                $biayaItem = (int) ($biayaLain - $runningBiaya);
            }

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
                'hpp'               => 0,
                'harga_mentah'      => (float) $it['harga_mentah'],
                'pajak'             => $pajakItem,
                'subtotal'          => (float) $it['subtotal'],
                'total'             => $totalItem,
                'biaya_lain'        => (float) $biayaLain,
                'diskon'             => (float) $diskonPersen,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $runningPajak += $pajakItem;
            $runningBiaya += $biayaItem;

            // Update stok
            $barang = DatBarangModel::where('id_barang', (int) $it['barang_id'])->lockForUpdate()->first();
            if (!$barang) {
                throw new \RuntimeException("Barang ID {$it['barang_id']} tidak ditemukan.");
            }

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
                // Tunai
                $akunDebet  = 1;   // Kas
                $akunKredit = 15;  // Pendapatan Penjualan
                $tambahAkun17 = true;
            } else {
                // Kredit
                $akunDebet  = 20;   // Piutang Usaha
                $akunKredit = 15;  // Pendapatan Penjualan
                $tambahAkun17 = true;
            }
        } else {
            // PEMBELIAN (INVENTARIS)
            if ($tipePembayaran === 1) {
                // Tunai
                $akunDebet  = 6;   // Persediaan / Inventaris
                $akunKredit = 1;   // Kas
                 $tambahAkun17 = false;
            } else {
                // Kredit
                $akunDebet  = 6;   // Persediaan / Inventaris
                $akunKredit = 5;   // Utang Usaha
                 $tambahAkun17 = false;
            }
        }

        // Panggil helper jurnal
        $this->insertJurnalSimple(
            $tglSql,
            (float) $grandTotal,
            $keterangan,
            $akunDebet,
            $akunKredit,
            2, 1,
            $noTransaksi,
            $request->tipe
        );
            if ($tambahAkun17) {
                $periode = Carbon::parse($tglSql)->format('Y-m');

                // Naikkan saldo berjalan akun 17 (treat sebagai debit)
                DB::table('mst_akun')
                    ->where('id', 17)
                    ->lockForUpdate()
                    ->increment('saldo_berjalan', (float) $grandTotal);
            }
        DB::commit();
        return response()->json([
            'ok' => true,
            'message' => 'Transaksi tersimpan dan jurnal dibuat',
            'no_transaksi' => $noTransaksi
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
    }
}

 private function insertJurnalSimple(
    string $tanggal,
    float $nominal,
    ?string $keterangan,
    int $akunDebet,
    int $akunKredit,
    int $jenisLaporanDebet = 2,
    int $jenisLaporanKredit = 1,
    string $noReferensi = 'tes',
    string $modulSumber = 'tes'
): void {
    $idJurnal = DB::table('dat_header_jurnal')->insertGetId([
        'tgl_transaksi' => $tanggal,
        'no_referensi'  => $noReferensi,
        'keterangan'    => $keterangan,
        'modul_sumber'  => $modulSumber,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    DB::table('dat_detail_jurnal')->insert([
        [
            'id_jurnal'     => $idJurnal,
            'id_akun'       => $akunDebet,
            'jml_debit'     => $nominal,
            'jml_kredit'    => 0,
            'jenis_laporan' => $jenisLaporanDebet,
            'created_at'    => now(),
            'updated_at'    => now(),
        ],
        [
            'id_jurnal'     => $idJurnal,
            'id_akun'       => $akunKredit,
            'jml_debit'     => 0,
            'jml_kredit'    => $nominal,
            'jenis_laporan' => $jenisLaporanKredit,
            'created_at'    => now(),
            'updated_at'    => now(),
        ],
    ]);

    $periode = Carbon::parse($tanggal)->format('Y-m');
    foreach ([
        $akunDebet  => ['debit' => $nominal, 'kredit' => 0],
        $akunKredit => ['debit' => 0, 'kredit' => $nominal],
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
                    'ttl_debit'   => (float)$bukbes->ttl_debit + (float)$val['debit'],
                    'ttl_kredit'  => (float)$bukbes->ttl_kredit + (float)$val['kredit'],
                    'saldo_akhir' => (float)$bukbes->saldo_akhir + ((float)$val['debit'] - (float)$val['kredit']),
                    'updated_at'  => now(),
                ]);
        } else {
            DB::table('dat_buku_besar')->insert([
                'id_akun'     => $akunId,
                'periode'     => $periode,
                'ttl_debit'   => (float)$val['debit'],
                'ttl_kredit'  => (float)$val['kredit'],
                'saldo_akhir' => (float)$val['debit'] - (float)$val['kredit'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    DB::table('mst_akun')->where('id', $akunDebet)->lockForUpdate()->increment('saldo_berjalan', $nominal);
    DB::table('mst_akun')->where('id', $akunKredit)->lockForUpdate()->decrement('saldo_berjalan', $nominal);
}

    
public function datatableTransaksi()
{
    $agg = DB::table('dat_transaksi as t')
        ->select(
            't.no_transaksi',
            DB::raw('MIN(t.tgl) as tgl'),
            DB::raw('MAX(t.jenis_transaksi) as jenis_transaksi'),
            DB::raw('MAX(t.id_kontak) as id_kontak'),
            DB::raw('SUM(t.jml_barang) as qty'),
            DB::raw('SUM(t.total) as total')
        )
        ->whereIn('t.jenis_transaksi', [1, 2])
        ->groupBy('t.no_transaksi');

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


}
