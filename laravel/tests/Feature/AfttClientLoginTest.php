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
                    '<html><body>Redirecting to the account</body></html>',
                    200
                );
            },
            'https://data.aftt.be/tools/fiche.php?licenceID=167814' => Http::response(
                '<section class="player-profile-hero"><span>167814</span><span>Points actuels</span><span>Points de base</span></section>',
                200
            ),
        ]);

        $client = new AfttClient();
        $client->setCredentials(167814, 'secret-pass');

        $session = $client->login();

        $this->assertArrayHasKey('cookies', $session);
        $this->assertStringContainsString('Points actuels', $session['dashboard']);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://data.aftt.be/tools/index.php'
                && $request->method() === 'POST'
                && $request['csrf_token'] === 'abc123'
                && $request['licence'] === '167814'
                && $request['password'] === 'secret-pass'
                && $request['sidebar_login'] === '1';
        });
    }

    public function test_login_fails_if_the_session_still_returns_the_login_page(): void
    {
        Http::fake([
            'https://data.aftt.be/tools/index.php' => function ($request) {
                return $request->method() === 'GET'
                    ? Http::response('<input name="csrf_token" value="abc123">')
                    : Http::response('<div class="login-modern-alert">Licence ou mot de passe incorrect</div>');
            },
            'https://data.aftt.be/tools/fiche.php?licenceID=167814' => Http::response(
                '<form><input name="licence" placeholder="Licence ou login"><input name="sidebar_login"></form>',
                200
            ),
        ]);

        $client = new AfttClient();
        $client->setCredentials(167814, 'secret-pass');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('the portal returned its login page');
    $this->expectExceptionMessage('Licence ou mot de passe incorrect');

        $client->login();
    }
}
