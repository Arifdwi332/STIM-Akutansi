<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BepController extends Controller
{
    public function index()
    {
        return view('bep.index'); 
    }
}
