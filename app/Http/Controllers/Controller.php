<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // [NEW] properti userId yang bisa dipakai di semua controller turunan
    protected ?int $userId = null;

    public function __construct()
    {
        // middleware ini jalan di setiap request ke controller turunan
        $this->middleware(function ($request, $next) {
            $this->userId = session('user_id'); // ambil dari session login
            return $next($request);
        });
    }
}
