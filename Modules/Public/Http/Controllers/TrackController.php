<?php

namespace Modules\Public\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;

class TrackController extends Controller
{
    public $module = self::class;
    private $url = '/';

    public function certification($key)
    {
    	// generating link
	    // $encrypted = Crypt::encryptString($id);
	    // $qrcode_link = url("/track/certification/$encrypted");

    	$error_message = null;
    	$data = null;

    	if ($key)
    	{
	    	try {
	            $data = SisPermohonan::find(Crypt::decryptString($key));
	        } catch (DecryptException $e) {
	            $error_message = $e->getMessage();
	        }
    	}

        return view('public::track.certification')->with([
        	'error' => $error_message,
        	'data' => $data
        ]);
    }

    public function certificate($key = null)
    {
    	// generating link
	    // $encrypted = Crypt::encryptString($id);
	    // $qrcode_link = url("/track/certificate/$encrypted");

    	$error_message = null;
    	$data = null;

    	if ($key)
    	{
	    	try {
	            $data = SisPelangganSertifikasi::find(Crypt::decryptString($key));
	        } catch (DecryptException $e) {
	            $error_message = $e->getMessage();
	        }
    	}

    	$response = ['cert_number' => trim($data?->cust_sert_nomor_sertifikat)];

    	if ($error_message) $response['error'] = $error_message;
    	if ($data) $response['data'] = $data;

        return view('public::track.certificate')->with($response);
    }

    public function doTrackCertificate(Request $request)
    {
    	$request->validate([
            'cert_number' => 'required|string',
        ]);

    	$data = SisPelangganSertifikasi::where('cust_sert_nomor_sertifikat', trim($request->cert_number))->first();

        return view('public::track.certificate')->with([
        	'data' => $data ?? [],
        	'cert_number' => trim($request->cert_number)
        ]);
    }
}
