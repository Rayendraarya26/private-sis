<?php

namespace Modules\Pelanggan\Http\Controllers;

use Illuminate\Routing\Controller;

class ProfilPerusahaanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/profil-perusahaan';

    public function index()
    {
        return redirect(url("account/profile"));
    }
}
