<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\User;
use Illuminate\Console\Command;

class UserAdminCommand extends Command
{
    protected $signature = 'user:admin
                            {login : The telegram_login of the user}';

    protected $description = 'Set admin user group';

    public function handle(): void
    {
        $login = $this->argument('login');
        $user = User::whereTelegramLogin($login)->first();
        if (! $user) {
            $this->error('No query results for telegram_login: '.$login);

            return;
        }

        if ($user->group_id === Group::GROUP_ADMIN) {
            $this->error('Login: '.$login.' already admin');

            return;
        }

        $user->update(['group_id' => Group::GROUP_ADMIN]);
        $this->info('Successful! Login: '.$login.' set admin group');

        // todo fire telegram notification
    }
}
