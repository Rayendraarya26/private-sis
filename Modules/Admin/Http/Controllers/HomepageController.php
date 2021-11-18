<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Support\Facades\DB;
use App\Models\BbkkpSis\PublicProfilPerusahaan;
use App\Models\BbkkpSis\PublicLembaga;
use App\Models\BbkkpSis\PublicSop;
use App\Models\BbkkpSis\PublicSocialMedia;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class HomepageController extends Controller
{
    public $module = self::class;
    private $url = 'admin/homepage';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Homepage'),
        ];

        $parse = [
        	'url' 			=> $this->url,
        	'module' 		=> $this->module,
        	'breadcrumbs' 	=> $breadcrumbs,
        	'company' 		=> PublicProfilPerusahaan::first(),
        	'lembaga_rows' 	=> PublicLembaga::all(),
        	'sop_rows' 		=> PublicSop::all(),
        	'socmed_rows' 	=> PublicSocialMedia::all(),
        ];
        
        return view('admin::homepage.index')->with($parse);
    }

    public function update(Request $request)
    {
    	// dd($request);
    	$request->validate([
            'company_name' 		=> 'required|string',
            'company_shortname' => 'required|string',
            'company_email' 	=> 'required|email|min:4',
            'app_name' 			=> 'required|string',
            'app_shortname' 	=> 'required|string',
        ]);

        return DB::transaction(function() use ($request)
        {
        	$data = PublicProfilPerusahaan::find(1);
	        $payload_company = [
	        	'profil_fullname_perusahaan'	=> $request->company_name,
	        	'profil_shortname_perusahaan'	=> $request->company_shortname,
	        	'profil_desc_perusahaan'		=> $request->company_desc,
	        	'profil_alamat_perusahaan'		=> $request->company_address,
	        	'profil_email_perusahaan'		=> $request->company_email,
	        	'profil_fax_perusahaan'			=> $request->company_fax,
	        	'profil_telp_perusahaan'		=> $request->company_telp,
	        	'profil_whatsapp_perusahaan'	=> $request->company_whatsapp,
	        	'profil_fullname_app'			=> $request->app_name,
	        	'profil_shortname_app'			=> $request->app_shortname,
	        	'profil_app_desc' 				=> $request->app_desc,
	        ];

	        $deleted_files = [];

	        $bg_file = $request->file('app_bg');
	        $icon_file = $request->file('app_icon');
	        $ketidakperpihakan_file = $request->file('app_ketidakberpihakan');

	        $app_bg_path = $this->upload_file($bg_file, "profile");
	        $app_icon_path = $this->upload_file($icon_file, "profile");
	        $app_ketidakberpihakan_path = $this->upload_file($ketidakperpihakan_file, "profile");

	        if ($app_icon_path)
	        {
	        	$payload_company['profil_app_icon'] = $app_icon_path;
	        	if ($data && $data->profil_app_icon) array_push($deleted_files, $data->profil_app_icon);
	        }

	        if ($app_bg_path)
	        {
	        	$payload_company['profil_background_image'] = $app_bg_path;
	        	if ($data && $data->profil_background_image) array_push($deleted_files, $data->profil_background_image);
	        }

	        if ($app_ketidakberpihakan_path)
	        {
	        	$payload_company['profil_ketidakperpihakan_file'] = $app_ketidakberpihakan_path;
	        	if ($data && $data->profil_ketidakperpihakan_file) array_push($deleted_files, $data->profil_ketidakperpihakan_file);
	        }

	        PublicProfilPerusahaan::updateOrCreate([
	        	'profil_id' => 1
	        ], $payload_company);

	        // lembaga
	        if ($request->lembaga)
	        {
	        	foreach ($request->lembaga as $index => $row)
	        	{
	        		$id = isset($row['id']) ? $row['id'] : null;
	        		$is_delete = isset($row['delete']) && $row['delete'] == 1 ? true : false ;
	        		$payload = [
	        			'lem_name' 			=> $row['name'],
	        			'lem_desc' 			=> $row['desc'],
	        			'lem_content' 		=> $row['content'],
	        			'lem_external_link' => $row['link'],
	        			'lem_status' 		=> isset($row['status']) && $row['status'] == 1 ? 1 : 0,
	        		];

	        		if ($is_delete)
	        		{
	        			if ($id) PublicLembaga::where('lem_id', $id)->delete();
	        		} else {
	        			if ($id) {
	        				PublicLembaga::updateOrCreate(['lem_id' => $id], $payload);
	        			} else {
	        				PublicLembaga::create($payload);
	        			}
	        		}
	        	}
	        }

	        // sop
	        if ($request->sop)
	        {
	        	foreach ($request->sop as $index => $row)
	        	{
	        		$id = isset($row['id']) ? $row['id'] : null;
	        		$is_delete = isset($row['delete']) && $row['delete'] == 1 ? true : false ;
	        		$is_delete_img = isset($row['delete_img']) && $row['delete_img'] == 1 ? true : false ;
	        		$payload = [
	        			'sop_name' 		=> $row['title'],
	        			'sop_desc' 		=> $row['desc'],
	        			'sop_status' 	=> isset($row['status']) && $row['status'] == 1 ? 1 : 0,
	        		];

	        		$sop_row = null;
	        		if ($id) $sop_row = PublicSop::find($id);
	        		
	        		if ($is_delete_img && $sop_row)
	        		{
	        			array_push($deleted_files, $sop_row->sop_image);
	        			$payload['sop_image'] = null;
	        		}

	        		if ($is_delete)
	        		{
	        			if ($id)
	        			{
	        				if ($sop_row)
	        				{
	        					if ($sop_row->sop_image) array_push($deleted_files, $sop_row->sop_image);
	        					$sop_row->delete();
	        				}
	        			}
	        		} else {
	        			$sop_image_path = $this->upload_file(isset($row['file']) ? $row['file'] : null, "sop");
	        			if ($sop_image_path) $payload['sop_image'] = $sop_image_path;
	        			if ($id) {
	        				if ($sop_row && $sop_image_path && $sop_row->sop_image) array_push($deleted_files, $sop_row->sop_image);
	        				PublicSop::updateOrCreate(['sop_id' => $id], $payload);
	        			} else {
	        				PublicSop::create($payload);
	        			}
	        		}
	        	}
	        }

	        // social media
	        if ($request->socmed)
	        {
	        	foreach ($request->socmed as $index => $row)
	        	{
	        		$id = isset($row['id']) ? $row['id'] : null;
	        		$is_delete = isset($row['delete']) && $row['delete'] == 1 ? true : false ;
	        		$payload = [
	        			'socmed_name' 		=> $row['name'],
	        			'socmed_icon_cls' 	=> $row['icon'],
	        			'socmed_link' 		=> $row['link'],
	        			'socmed_status' 	=> isset($row['status']) && $row['status'] == 1 ? 1 : 0,
	        		];

	        		if ($is_delete)
	        		{
	        			if ($id) PublicSocialMedia::where('socmed_id', $id)->delete();
	        		} else {
	        			if ($id) {
	        				PublicSocialMedia::updateOrCreate(['socmed_id' => $id], $payload);
	        			} else {
	        				PublicSocialMedia::create($payload);
	        			}
	        		}
	        	}
	        }

	        if (count($deleted_files))
	        {
	        	foreach ($deleted_files as $file_path)
	        	{
	        		$path = substr($file_path, 1);
	        		if (File::exists($path)) File::delete($path);
	        	}
	        }

        	return redirect($this->url)->with('message', "Data berhasil diperbarui");
        });
    }

    private function upload_file($file, $sub_path = 'images')
    {
    	if ($file && $file->isValid())
    	{
    		$path = "/homepage/$sub_path";
	        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
	        $file->move(public_path($path), $filename);
	        return $path.'/'.$filename;
    	}
    	return null;
    }
}