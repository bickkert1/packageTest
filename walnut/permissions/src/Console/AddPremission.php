<?php
namespace Walnut\Permissions\Console;

use Illuminate\Console\Command;
use Walnut\Permissions\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class AddPremission extends Command {
	protected $signature = "permission:add";

	protected $description = "Add a new permission";

	public function handle() {
		$permission_name = $this->ask("State the name of the permission");

		$permission = new Permission;
		$permission->permission_name = $permission_name;
		$permission->save();

		$users = User::all();
		foreach($users as $user){
			$user_key = Crypt::decryptString($user->user_key);
			$user->user_key = Crypt::encryptString($user_key . "1");
			$user->save();
		}

		$this->info("succes");
	}
}