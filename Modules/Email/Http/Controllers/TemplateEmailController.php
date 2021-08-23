<?php

namespace Modules\Email\Http\Controllers;


use App\Models\BbkkpSis\MasterEmailTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Email\Http\Traits\EmailTrait;

class TemplateEmailController extends Controller
{
    use EmailTrait;

    public string $module = self::class;
    private string $url = 'email/template';

    public function index()
    {
        return view("email::template.index")->with(['url' => $this->url, 'module' => $this->module]);
    }

    public function create()
    {
        return view('email::template.create')->with(['url' => $this->url, 'module' => $this->module, 'email_parser' => $this->commonParser()]);
    }


    public function store(Request $request)
    {
        $request->validate([
            "template_code" => 'required|string|unique:App\Models\BbkkpSis\MasterEmailTemplate,template_code',
        ]);

        $newData = $request->except('_token');
        $newData['template_uuid'] = Str::uuid();
        $newData['template_code'] = strtoupper($newData['template_code']);
        MasterEmailTemplate::create($newData);
        return redirect()->back()->with("message", "Sukses menambahkan template " . $request['template_mail_subject']);
    }


    public function edit($uuid)
    {
        $data = MasterEmailTemplate::where("template_uuid", $uuid)->firstOrFail();
        return view('email::template.edit')->with(['url' => $this->url, 'module' => $this->module, 'data' => $data, 'email_parser' => $this->commonParser()]);
    }

    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'template_code' => ['required', 'string', Rule::unique('master_email_template')->ignore($request['template_id'], 'template_id'),],
        ])->validate();


        $data = MasterEmailTemplate::find($request['template_id']);
        $data->template_code = strtoupper($request['template_code']);
        $data->template_desc = $request['template_desc'];
        $data->template_mail_subject = $request['template_mail_subject'];
        $data->template_mail_body = $request['template_mail_body'];
        $data->save();

        return redirect()->back()->with("message", "Update template berhasil");
    }

    public function destroy($uuid)
    {
        try {
            $data = MasterEmailTemplate::where("template_uuid", $uuid)->firstOrFail();
            if ($data->delete()) {
                return responseJSON(200, [], "Data berhasil dihapus");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }

    public function previewEmail(Request $request)
    {
        $request->validate(['template_id' => 'required|integer']);
        $data = MasterEmailTemplate::findOrFail($request['template_id']);

        $response = [
            'template_mail_subject' => $data->template_mail_subject,
            'template_mail_body' => $data->template_mail_body,
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
            case 'tinymce-uploadimage':
                $response = $this->ajax_tinymce_uploadimage($request);
                break;
            default:
                abort(404);
        }

        return $response;
    }

    private function ajax_datagrid(Request $request)
    {
        $data = MasterEmailTemplate::select("*");
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
            $x['template_id'] = $d->template_id;
            $x['template_uuid'] = $d->template_uuid;
            $x['template_code'] = $d->template_code;
            $x['template_desc'] = $d->template_desc;
            $x['template_mail_subject'] = $d->template_mail_subject;
            $x['template_created_at'] = $d->template_created_at?->format("Y-m-d H:i:s");
            $x['template_updated_at'] = $d->template_updated_at?->format("Y-m-d H:i:s");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_tinymce_uploadimage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimetypes:image/jpeg,image/png|max:1000', // 1MB
            ]);

            $img = $request->file('file');
            $imgName = $img->hashName();
            $img->move(public_path(config('email.email_template_image_url')), $imgName);
            $publicUrl = asset(config('email.email_template_image_url') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }
}
