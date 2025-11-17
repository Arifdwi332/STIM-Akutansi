<?php

namespace App\Http\Controllers;

use App\Models\MstUserModel;
use App\Models\MstAkunModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'nama_umkm'    => ['required', 'string', 'max:150'],
            'email'        => ['required', 'email', 'max:255', 'unique:mst_user,email'],
            'password'     => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($request) {

            $user = MstUserModel::create([
                'nama_pemilik' => $request->nama_pemilik,
                'nama_umkm'    => $request->nama_umkm,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
            ]);

            $templateAkuns = DB::table('template_akun')->get();

            foreach ($templateAkuns as $tmpl) {
                MstAkunModel::create([
                    'id'             => $tmpl->id,    
                    'kode_akun'      => $tmpl->kode_akun,
                    'nama_akun'      => $tmpl->nama_akun,
                    'kategori_akun'  => $tmpl->kategori_akun ?? null,
                    'jenis_laporan'  => $tmpl->jenis_laporan ?? null,
                    'saldo_berjalan' => 0,         // atau $tmpl->saldo_awal ?? 0
                    'status_aktif'   => 1,         // atau $tmpl->status_aktif ?? 1
                    'created_by'     => $user->user_id,
                ]);
            }

        });

        return redirect()
            ->back()
            ->with('success', 'Registrasi berhasil. Akun-akun default berhasil dibuat.');
    }

     public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = MstUserModel::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah'])
                ->withInput();
        }

        session([
            'user_id'    => $user->user_id,
            'user_name'  => $user->nama_pemilik,
            'umkm_name'  => $user->nama_umkm,
            'user_email' => $user->email,
        ]);

        $request->session()->regenerate();

        return redirect()->to('/'); 
    }


}
