<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Walnut\Permissions\Models\UserHistory;
use Walnut\Permissions\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Permissions {

	use HasApiTokens;

	public static function getTokens($user_id){
		$user = User::whereId($request->user)->first();
		$tokens = PersonalAccesToken::where("tokenable_id", $user->id)->get();
		return $tokens;
	}

	public static function getCurrentToken(){
		if (Auth::check()) {
			if (Cache::get(Auth::user()->id . "Permission_tokenx")) {
				return Cache::get(Auth::user()->id . "Permission_tokenx");
			}
			$tokens = Cache::remember(Auth::user()->id . "Permission_tokenx", 3600, function () {
    		return PersonalAccesToken::where('tokenable_id', Auth::user()->id)->get();
			});
			return $tokens;
		}else{
			return '';
		}

	}

	public static function tokenCheck(){
		if (Auth::check()) {
			$tokens = PersonalAccesToken::where('tokenable_id', Auth::user()->id)->get()
			if (json_decode($tokens) == []) {
				$permissionGroups = config('permission.roles')[Auth::user()->role->name];
				$permissions = [];
				foreach($permissionGroups as $group){
					foreach (config('permission.groups')[$group] as $value) {
						$permissions[$value] = "on";
					}
					$permissions[$group] = 'on';
				}
				$response = Http::post(config('url.docker') . '/api/access', ['permissions' => $permissions, 'user_id' => Auth::user()->id, 'token_name' => 'dashboard']);
				return redirect("home");
			}
		}else{
			return '';
		}
	}

	public static function generateKey($user, $name, $permissions){
		return;
	}
  public static function anyCheck($permissions){
		$guards = explode(' ', $permissions);
		foreach ($guards as $guard) {
			if(\App\Permissions::checkSanctum($guard)){
				return true;
			}
		}
		return false;
	}
	public static function multiCheck($permissions){
		$guards = explode($permissions, ',');
		foreach ($guards as $guard) {
			if(!\App\Permissions::checkSanctum($guard)){
				return false;
			}
		}
		return true;
	}
	public static function checkSanctum($permission){
		$valid = false;
		// dd(json_decode(Permissions::getTokens($user))[0]->abilities);
		if (Auth::check()) {
			$token = PersonalAccesToken::where('tokenable_id', Auth::user()->id)->get();
			$abilities = json_encode($token->abilities);
			//$abilities = Auth::user()->accesToken->abilities;
			// $abilities = collect(json_decode(Permissions::getCurrentToken()));
			// $abilities->each(function($value, $key) use (&$permission, &$valid){
			// 	$abilities = json_decode($value->abilities);
			// 	$delimiters = ['.', ':'];
			// 	if (in_array("*", $abilities)) {
			// 		$valid = true;
			// 	}
			// 	foreach($delimiters as $delimiter){
			// 		if(strpos($permission, $delimiter)){
			// 			if (explode($delimiter, $permission)[1] == "*") {
			// 				foreach($abilities as $ability){
			// 					if (explode($delimiter, $ability)[0] ==  explode($delimiter, $permission)[0]){
			// 						$valid =  true;
			// 					}
			// 				}
			// 			}
			// 		}
			// 	}
			// 	if (in_array($permission, $abilities)) {
			// 		$valid =  true;
			// 	}
			// });
			if(str_contains($abilities, $permission)) {$valid = true;}
			elseif(str_contains($abilities, "\"*\"")) {$valid = true;}

			return $valid;
		}
	}

}
