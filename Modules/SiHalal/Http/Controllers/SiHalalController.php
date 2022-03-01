<?php

namespace Modules\SiHalal\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SiHalalController extends Controller
{
    public function index()
    {
        return view('sihalal::index');
    }
}
