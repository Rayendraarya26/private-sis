<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SysUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PegawaiController extends Controller
{
    public $module = self::class;
    private $url = 'admin/data/pegawai';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Data Pegawai'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('admin::data_pegawai.index')->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SysUser::whereNotIn('ug_group_id', [1, 3])
            ->leftJoin('sys_user_group', 'ug_user_id', '=', 'user_id');
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }

        $data = $data->groupBy('user_id');

        // Total
        $total = $data->get()->count();
        // Pagination
        $data->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['user_id']         = $d->user_id;
            $x['user_fullname']   = $d->user_fullname;
            $x['user_email']      = $d->user_email;
            $x['user_is_active']  = ucwords($d->user_is_active);
            $x['user_is_banned']  = ucwords($d->user_is_banned);
            $x['user_picture']    = url(config("app.url_profile_image") . $d->user_picture);
            $x['user_last_login'] = !empty($d->user_last_login) ? Carbon::createFromFormat('Y-m-d H:i:s', $d->user_last_login)->format("d M Y, h:i:s") : $d->user_last_login;
            $x['user_created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $d->user_created_at)->format("d M Y, h:i:s");
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }
}
