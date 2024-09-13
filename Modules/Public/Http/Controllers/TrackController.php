<?php

namespace Modules\Public\Http\Controllers;

use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

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
        $data          = null;

        if ($key) {
            try {
                $data = SisPermohonan::find(Crypt::decryptString($key));
            } catch (DecryptException $e) {
                $error_message = $e->getMessage();
            }
        }

        return view('public::track.certification')->with([
            'error' => $error_message,
            'data'  => $data
        ]);
    }

    public function certificate($key = null)
    {
        // generating link
        // $encrypted = Crypt::encryptString($id);
        // $qrcode_link = url("/track/certificate/$encrypted");

        $error_message = null;
        $data          = null;

        if ($key) {
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

        // catch Illegal mix of collations
        try {
            $data = SisPelangganSertifikasi::where('cust_sert_nomor_sertifikat', trim($request->cert_number))->first();
        } catch (\Exception $e) {
            Log::withContext([
                'module' => $this->module,
                'url'    => $this->url,
                'method' => __FUNCTION__,
                'input'  => $request->all(),
            ])->error($e->getMessage());
            $data = null;
        }

        return view('public::track.certificate')->with([
            'data'        => $data ?? null,
            'cert_number' => trim($request->cert_number)
        ]);
    }
}
