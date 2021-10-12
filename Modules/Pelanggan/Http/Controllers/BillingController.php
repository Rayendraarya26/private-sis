<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Routing\Controller;

class BillingController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/billing';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Billing'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::billing.index')->with($parser);
    }
}
