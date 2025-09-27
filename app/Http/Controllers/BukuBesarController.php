<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MstAkunModel;
use App\Models\PemasokModel;
use App\Models\PelangganModel;
use App\Models\DatAkunModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BukuBesarController extends Controller
{
    public function index()
    {
        $mstAkun = MstAkunModel::orderBy('kode_akun')->get(['id','kode_akun','nama_akun']);

        return view('buku_besar.index', compact('mstAkun'));
    }

    public function jurnalData()
    {
        $data = collect([
            ['tanggal'=>'19/09/2025','keterangan'=>'tes','akun'=>'1130 - Piutang','debet'=>null,'kredit'=>50000000,'tipe'=>'Kredit'],
            ['tanggal'=>'20/09/2025','keterangan'=>'tes','akun'=>'2101 - Kas','debet'=>50000000,'kredit'=>null,'tipe'=>'Debet'],
        ]);

        return DataTables::of($data)->make(true);
    }

    public function bukuBesarData()
    {
        $data = collect([
            ['akun'=>'1130 - Piutang','tanggal'=>'19/09/2025','debet'=>null,'kredit'=>50000000,'saldo'=>-50000000,'tipe'=>'Kredit'],
            ['akun'=>'2101 - Kas','tanggal'=>'20/09/2025','debet'=>50000000,'kredit'=>null,'saldo'=>50000000,'tipe'=>'Debet'],
        ]);

        return DataTables::of($data)->make(true);
    }

    public function storeakun(Request $r)
    {
        $data = $r->validate([
            'kode_akun'     => ['required','string','max:50','unique:mst_akun,kode_akun'],
            'nama_akun'     => ['required','string','max:150'],
            'kategori_akun' => ['required','string','max:100'],
            'saldo_awal'    => ['nullable','string'],
            'saldo_berjalan'=> ['nullable','string'],
            'status_aktif'  => ['nullable'],
        ]);

        $akun = MstAkunModel::create([
            'kode_akun'      => $data['kode_akun'],
            'nama_akun'      => $data['nama_akun'],
            'kategori_akun'  => $data['kategori_akun'],
            'saldo_awal'     => $data['saldo_awal'] ?? '0',
            'saldo_berjalan' => $data['saldo_berjalan'] ?? '0',
            'status_aktif'   => (bool)($data['status_aktif'] ?? 1),
        ]);

        return response()->json([
            'ok'   => true,
            'data' => $akun,
        ]);
    }

     public function listMstAkun()
    {
        $items = MstAkunModel::orderBy('kode_akun')
            ->get(['id','kode_akun','nama_akun']);

        return response()->json([
            'ok'   => true,
            'data' => $items,
        ]);
    }

   public function storeSubAkun(Request $request)
    {
        $data = $request->validate([
            'mst_akun_id' => ['required', 'exists:mst_akun,id'],
            'nama_sub'    => ['required', 'string', 'max:150'],
        ]);

        // ambil akun induk
        $parent = MstAkunModel::findOrFail($data['mst_akun_id']);

        $last = DatAkunModel::where('mst_akun_id', $parent->id)
                ->orderByDesc('kode_sub')
                ->value('kode_sub');

        $next = 1;

        if ($last) {
            $suffix = (int) substr($last, -3);
            $next = $suffix + 1;
        }

        $kodeSub = $parent->kode_akun . str_pad($next, 3, '0', STR_PAD_LEFT);

        $sub = DatAkunModel::create([
            'mst_akun_id'   => $parent->id,
            'kode_sub'      => $kodeSub,
            'nama_sub'      => $data['nama_sub'],
            'saldo_awal'    => '0',
            'saldo_berjalan'=> '0',
            'status_aktif'  => true,
        ]);

        return response()->json(['ok' => true, 'data' => $sub]);
    }

    public function listAkunFlat(Request $r)
{
    $masters = MstAkunModel::with([
        'subAkuns:id,mst_akun_id,kode_sub,nama_sub'
    ])
    ->orderBy('kode_akun')
    ->get(['id','kode_akun','nama_akun','kategori_akun']);

    $rows = [];
    foreach ($masters as $m) {
        $rows[] = [
            'is_sub'        => false,
            'id'            => $m->id,
            'kode'          => $m->kode_akun,      
            'nama_akun'     => $m->nama_akun,      
            'sub_akun'      => null,            
            'kategori_akun' => $m->kategori_akun,
        ];

        foreach ($m->subAkuns as $s) {
            $rows[] = [
                'is_sub'        => true,
                'id'            => $s->id,
                'kode'          => $s->kode_sub,     
                'nama_akun'     => null,             
                'sub_akun'      => $s->nama_sub,     
                'kategori_akun' => $m->kategori_akun 
            ];
        }
    }

    if ($r->boolean('dt')) {
        return DataTables::of($rows)->make(true);
    }

    return response()->json(['ok' => true, 'data' => $rows]);
}

public function subAkunList(Request $r)
{
    $id = $r->get('mst_akun_id');
    $items = DatAkunModel::where('mst_akun_id', $id)
        ->orderBy('kode_sub')
        ->get(['id','kode_sub','nama_sub']);

    return response()->json(['ok' => true, 'data' => $items]);
}
  public function storeSaldoAwal(Request $r)
    {
        $mstId   = $r->input('mst_akun_id');
        $subIds  = $r->input('sub_akun_id', []);   
        $nominal = $r->input('nominal', []);       

        if (!$mstId) {
            return response()->json(['ok'=>false,'message'=>'Kode akun wajib diisi'], 422);
        }

        $clean = static function($v){
            $n = (int) preg_replace('/[^\d\-]/', '', (string)$v);
            return max(0, $n);
        };

        $nominal = array_map($clean, $nominal);

        // samakan panjang array (jaga-jaga)
        $count = min(count($subIds), count($nominal));

        // hitung total
        $total = 0;
        for ($i=0; $i<$count; $i++) {
            $total += $nominal[$i];
        }

        try {
            DB::transaction(function () use ($mstId, $subIds, $nominal, $count, $total) {
                // update saldo induk (tambah ke nilai yg ada)
                /** @var MstAkunModel $mst */
                $mst = MstAkunModel::lockForUpdate()->findOrFail($mstId);

                $currAwal  = (int) preg_replace('/[^\d\-]/', '', (string)($mst->saldo_awal ?? '0'));
                $currJalan = (int) preg_replace('/[^\d\-]/', '', (string)($mst->saldo_berjalan ?? '0'));

                $mst->saldo_awal      = (string)($currAwal + $total);
                $mst->saldo_berjalan  = (string)($currJalan + $total);
                $mst->save();

                $bySub = [];
                for ($i=0; $i<$count; $i++) {
                    $sid = $subIds[$i] ?? null;
                    $val = $nominal[$i] ?? 0;
                    if (!$sid || $val <= 0) continue;
                    $bySub[$sid] = ($bySub[$sid] ?? 0) + $val;
                }

                if (!empty($bySub)) {
                    $subs = DatAkunModel::whereIn('id', array_keys($bySub))
                            ->lockForUpdate()
                            ->get();

                    foreach ($subs as $sub) {
                        $currAwal  = (int) preg_replace('/[^\d\-]/', '', (string)($sub->saldo_awal ?? '0'));
                        $currJalan = (int) preg_replace('/[^\d\-]/', '', (string)($sub->saldo_berjalan ?? '0'));
                        $add       = $bySub[$sub->id];

                        $sub->saldo_awal     = (string)($currAwal + $add);
                        $sub->saldo_berjalan = (string)($currJalan + $add);
                        $sub->save();
                    }
                }
            });

            return response()->json([
                'ok'     => true,
                'message'=> 'Saldo awal berhasil disimpan',
                'total'  => $total,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Gagal menyimpan saldo awal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
        
    public function storetransaksi(Request $request)
    {
        $request->validate([
            'tipe' => 'required|string',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'akun_debet_id' => 'nullable|exists:mst_akun,id',
            'akun_kredit_id' => 'nullable|exists:mst_akun,id',
        ]);

        if ($request->tipe === 'Manual') {
            $request->validate([
                'akun_debet_id'  => 'required|exists:mst_akun,id|different:akun_kredit_id',
                'akun_kredit_id' => 'required|exists:mst_akun,id',
            ]);
        }
        

        $tipe     = $request->tipe;
        $nominal  = (float) $request->nominal;
        $tanggal  = \Carbon\Carbon::parse($request->tanggal)->toDateString();
        $ket      = $request->keterangan ?? null;
        $akunD    = $request->akun_debet_id;
        $akunK    = $request->akun_kredit_id;

        DB::beginTransaction();
        try {
            if ($tipe === 'Manual') {
                // 1) Header jurnal
                $idJurnal = DB::table('dat_header_jurnal')->insertGetId([
                    'tgl_transaksi' => $tanggal,
                    'no_referensi'  => 'tes',
                    'keterangan'    => $ket,
                    'modul_sumber'  => 'tes',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                // 2) Detail jurnal (Debet)
                DB::table('dat_detail_jurnal')->insert([
                    'id_jurnal'   => $idJurnal,
                    'id_akun'     => $akunD,
                    'jml_debit'   => $nominal,
                    'jml_kredit'  => 0,
                    'id_proyek'   => null,
                    'kode_pajak'  => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // 3) Detail jurnal (Kredit)
                DB::table('dat_detail_jurnal')->insert([
                    'id_jurnal'   => $idJurnal,
                    'id_akun'     => $akunK,
                    'jml_debit'   => 0,
                    'jml_kredit'  => $nominal,
                    'id_proyek'   => null,
                    'kode_pajak'  => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // 4) Buku Besar (per-periode)
                foreach ([$akunD => ['debit' => $nominal, 'kredit' => 0],
                        $akunK => ['debit' => 0, 'kredit' => $nominal]] as $akunId => $val) {

                    $periode = Carbon::parse($tanggal)->format('Y-m');

                    $bukbes = DB::table('dat_buku_besar')
                        ->where('id_akun', $akunId)
                        ->where('periode', $periode)
                        ->first();

                    if ($bukbes) {
                        DB::table('dat_buku_besar')
                            ->where('id_bukbes', $bukbes->id_bukbes)
                            ->update([
                                'ttl_debit'   => (float)$bukbes->ttl_debit + (float)$val['debit'],
                                'ttl_kredit'  => (float)$bukbes->ttl_kredit + (float)$val['kredit'],
                                'saldo_akhir' => (float)$bukbes->saldo_akhir + (float)$val['debit'] - (float)$val['kredit'],
                                'updated_at'  => now(),
                            ]);
                    } else {
                        \DB::table('dat_buku_besar')->insert([
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

              
                $saldoDeb = (float) \DB::table('mst_akun')->where('id', $akunD)->value('saldo_berjalan');
                $saldoKrd = (float) \DB::table('mst_akun')->where('id', $akunK)->value('saldo_berjalan');

                \DB::table('mst_akun')->where('id', $akunD)->update([
                    'saldo_berjalan' => $saldoDeb + $nominal,
                    'updated_at'     => now(),
                ]);

                \DB::table('mst_akun')->where('id', $akunK)->update([
                    'saldo_berjalan' => $saldoKrd - $nominal,
                    'updated_at'     => now(),
                ]);
               
            }
           elseif (in_array($tipe, [
            'Bayar Gaji',
            'Bayar Listrik',
            'Bayar Utang Bank',
            'Beli Peralatan Tunai',
            'Beli ATK Tunai',
            'Pengambilan Pribadi',
            'Pinjam Uang di Bank',
            'Pendapatan Bunga',
            'Setoran Pemilik',
        ], true)) {

            $map = [
                'Bayar Gaji'         =>  [7,  1, 1, 2],
                'Bayar Listrik'      =>  [8,  1, 1, 2],
                'Bayar Utang Bank'   => [9,  1, 1, 2],
                'Beli Peralatan Tunai' =>  [10, 1, 2, 2],
                'Beli ATK Tunai'     => [11, 1, 2, 2],
                'Pengambilan Pribadi'     => [12, 1, 2, 2],
                'Pinjam Uang di Bank'     => [1, 14, 2, 2],
                'Pendapatan Bunga'     => [1, 15, 2, 1],
                'Setoran Pemilik'     => [1, 16, 2, 2],
            ];

            [$akunD, $akunK, $jlD, $jlK] = $map[$tipe];

            $this->insertJurnalSimple(
                $tanggal,
                (float)$nominal,
                $ket,
                (int)$akunD,
                (int)$akunK,
                (int) $jlD,   
                (int) $jlK    
            );
            if (in_array($tipe, ['Bayar Gaji', 'Bayar Listrik', 'Bayar Utang Bank'], true)) {
            DB::table('mst_akun')
                ->where('id', 17)
                ->lockForUpdate()
                ->decrement('saldo_berjalan', $nominal);
            }

        }


            \DB::commit();
            return response()->json(['ok' => true, 'message' => 'Transaksi tersimpan']);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function insertJurnalSimple(
        string $tanggal,
        float $nominal,
        ?string $keterangan,
        int $akunDebet,
        int $akunKredit,
        int $jenisLaporanDebet = 1,
        int $jenisLaporanKredit = 1,
        string $noReferensi = 'tes',
        string $modulSumber = 'tes'
    ): void {
        // 1) Header jurnal
        $idJurnal = DB::table('dat_header_jurnal')->insertGetId([
            'tgl_transaksi' => $tanggal,
            'no_referensi'  => $noReferensi,
            'keterangan'    => $keterangan,
            'modul_sumber'  => $modulSumber,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 2) Detail jurnal (insert sekaligus 2 baris)
        DB::table('dat_detail_jurnal')->insert([
            [
                'id_jurnal'     => $idJurnal,
                'id_akun'       => $akunDebet,
                'jml_debit'     => $nominal,
                'jml_kredit'    => 0,
                'id_proyek'     => null,
                'kode_pajak'    => null,
                'jenis_laporan' => $jenisLaporanDebet,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id_jurnal'     => $idJurnal,
                'id_akun'       => $akunKredit,
                'jml_debit'     => 0,
                'jml_kredit'    => $nominal,
                'id_proyek'     => null,
                'kode_pajak'    => null,
                'jenis_laporan' => $jenisLaporanKredit,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // 3) Buku Besar (per-periode)
        $periode = Carbon::parse($tanggal)->format('Y-m');

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

        // 4) Update saldo_berjalan (atomic, dengan lock)
        $affD = DB::table('mst_akun')->where('id', $akunDebet)->lockForUpdate()->increment('saldo_berjalan', $nominal);
        $affK = DB::table('mst_akun')->where('id', $akunKredit)->lockForUpdate()->decrement('saldo_berjalan', $nominal);

        if ($affD === 0) {
            throw new \RuntimeException("Akun debet (ID {$akunDebet}) tidak ditemukan di mst_akun.");
        }
        if ($affK === 0) {
            throw new \RuntimeException("Akun kredit (ID {$akunKredit}) tidak ditemukan di mst_akun.");
        }
    }
      public function getJurnal(Request $request)
    {
        $search   = trim($request->get('search', ''));
        $dateFrom = $request->get('date_from'); // 'YYYY-MM-DD'
        $dateTo   = $request->get('date_to');   // 'YYYY-MM-DD'
        $page     = max(1, (int) $request->get('page', 1));
        $perPage  = max(1, min(100, (int) $request->get('per_page', 20)));

        $q = DB::table('dat_detail_jurnal as d')
        ->join('dat_header_jurnal as h', 'h.id_jurnal', '=', 'd.id_jurnal')
        ->join('mst_akun as a', 'a.id', '=', 'd.id_akun')
        ->select([
                'h.tgl_transaksi as tanggal',
                'h.keterangan',
                'a.nama_akun',
                'd.jml_debit as debet',
                'd.jml_kredit as kredit',
                DB::raw("COALESCE(h.modul_sumber, 'Manual') as tipe"),
                 'a.saldo_berjalan as saldo',
            ]);

        if ($search !== '') {
            $q->where(function($w) use ($search) {
                $w->where('h.keterangan', 'like', "%{$search}%")
                  ->orWhere('a.nama_akun', 'like', "%{$search}%")
                  ->orWhere('a.kode_akun', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $q->whereDate('h.tgl_transaksi', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('h.tgl_transaksi', '<=', $dateTo);
        }

        $total = (clone $q)->count();
        $rows  = $q->orderBy('h.tgl_transaksi', 'desc')
                   ->offset(($page-1)*$perPage)
                   ->limit($perPage)
                   ->get()
                   ->map(function($r){
                        // pastikan numeric
                        $r->debet  = (float) $r->debet;
                        $r->kredit = (float) $r->kredit;
                        $r->saldo  = (float) $r->saldo;
                        return $r;
                   });

        return response()->json([
            'ok'        => true,
            'data'      => $rows,
            'page'      => $page,
            'per_page'  => $perPage,
            'total'     => $total,
        ]);
    }

    
    public function getBukuBesar(Request $request)
    {
        $search   = trim($request->get('search', ''));
        $periode  = $request->get('periode'); 
        $page     = max(1, (int) $request->get('page', 1));
        $perPage  = max(1, min(100, (int) $request->get('per_page', 20)));

            $q = DB::table('dat_buku_besar as b')
            ->join('mst_akun as a', 'a.id', '=', 'b.id_akun')
            ->select([
                'a.nama_akun',
                'b.periode as tanggal', 
                'b.ttl_debit as debet',
                'b.ttl_kredit as kredit',
                'a.saldo_berjalan as saldo',
                 DB::raw("'Manual' as tipe"),
            ]);

        if ($search !== '') {
            $q->where(function($w) use ($search) {
                $w->where('a.nama_akun', 'like', "%{$search}%")
                  ->orWhere('a.kode_akun', 'like', "%{$search}%")
                  ->orWhere('b.periode', 'like', "%{$search}%");
            });
        }

        if ($periode) {
            $q->where('b.periode', $periode); 
        }

        $total = (clone $q)->count();
        $rows  = $q->orderBy('b.periode', 'desc')
                   ->orderBy('a.nama_akun')
                   ->offset(($page-1)*$perPage)
                   ->limit($perPage)
                   ->get()
                   ->map(function($r){
                        $r->debet  = (float) $r->debet;
                        $r->kredit = (float) $r->kredit;
                        $r->saldo  = (float) $r->saldo;
                        return $r;
                   });

        return response()->json([
            'ok'        => true,
            'data'      => $rows,
            'page'      => $page,
            'per_page'  => $perPage,
            'total'     => $total,
        ]);
    }
         public function listPemasok()
    {
        $items = PemasokModel::orderBy('kode_pemasok')
            ->get(['id_pemasok','kode_pemasok','nama_pemasok']);

        return response()->json([
            'ok'   => true,
            'data' => $items,
        ]);
    }
         public function listPelanggan()
    {
        $items = pelangganModel::orderBy('id_pelanggan')
            ->get(['id_pelanggan','nama_pelanggan']);

        return response()->json([
            'ok'   => true,
            'data' => $items,
        ]);
    }

}
