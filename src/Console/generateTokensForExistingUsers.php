<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateTokensForExistingUsers extends command{
	 /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access:tokens {action} {--user=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'do an action';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        switch ($this->argument('action')) {
        	case 'fill':
        		$this->changeKey();
        		break;
          case 'remove':
            $this->removeKeys();
            break;
          case 'SU':
            $this->addPermission("SuperUser", $this->option("user"));
            break;
          case "help":
            $this->help();
            break;
        	default:
        		$this->info('no valid action selected, try updating.');
        		break;
        }
    }

    private function help(){
      $this->line("-------------------------------------------------------------");
      $this->line("                                                             ");
      $this->line("-----------------   ACCES:TOKEN   ---------------------------");
      $this->line("-------------------------------------------------------------");
      $this->line(" ");
      $this->line("Usage: access:token [command] [--user]");
      $this->line("");
      $this->line("Commands: ");
      $this->line("fill:    create a new token for all users with basic permissions");
      $this->line("remove:  removes all keys that are assigned to a user");
      $this->line("SU:      give the SuperUser permission to the geven user");
      $this->line("help:    show this menu");
      $this->line("");
      $this->line("-------------------------------------------------------------");
      $this->line("");
      $this->line("Options: ");
      $this->line("--user=[user_id]: give the user id. used with SU");
      $this->line("");
      $this->line("-------------------------------------------------------------");

    }
    private function addPermission($perm, $user){
      $this->info("Gathering user and abilities.");
      $user = User::where('id', $user)->first();
      $abilities = $user->tokens()->where('name', 'dashboard')->first()->abilities;
      array_push($abilities, $perm);
      $this->info("Your new permission set: " . json_encode($abilities));
      if (count($abilities) > 1) {
        $this->info("Removing existing key.");
        $user->tokens()->where('name', 'dashboard')->first()->delete();
        $user->createToken('dashboard', $abilities);
        $this->info("Created a new token.");
      }
      else{
        $this->info("Something went wrong, try again.");
      }
    }
    private function removeKeys(){
      $this->info("gathering all users");
      $users = User::all();
      $this->info("Finished gathering users, removing keys");
      foreach($users as $user){
        $user->tokens()->delete();
      }
      $this->info("Finished revoking tokens");
    }

    private function changeKey(){
    	$this->info("Gathering all users");
    	$users = \App\Models\Auth\Password::whereNotNull('organization_id')->whereNotNull('user_id')->pluck('user_id')->toArray();
      $users = \App\Models\User::whereIn('id', $users)->get();
    	$this->info("Finished gathering users, adding keys");
    	foreach($users as $user){
    		$role_id = json_decode($user->roles);
    		if (is_null($role_id) || ! isset($role_id[0])) {
    			continue;
    		}
        if(json_encode($user->tokens()->get()) == '[]'){
          $this->info("Creating a key for: ". $user->id);
          $user->createToken("dashboard", ["dashboard.get","scan.get","members.addMembers","members.addPass","members.segmentation","marketing.leads","marketing.campaigns","marketing.loyalty","marketing.games","marketing.mailTemplates","products.products","products.coupons","products.storecards","products.onlineOffer","products.marketplace","webshops.overview","webshops.categories","organization.organization","organization.brands","organization.locations","advanced.integrations",
          "advanced.accesTokens","advanced.migrationstest","request.apiToken"]);
        }
        else {
          continue;
        }

    		//$user->createToken("dashboard", $permissions)->plainTextToken;
    	}
      $this->info('Created a new token with basic permissions for users that didn\'t have a token');
    }
}
