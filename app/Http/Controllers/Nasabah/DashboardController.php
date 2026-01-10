<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return view('nasabah.index', [
            'user' => $user,
            'role' => 'Nasabah',
            'title' => 'Testing'
        ]);
    }
}
