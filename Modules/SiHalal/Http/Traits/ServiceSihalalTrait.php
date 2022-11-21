<?php

namespace Modules\SiHalal\Http\Traits;

use App\Exceptions\ExpectedException;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

trait ServiceSihalalTrait
{
    private PendingRequest $http;
    private int $cacheSecond = 120; // 2 menit

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (empty(config('app.sihalal_api_server'))) {
            throw new ExpectedException("SIHALAL URL CANNOT EMPTY");
        }
        $this->http = Http::baseUrl(config('app.sihalal_api_server'))
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(120);
    }

    /**
     * mustLogin
     * Untuk memastikan cookie tersedia pada session dan menambahkan ke http headers
     *
     * @throws Exception
     */
    private function mustLogin(): void
    {
        $cookie = Session::get(config('app.sihalal_cookie_name'));
        if (empty($cookie)) {
            $this->postLogin();
        }
        $this->http->withHeaders(['Cookie' => $cookie]);
    }

    /**
     * @throws Exception
     */
    public function postLogin()
    {
        $apiUrl     = "/auth/signin";
        $apiPayload = [
            'userid'   => config("app.sihalal_username"),
            'password' => config("app.sihalal_password"),
        ];

        $login_process = $this->http->post($apiUrl, $apiPayload);

        $body_data    = $login_process->json();
        $headers_data = $login_process->headers();

        if ($body_data['status'] == 'success') {
            $cookie_string = str_replace("\r\n", "", str_replace("; Path=/; HttpOnly", "", implode(';', $headers_data['Set-Cookie'])));
            session()->put(config('app.sihalal_cookie_name'), $cookie_string);
        } else {
            throw new ExpectedException('Login Error');
        }
    }

    public function postLoginGetCookie()
    {
        $login_process = Http::asForm()->timeout(3)->post(config("app.sihalal_api_server") . '/auth/signin', [
            'userid'   => config("app.sihalal_username"),
            'password' => config("app.sihalal_password"),
        ]);

        $body_data    = $login_process->json();
        $headers_data = $login_process->headers();

        if ($body_data['status'] == 'success') {
            $cookie_string = str_replace("\r\n", "", str_replace("; Path=/; HttpOnly", "", implode(';', $headers_data['Set-Cookie'])));
            session()->put(config('app.sihalal_cookie_name'), $cookie_string);
            return $cookie_string;
        } else {
            return null;
        }
    }

    public function getPermohonan($status_list = '10010')
    {
        $cacheKey = implode(":", [
            self::class,
            'getPermohonan',
            $status_list,
            config("app.sihalal_lph_id")
        ]);

        // get from Cache
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        // get from API
        $this->mustLogin();
        $apiUrl = "/api/v1/data_list/$status_list/" . config("app.sihalal_lph_id");

        $result = $this->http->get($apiUrl)->json();
        if (isset($result['message']) && $result['message'] == "You are not logged in") {
            $this->postLogin();
            return $this->getPermohonan($status_list);
        } else if (!empty($result) && isset($result['status']) && in_array($result['status'], [200, 201])) {
            Cache::add($cacheKey, $result, $this->cacheSecond);
            return $result;
        }
        return [];
    }

    public function getPermohonanDetail($reg_id)
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/reg/$reg_id";
        return $this->http->get($apiUrl)->json();
    }

    public function postUpdatePermohonan($reg_id, $status_list = 'Ajuan')
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/data_list/updatestatus";
        $apiPayload = [
            "status"        => "$status_list",
            "reg_id"        => $reg_id,
            "lph_mapped_id" => config('app.sihalal_lph_id')
        ];

        return $this->http->post($apiUrl, $apiPayload)->json();
    }

    public function getBiaya()
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/costs?order_dir=asc&limit=500000";
        return $this->http->get($apiUrl)->json();
    }

    public function postAddBiaya($data)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/costs";
        $apiPayload = [
            "id_reg"     => $data['id_reg'],
            "keterangan" => $data['keterangan'],
            "qty"        => $data['qty'],
            "harga"      => $data['harga']
        ];

        return $this->http->post($apiUrl, $apiPayload)->json();
    }

    public function putUpdateBiaya($data, $id_biaya)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/costs/$id_biaya";
        $apiPayload = [
            "id_reg"     => $data['id_reg'],
            "keterangan" => $data['keterangan'],
            "qty"        => $data['qty'],
            "harga"      => $data['harga']
        ];

        return $this->http->put($apiUrl, $apiPayload)->json();
    }

    public function deletBiaya($id_biaya)
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/costs/$id_biaya";
        $result = $this->http->delete($apiUrl)->json();

        if (isset($result["status"]))
            return false;
        else
            return true;

    }

    public function getListJadwalAudit()
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/audit_schedule?order_dir=asc&limit=500000";

        return $this->http->get($apiUrl)->json();
    }

    public function postAddListJadwalAudit($data)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/audit_schedule";
        $apiPayload = [
            "id_reg"       => $data['id_reg'],
            "jadwal_awal"  => $data['jadwal_awal'],
            "jadwal_akhir" => $data['jadwal_akhir'],
            "jml_hari"     => $data['jml_hari']
        ];

        return $this->http->post($apiUrl, $apiPayload)->json();
    }

    public function putUpdateListJadwalAudit($data, $id_audit)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/audit_schedule/$id_audit";
        $apiPayload = [
            "id_reg"       => $data['id_reg'],
            "jadwal_awal"  => $data['jadwal_awal'],
            "jadwal_akhir" => $data['jadwal_akhir'],
            "jml_hari"     => $data['jml_hari']
        ];

        return $this->http->put($apiUrl, $apiPayload)->json();
    }

    public function deleteListJadwalAudit($id_audit)
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/audit_schedule/$id_audit";

        $result = $this->http->delete($apiUrl)->json();

        if (isset($result["status"]))
            return false;
        else
            return true;
    }

    public function getListJadwalAuditor()
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/reg_auditor?order_dir=asc&limit=500000";
        return $this->http->get($apiUrl)->json();
    }

    public function getRefAuditor()
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/check_auditor_list/" . config("app.sihalal_lph_maped_id");
        return $this->http->get($apiUrl)->json();
    }

    public function postAddListJadwalAuditor($data)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/reg_auditor";
        $apiPayload = [
            "id_reg"     => $data['id_reg'],
            "auditor_id" => $data['auditor_id'],
            "create_by"  => $data['create_by']
        ];

        return $this->http->post($apiUrl, $apiPayload)->json();
    }

    public function deleteListJadwalAuditor($id_audit_person)
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/reg_auditor/$id_audit_person";

        $result = $this->http->delete($apiUrl)->json();

        if (isset($result["status"]))
            return false;
        else
            return true;
    }

    public function getAuditResult()
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/audit_result?order_dir=asc&limit=50000";

        return $this->http->get($apiUrl)->json();
    }

    public function postProsesAudit1($data)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/audit_result";
        $apiPayload = [
            "id_reg"      => $data['id_reg'],
            "tgl_selesai" => $data['tgl_selesai'],
            "keterangan"  => $data['keterangan'],
            "hasil_audit" => $data['hasil_audit']
        ];

        return $this->http->post($apiUrl, $apiPayload)->json();
    }

    public function postProsesAudit2($data)
    {
        $this->mustLogin();
        $apiUrl     = "/api/v1/audit_result";
        $apiPayload = [
            "id_reg"      => $data['id_reg'],
            "tgl_selesai" => $data['tgl_selesai'],
            "keterangan"  => $data['keterangan'],
            "hasil_audit" => $data['hasil_audit']
        ];

        $file = fopen($data['file'], 'r');

        return $this->http
            ->withHeaders(['Connection' => 'keep-alive'])
            ->attach('file', $file, $data['nama_file'])
            ->post($apiUrl, $apiPayload)->json();
    }

    public function getInvoice2()
    {
        $login_data = $this->postLoginGetCookie();
        if (!is_null($login_data)) {
            return Http::withHeaders([
                'Cookie' => $login_data,
            ])
                // ?order_dir=asc&limit=50000
                ->get("/api/v1/invoice/" . config("app.sihalal_lph_maped_id"))->json();
        } else {
            return [];
        }
    }

    // Error not result data
    public function getInvoice()
    {
        $cacheKey = implode(":", [
            self::class,
            'getInvoice',
            config("app.sihalal_lph_maped_id")
        ]);

        // get from Cache
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        // get from Api
        $this->mustLogin();
        $apiUrl = "/api/v1/invoice/" . config("app.sihalal_lph_maped_id");

        $result = $this->http->get($apiUrl)->json();
        if (!empty($result) && isset($result['status']) && in_array($result['status'], [200, 201])) Cache::add($cacheKey, $result, $this->cacheSecond);

        return $result;
    }

    public function putInvoiceLunas($id_inv)
    {
        $this->mustLogin();
        $apiUrl = "/api/v1/invoice/" . $id_inv;

        return $this->http->put($apiUrl)->json();
    }
}
