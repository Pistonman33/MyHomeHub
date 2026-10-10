<?php

namespace App\Console\Commands;

use App\Models\CttPlayer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class UpdateAfttPassword extends Command
{
    protected $signature = 'ctt:aftt-password {license : The CTT player license}';

    protected $description = 'Securely replace a player AFTT password using the current APP_KEY';

    public function handle(): int
    {
        if (! app()->runningInConsole() || ! $this->input->isInteractive()) {
            $this->error('This command requires an interactive terminal so the password can be entered without echoing it.');

            return self::FAILURE;
        }

        $player = CttPlayer::find((int) $this->argument('license'));
        if (! $player) {
            $this->error('No CTT player found for that license.');

            return self::FAILURE;
        }

        $password = $this->secret('Enter the new AFTT password');
        if ($password === null || trim($password) === '') {
            $this->error('The password cannot be empty. No changes were saved.');

            return self::FAILURE;
        }

        $confirmation = $this->secret('Confirm the new AFTT password');
        if ($confirmation === null || ! hash_equals($password, $confirmation)) {
            $this->error('The passwords did not match. No changes were saved.');

            return self::FAILURE;
        }

        $player->password = Crypt::encryptString($password);
        $player->save();

        $decrypted = Crypt::decryptString($player->getRawOriginal('password'));
        if (! hash_equals($password, $decrypted)) {
            $this->error('The saved password did not pass its encryption check.');

            return self::FAILURE;
        }

        $this->info("AFTT password securely updated for license {$player->license}.");

        return self::SUCCESS;
    }
}
