<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MstAkunModel;
use App\Models\DatAkunModel;

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

}
