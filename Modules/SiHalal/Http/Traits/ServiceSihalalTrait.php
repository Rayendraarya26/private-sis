<?php

namespace Modules\SiHalal\Http\Traits;

use Illuminate\Support\Facades\Http;
use Exception;

trait ServiceSihalalTrait
{
    /**
     * @throws Exception
     */
    public function postLogin()
    {
        $login_process = Http::asForm()->timeout(3)->post(config("app.sihalal_api_server").'/auth/signin', [
			'userid' => config("app.sihalal_username"),
			'password' => config("app.sihalal_password"),
		]);
		
		$body_data = $login_process->json();
		$headers_data = $login_process->headers();
		
		try {
			if($body_data['status'] == 'success'){
				$cookie_string = str_replace("\r\n","",str_replace("; Path=/; HttpOnly","",implode(';', $headers_data['Set-Cookie'])));
				session()->put('sihalal_cookie', $cookie_string);
			}
			else{
				return responseJSON(401, null, "Error login");
			}
		} catch (Exception $e) {
			return $e->getMessage();
        }
    }
	
	public function postLoginGetCookie()
    {
        $login_process = Http::asForm()->timeout(3)->post(config("app.sihalal_api_server").'/auth/signin', [
			'userid' => config("app.sihalal_username"),
			'password' => config("app.sihalal_password"),
		]);
		
		$body_data = $login_process->json();
		$headers_data = $login_process->headers();
		
		if($body_data['status'] == 'success'){
			$cookie_string = str_replace("\r\n","",str_replace("; Path=/; HttpOnly","",implode(';', $headers_data['Set-Cookie'])));
			session()->put('sihalal_cookie', $cookie_string);
			return $cookie_string;
		}
		else{
			return null;
		}
    }
	
	public function getPermohonan($status_list = '10010')
    {
		$cookie_string = session()->get('sihalal_cookie');
		$result = Http::withHeaders([
			'Cookie' => $cookie_string,
		])
		->get(config("app.sihalal_api_server")."/api/v1/data_list/$status_list/".config("app.sihalal_lph_id"))->json();
		if(isset($result['message']) == "You are not logged in"){
			$this->postLogin();
			return $this->getPermohonan($status_list);
		}
		else{
			return $result;
		}
    }
	
	public function getPermohonanDetail($reg_id)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			->get(config("app.sihalal_api_server")."/api/v1/reg/$reg_id")->json();			
    }
	
	public function postUpdatePermohonan($status_list = 'Ajuan', $reg_id)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->post(config("app.sihalal_api_server")."/api/v1/data_list/updatestatus",  
				[
					"status" => "$status_list"
					, "reg_id" => $reg_id
					, "lph_mapped_id" => config('app.sihalal_lph_id')
				]
			)->json();		
    }
	
	public function getBiaya()
    {
		$cookie_string = session()->get('sihalal_cookie');
		$result = Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			->get(config("app.sihalal_api_server")."/api/v1/costs?order_dir=asc&limit=500000")->json();
			
		return $result;
    }
	
	public function postAddBiaya($data)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->post(config("app.sihalal_api_server")."/api/v1/costs",  
				[
					"id_reg" => $data['id_reg']
					, "keterangan" => $data['keterangan']
					, "qty" => $data['qty']
					, "harga" => $data['harga']
				]
			)->json();
    }
	
	public function putUpdateBiaya($data, $id_biaya)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->put(config("app.sihalal_api_server")."/api/v1/costs/$id_biaya",  
				[
					"id_reg" => $data['id_reg']
					, "keterangan" => $data['keterangan']
					, "qty" => $data['qty']
					, "harga" => $data['harga']
				]
			)->json();		
    }
	
	public function deletBiaya($id_biaya)
    {
		$cookie_string = session()->get('sihalal_cookie');
		$result = Http::withHeaders([
				'Cookie' => $cookie_string,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'X-Powered-By' => 'Express',
				'Vary' => 'Origin',
				'Access-Control-Allow-Credentials' => true,
			])
			->delete(config("app.sihalal_api_server")."/api/v1/costs/$id_biaya")->json();
			
		if(isset($result["status"]))
			return false;
		else
			return true;
			
    }
	
	public function getListJadwalAudit()
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			->get(config("app.sihalal_api_server")."/api/v1/audit_schedule?order_dir=asc&limit=500000")->json();
    }
	
	public function postAddListJadwalAudit($data)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->post(config("app.sihalal_api_server")."/api/v1/audit_schedule",  
				[
					"id_reg" => $data['id_reg']
					, "jadwal_awal" => $data['jadwal_awal']
					, "jadwal_akhir" => $data['jadwal_akhir']
					, "jml_hari" => $data['jml_hari']
				]
			)->json();
    }
	
	public function putUpdateListJadwalAudit($data, $id_audit)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->put(config("app.sihalal_api_server")."/api/v1/audit_schedule/$id_audit",  
				[
					"id_reg" => $data['id_reg']
					, "jadwal_awal" => $data['jadwal_awal']
					, "jadwal_akhir" => $data['jadwal_akhir']
					, "jml_hari" => $data['jml_hari']
				]
			)->json();
    }
	
	public function deleteListJadwalAudit($id_audit)
    {
		$cookie_string = session()->get('sihalal_cookie');
		$result = Http::withHeaders([
				'Cookie' => $cookie_string,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'X-Powered-By' => 'Express',
				'Vary' => 'Origin',
				'Access-Control-Allow-Credentials' => true,
			])
			->delete(config("app.sihalal_api_server")."/api/v1/audit_schedule/$id_audit")->json();
		
		if(isset($result["status"]))
			return false;
		else
			return true;
    }
	
	public function getListJadwalAuditor()
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			->get(config("app.sihalal_api_server")."/api/v1/reg_auditor?order_dir=asc&limit=500000")->json();
    }
	
	public function getRefAuditor()
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			// ->get(config("app.sihalal_api_server")."/api/v1/check_auditor_list/{map_id}")->json();
			->get(config("app.sihalal_api_server")."/api/v1/check_auditor_list/".config("app.sihalal_lph_maped_id"))->json();
    }
	
	public function postAddListJadwalAuditor($data)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->post(config("app.sihalal_api_server")."/api/v1/reg_auditor",  
				[
					"id_reg" => $data['id_reg']
					, "auditor_id" => $data['auditor_id']
					, "create_by" => $data['create_by']
				]
			)->json();
    }
	
	public function deleteListJadwalAuditor($id_audit_person)
    {
		$cookie_string = session()->get('sihalal_cookie');
		$result = Http::withHeaders([
				'Cookie' => $cookie_string,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'X-Powered-By' => 'Express',
				'Vary' => 'Origin',
				'Access-Control-Allow-Credentials' => true,
			])
			->delete(config("app.sihalal_api_server")."/api/v1/audit_schedule/$id_audit_person")->json();
		
		if(isset($result["status"]))
			return false;
		else
			return true;
    }
	
	public function getAuditResult()
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			->get(config("app.sihalal_api_server")."/api/v1/audit_result?order_dir=asc&limit=50000")->json();
    }
	
	public function postProsesAudit1($data)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->post(config("app.sihalal_api_server")."/api/v1/audit_result",  
				[
					"id_reg" => $data['id_reg']
					, "tgl_selesai" => $data['tgl_selesai']
					, "keterangan" => $data['keterangan']
					, "hasil_audit" => $data['hasil_audit']
				]
			)->json();
    }
	
	public function postProsesAudit2($data)
    {
		$cookie_string = session()->get('sihalal_cookie');
		$file = fopen($data['file'], 'r');
		
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'Connection' => 'keep-alive',
			])
			->attach(
				'file', $file, $data['nama_file']
			)
			->post(config("app.sihalal_api_server")."/api/v1/audit_result",  
				[
					"id_reg" => $data['id_reg']
					, "tgl_selesai" => $data['tgl_selesai']
					, "keterangan" => $data['keterangan']
					, "hasil_audit" => $data['hasil_audit']
				]
			)->json();
    }
	
	public function getInvoice2()
    {
		$login_data = $this->postLoginGetCookie();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			// ?order_dir=asc&limit=50000
			->get(config("app.sihalal_api_server")."/api/v1/invoice/".config("app.sihalal_lph_maped_id"))->json();
		}
		else{
			return [];
		}
    }
	
	// Error not result data
	public function getInvoice()
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
			])
			// ?order_dir=asc&limit=50000
			->get(config("app.sihalal_api_server")."/api/v1/invoice/".config("app.sihalal_lph_maped_id"))->json();
    }
	
	public function putInvoiceLunas($id_inv)
    {
		$cookie_string = session()->get('sihalal_cookie');
		return Http::withHeaders([
				'Cookie' => $cookie_string,
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
			])
			->put(config("app.sihalal_api_server")."/api/v1/invoice/".$id_inv)->json();
    }
}
