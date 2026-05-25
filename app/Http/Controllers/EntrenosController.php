<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;

class EntrenosController extends Controller
{
    public function index()
    {
        $markers = Marker::where('user_id', auth()->id())->get();

        return view('entrenos.index', compact('markers'));
    }
}

