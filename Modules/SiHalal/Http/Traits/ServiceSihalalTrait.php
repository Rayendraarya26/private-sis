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
		
		if($body_data['status'] == 'success'){
			$cookie_string = str_replace("\r\n","",str_replace("; Path=/; HttpOnly","",implode(';', $headers_data['Set-Cookie'])));
			return $cookie_string;
		}
		else{
			return null;
		}
    }
	
	public function getPermohonan($status_list = '10010')
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/data_list/$status_list/".config("app.sihalal_lph_id"))->json();
		}
		else{
			return [];
		}
    }
	
	public function getPermohonanDetail($reg_id)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/reg/$reg_id")->json();
		}
		else{
			return [];
		}
		return [];
    }
	
	public function postUpdatePermohonan($status_list = 'Ajuan', $reg_id)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function getBiaya()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/costs?order_dir=asc&limit=500000")->json();
		}
		else{
			return [];
		}
    }
	
	public function postAddBiaya($data)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function putUpdateBiaya($data, $id_biaya)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function deletBiaya($id_biaya)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			$rest = Http::withHeaders([
				'Cookie' => $login_data,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'X-Powered-By' => 'Express',
				'Vary' => 'Origin',
				'Access-Control-Allow-Credentials' => true,
			])
			->delete(config("app.sihalal_api_server")."api/v1/costs/$id_biaya")->json();
			
			if(isset($rest["status"]))
				return false;
			else
				return true;
		}
		else{
			return false;
		}
    }
	
	public function getListJadwalAudit()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/audit_schedule?order_dir=asc&limit=500000")->json();
		}
		else{
			return [];
		}
    }
	
	public function postAddListJadwalAudit($data)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function putUpdateListJadwalAudit($data, $id_audit)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function deleteListJadwalAudit($id_audit)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			$rest = Http::withHeaders([
				'Cookie' => $login_data,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'X-Powered-By' => 'Express',
				'Vary' => 'Origin',
				'Access-Control-Allow-Credentials' => true,
			])
			->delete(config("app.sihalal_api_server")."api/v1/audit_schedule/$id_audit")->json();
			
			if(isset($rest["status"]))
				return false;
			else
				return true;
		}
		else{
			return false;
		}
    }
	
	public function getListJadwalAuditor()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/reg_auditor?order_dir=asc&limit=500000")->json();
		}
		else{
			return [];
		}
    }
	
	public function getRefAuditor()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			// ->get(config("app.sihalal_api_server")."/api/v1/check_auditor_list/{map_id}")->json();
			->get(config("app.sihalal_api_server")."/api/v1/check_auditor_list/".config("app.sihalal_lph_maped_id"))->json();
		}
		else{
			return [];
		}
    }
	
	public function postAddListJadwalAuditor($data)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function deleteListJadwalAuditor($id_audit_person)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			$rest = Http::withHeaders([
				'Cookie' => $login_data,
				'Cache-Control' => 'no-cache',
				'Content-Type' => 'application/json',
				'User-Agent' => 'PostmanRuntime/7.29.0',
				'X-Powered-By' => 'Express',
				'Vary' => 'Origin',
				'Access-Control-Allow-Credentials' => true,
			])
			->delete(config("app.sihalal_api_server")."api/v1/audit_schedule/$id_audit_person")->json();
			
			if(isset($rest["status"]))
				return false;
			else
				return true;
		}
		else{
			return false;
		}
    }
	
	public function getAuditResult()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/audit_result?order_dir=asc&limit=50000")->json();
		}
		else{
			return [];
		}
    }
	
	public function postProsesAudit1($data)
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
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
		else{
			return [];
		}
    }
	
	public function getInvoice()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server")."/api/v1/invoice?order_dir=asc&limit=50000")->json();
		}
		else{
			return [];
		}
    }
}
