<?php

namespace Tests\Feature;

use App\Models\CttPlayer;
use Tests\TestCase;

class CttPasswordCompatibilityTest extends TestCase
{
    public function test_it_decrypts_laravel_encrypt_string_payloads(): void
    {
        $password = 'MyAfttSecret!2026';
        $player = new CttPlayer([
            'password' => app('encrypter')->encryptString($password),
        ]);

        $this->assertSame($password, $player->decrypted_password);
    }
}
