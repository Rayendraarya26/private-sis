<?php

namespace Modules\Public\Http\Controllers;

use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    public $module = self::class;
    private $url = '/';

    public function index()
    {
        return redirect('auth/login');
        // return view('public::public.index');
    }
}
