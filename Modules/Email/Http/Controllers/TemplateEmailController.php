<?php

namespace Modules\Email\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TemplateEmailController extends Controller
{

    public function index()
    {
        return view("email::template.index");
    }


    public function create()
    {
        return view('email::create');
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('email::show');
    }


    public function edit($id)
    {
        return view('email::edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
