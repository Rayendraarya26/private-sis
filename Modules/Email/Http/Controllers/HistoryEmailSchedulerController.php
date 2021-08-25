<?php

namespace Modules\Email\Http\Controllers;


use App\Models\BbkkpSisLog\LogEmailOutbox;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class HistoryEmailSchedulerController extends Controller
{
    public $module = self::class;
    private $url = 'email/history/scheduler';

    public function index()
    {
        return view("email::outbox_scheduler.index")->with(['url' => $this->url, 'module' => $this->module]);
    }

    public function previewEmail(Request $request)
    {
        $request->validate(['outbox_id' => 'required|integer']);
        $data = LogEmailOutbox::findOrFail($request['outbox_id']);

        $response = [
            'outbox_title' => $data->outbox_title,
            'outbox_message' => $data->outbox_message,
        ];
        return responseJSON(200, $response, "data ditemukan");
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        $response = null;
        switch ($request['action']) {
            case 'datagrid':
                $response = $this->ajax_datagrid($request);
                break;
            default:
                abort(404);
        }

        return $response;
    }

    private function ajax_datagrid(Request $request)
    {
        $data = LogEmailOutbox::where('outbox_type', "scheduler");
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['outbox_id'] = $d->outbox_id;
            $x['outbox_uuid'] = $d->outbox_uuid;
            $x['outbox_reply_to'] = $d->outbox_reply_to;
            $x['outbox_from_name'] = $d->outbox_from_name;
            $x['outbox_from_email'] = $d->outbox_from_email;
            $x['outbox_to_name'] = $d->outbox_to_name;
            $x['outbox_to_email'] = $d->outbox_to_email;
            $x['outbox_title'] = $d->outbox_title;
            $x['outbox_message'] = $d->outbox_message;
            $x['outbox_read'] = $d->outbox_read;
            $x['outbox_read_at'] = $d->outbox_read_at?->format("Y-m-d H:i:s");
            $x['outbox_created_at'] = $d->outbox_created_at?->format("Y-m-d H:i:s");
            $x['outbox_updated_at'] = $d->outbox_updated_at?->format("Y-m-d H:i:s");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
