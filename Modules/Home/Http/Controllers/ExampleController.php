<?php

namespace Modules\Home\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Routing\Controller;

class ExampleController extends Controller
{
    public function domPdf()
    {
        $data = [
            'nama' => "aldino kemal"
        ];
        $pdf  = PDF::loadView('home::example.pdf', $data);
        return $pdf->download('invoice.pdf');
    }
}
