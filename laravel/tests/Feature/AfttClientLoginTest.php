<?php

namespace Tests\Feature;

use App\Services\AfttClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AfttClientLoginTest extends TestCase
{
    public function test_login_uses_session_and_csrf_token_from_aftt_login_page(): void
    {
        Http::fake([
            'https://data.aftt.be/tools/index.php' => function ($request) {
                if ($request->method() === 'GET') {
                    return Http::response(
                        '<form><input type="hidden" name="csrf_token" value="abc123"></form>',
                        200
                    );
                }

                $this->assertSame('abc123', $request['csrf_token']);
                $this->assertSame('167814', $request['licence']);
                $this->assertSame('secret-pass', $request['password']);
                $this->assertSame('1', $request['sidebar_login']);

                return Http::response(
                    '<html><body><a href="../logout.php">logout</a></body></html>',
                    200,
                    [
                        'Set-Cookie' => 'PHPSESSID=loggedin-session; path=/; secure; HttpOnly',
                    ]
                );
            },
        ]);

        $client = new AfttClient();
        $client->setCredentials(167814, 'secret-pass');

        $session = $client->login();

        $this->assertArrayHasKey('cookies', $session);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://data.aftt.be/tools/index.php'
                && $request->method() === 'POST'
                && $request['csrf_token'] === 'abc123'
                && $request['licence'] === '167814'
                && $request['password'] === 'secret-pass'
                && $request['sidebar_login'] === '1';
        });
    }
}
