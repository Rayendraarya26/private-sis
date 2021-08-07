<?php

namespace Modules\Certification\Http\Controllers;

use Illuminate\Routing\Controller;

class VerifCertificateController extends Controller
{
    public function index()
    {
        return view('certification::verif_certificate.index');
    }
}
