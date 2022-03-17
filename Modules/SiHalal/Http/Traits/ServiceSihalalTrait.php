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
	
	public function getPermohonan()
    {
		$login_data = $this->postLogin();
		if(!is_null($login_data)){
			return Http::withHeaders([
				'Cookie' => $login_data,
			])
			->get(config("app.sihalal_api_server").'/api/v1/data_list/10010/'.config("app.sihalal_unit_kode"))->json();
		}
		else{
			return [];
		}
    }
}
