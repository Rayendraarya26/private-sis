<?php

namespace Modules\Email\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HistoryEmailReminderController extends Controller
{
    public function index()
    {
        return view("email::outbox_reminder.index");
    }

    public function previewEmail(Request $request)
    {

    }

    public function ajax(Request $request)
    {

    }
}
