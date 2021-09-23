<?php

namespace Modules\Certification\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DataSertifikatController extends Controller
{
    public function index()
    {
        return view('certification::data_sertifikat.index');
    }
}
