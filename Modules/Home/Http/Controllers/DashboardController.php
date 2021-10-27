<?php

namespace Modules\Home\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        //dd(session()->all());
        return view('home::dashboard.index');
    }
}
