<?php

namespace Walnut\Permissions;

use Illuminate\Support\Facades\Auth;
use Walnut\Permissions\Models\UserHistory;
use Illuminate\Http\Request;

class UserHistorys {

	public static function addHistory($path = null){
		if (is_null($path)) {
			$path = Request()->path();
		}
		if (is_null(Auth::user()->id)) {
			return;
		}
		UserHistory::create(['user_key' => Auth::user()->id, 'link' => $path]);
		return;
	}

}