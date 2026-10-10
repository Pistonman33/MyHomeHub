<?php

namespace Tests\Feature;

use App\Models\CttPlayer;
use Illuminate\Encryption\Encrypter;
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

    public function test_it_does_not_return_ciphertext_when_app_key_cannot_decrypt_password(): void
    {
        $otherKeyEncrypter = new Encrypter(random_bytes(32), config('app.cipher'));
        $ciphertext = $otherKeyEncrypter->encryptString('AFTT password');
        $player = new CttPlayer(['password' => $ciphertext]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('cannot be decrypted with the current APP_KEY');

        $player->decrypted_password;
    }
}
