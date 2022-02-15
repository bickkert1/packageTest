<?php

namespace App;

use Illuminate\Support\Facades\Http;
use App\Permissions;
use Illuminate\Support\Facades\Auth;

class GHttp {

	public $CLIENT;

    public function __construct()
    {
        // return self::withToken(\Illuminate\Support\Facades\Auth::user()->token);
    }
    public static function get($url, $data = []){
        if(Auth::check()){
        	$token = json_decode(Permissions::getCurrentToken())[0]->token;
        	$data["__token__"] = $token;
        	$response = Http::withHeaders([
        		'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'X-Auth-Origin' => 'Dashboard',
        	])->withToken("Bearer ".$token)->get($url, $data);
        	return $response;
        }
        else{
            $response = Http::get($url, $data);
            return $response;
        }
    }
		public static function put($url, $data = []){
        if(Auth::check()){
        	$token = json_decode(Permissions::getCurrentToken())[0]->token;
        	$data["__token__"] = $token;
        	$response = Http::withHeaders([
        		'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'X-Auth-Origin' => 'Dashboard',
        	])->withToken("Bearer ".$token)->put($url, $data);
        	return $response;
        }
        else{
            $response = Http::get($url, $data);
            return $response;
        }
    }
    public static function post($url, $data){
        if (Auth::check()) {
           $token = json_decode(Permissions::getCurrentToken())[0]->token;
            $data["__token__"] = $token;
            $response = Http::withToken("Bearer ".$token)->post($url, $data);
            return $response;
        }
        else{
            $response = Http::post($url, $data);
            return $response;
        }
    }
    private static function checkForError($response){
        if (isset($response->error)) {
            switch ($response->getMessage()) {
                case 'Unauthorized':
                    \Redirect::to("/unautherised")->send();
                    break;

                default:
                    \Redirect::to("/login")->send();
                    break;
            }
        }
    }

}
