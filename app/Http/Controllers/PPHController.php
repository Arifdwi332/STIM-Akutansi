<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PPHController extends Controller
{
    

  

    // View utama (tabel kosong, nanti diisi JS)
    public function index()
    {
        return view('pph.index');
    }

    // Sumber data JSON untuk DataTables
    public function data(Request $request)
    {
        $tahun  = (int) $request->input('tahun', date('Y'));
        $rows   = $this->buildPphBulanan($tahun);

        return response()->json([
            'ok'   => true,
            'data' => $rows,
        ]);
    }

    /**
     * Hitung PPH bulanan:
     * - Penjualan barang : dat_transaksi.jenis_transaksi = 1, SUM(total) per bulan
     * - Penjualan jasa   : dat_detail_jurnal.id_akun = 17, SUM(jml_kredit) per bulan
     * - Akumulasi        : kumulatif total penjualan per tahun
     * - Pajak sebulan    : 0,5% x total_bulan setelah akumulasi > 500.000.000
     */
    protected function buildPphBulanan(int $tahun): array
    {
        $userId = $this->userId;

        // 1) Penjualan barang per bulan
        $barangPerBulan = DB::table('dat_transaksi')
            ->where('jenis_transaksi', 1)
            ->where('created_by', $userId)
            ->whereYear('tgl', $tahun)              // kolom tgl di dat_transaksi
            ->selectRaw('MONTH(tgl) as bulan, SUM(total) as total_barang')
            ->groupByRaw('MONTH(tgl)')
            ->pluck('total_barang', 'bulan');

        // 2) Penjualan jasa per bulan
        $jasaPerBulan = DB::table('dat_detail_jurnal')
            ->where('id_akun', 17)                  // akun penjualan jasa
            ->where('created_by', $userId)
            ->whereYear('tanggal', $tahun)          // kolom tanggal di dat_detail_jurnal
            ->selectRaw('MONTH(tanggal) as bulan, SUM(jml_kredit) as total_jasa')
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total_jasa', 'bulan');

        $namaBulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $plafonTanpaPph = 500_000_000; // Rp 500 juta
        $tarifPph       = 0.005;       // 0,5%
        $akumulasi      = 0;
        $rows           = [];

        for ($i = 1; $i <= 12; $i++) {
            $penjBarang = (float) ($barangPerBulan[$i] ?? 0);
            $penjJasa   = (float) ($jasaPerBulan[$i] ?? 0);
            $totalBulan = $penjBarang + $penjJasa;

            $akumulasi += $totalBulan;

            $pajakBulan = 0;
            if ($akumulasi > $plafonTanpaPph && $totalBulan > 0) {
                $pajakBulan = $totalBulan * $tarifPph;
            }

            $rows[] = [
                'bulan'               => $namaBulan[$i],
                'tahun'               => $tahun,
                'penjualan_barang'    => $penjBarang,
                'penjualan_jasa'      => $penjJasa,
                'akumulasi_penjualan' => $akumulasi,
                'pajak_sebulan'       => $pajakBulan,
            ];
        }

        return $rows;
    }
}
