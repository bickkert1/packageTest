<?php

namespace Schoutentech\Permissions;

use Illuminate\Support\Facades\Auth;
use Schoutentech\Permissions\Models\UserHistory;
use Schoutentech\Permissions\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class Permissions {

	public static function genKey($permissions, $email){
		return Crypt::encryptString($email . "|". $permissions);
	}

	public static function check($permissionName){
		$permission = Permission::where("permission_name", $permissionName)->first();
		if (is_null($permission)) {
			return False;
		}
		$permissions = explode('|', Crypt::decryptString(Auth::user()->user_key))[1];

		if ($permissions[$permission->id - 1] == 1) {
			return true;
		}
		else {
			return false;
		}

	}
	public static function checkSanctum($permission){
		if (Auth::check()) {
			$abilities = collect(Auth::user()->tokens()->first())['abilities'];
			$delimiters = ['.', ':'];
			if (in_array("*", $abilities)) {
				return true;
			}
			foreach($delimiters as $delimiter){
				if(strpos($permission, $delimiter)){
					if (explode($delimiter, $permission)[1] == "*") {
						foreach($abilities as $ability){
							if (explode($delimiter, $ability)[0] ==  explode($delimiter, $permission)[0]){
								return true;
							}
						}
					}
				}
			}
			if (in_array($permission, $abilities)) {
				return true;
			}
		}
		return false;
	}
	public static function change($permissionName, $toValue, $id){
		$permission = Permission::where("permission_name", $permissionName)->first();
		$user = User::where("id", $id);
		$user_key = Crypt::decryptString($user->user_key);
		$permissions = explode('|', $user_key)[1];
		$permissions[$permission->id] = $toValue;
		$user->user_key = $this->genKey($permissions, explode("|", $user_key)[0]);
		$user->save();
		return true;
	}

}
