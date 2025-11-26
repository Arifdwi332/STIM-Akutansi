<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PPHController extends Controller
{
    public function index()
    {
        return view('pph.index'); 
    }
}
