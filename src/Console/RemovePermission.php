<?php
namespace Walnut\Permissions\Console;

use Illuminate\Console\Command;
use Walnut\Permissions\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class RemovePermission extends Command {
	protected $signature = "permission:remove";

	protected $description = "Remove a permission";

	public function handle() {
		$permission_name = $this->ask("State the name of the permission");

		$permission = Permission::where("permission_name" = $permission_name)->first();
		$permission->delete();

		$this->info("succes");
	}
}