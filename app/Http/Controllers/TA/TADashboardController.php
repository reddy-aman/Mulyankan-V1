<?php

namespace App\Http\Controllers\TA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TADashboardController extends Controller
{
    public function index()
    {
        return view('ta.dashboard');
    }
}
