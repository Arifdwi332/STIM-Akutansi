<?php

namespace App\Http\Controllers;

use App\Models\MstUserModel;
use Illuminate\Http\Request;
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

        MstUserModel::create([
            'nama_pemilik' => $request->nama_pemilik,
            'nama_umkm'    => $request->nama_umkm,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
