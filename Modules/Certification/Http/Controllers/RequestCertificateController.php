<?php

namespace Modules\Certification\Http\Controllers;

use Illuminate\Routing\Controller;

class RequestCertificateController extends Controller
{
    public function index()
    {
        return view('certification::request_certificate.index');
    }
}
