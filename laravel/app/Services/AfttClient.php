<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AfttClient
{
    protected ?int $license = null;
    protected ?string $password = null;

    public function setCredentials(int $license, string $password): void
    {
        $this->license = $license;
        $this->password = $password;
    }

    public function login(): array
    {
        if (! $this->license || ! $this->password) {
            throw new \RuntimeException('AFTT credentials are not set.');
        }

        $baseUrl = rtrim((string) env('AFTT_BASE_URL', 'https://data.aftt.be'), '/');
        $loginUrl = $baseUrl . '/tools/index.php';
        $host = (string) parse_url($loginUrl, PHP_URL_HOST);

        $initialResponse = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->timeout(30)->get($loginUrl);

        $cookies = [];
        foreach ($initialResponse->cookies()->toArray() as $cookie) {
            $cookies[$cookie['Name']] = $cookie['Value'];
        }

        $initialBody = (string) $initialResponse->body();
        if (! preg_match('/name="csrf_token"[^>]*value="([^"]+)"/i', $initialBody, $matches)) {
            throw new \RuntimeException('Unable to authenticate to AFTT: CSRF token was not found on the login page.');
        }

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->withCookies($cookies, $host)
            ->timeout(30)
            ->asForm()
            ->post($loginUrl, [
                'csrf_token' => $matches[1],
                'licence' => (string) $this->license,
                'password' => $this->password,
                'sidebar_login' => '1',
            ]);

        foreach ($response->cookies()->toArray() as $cookie) {
            $cookies[$cookie['Name']] = $cookie['Value'];
        }

        if (! $this->isSuccessfulLoginResponse((string) $response->body())) {
            throw new \RuntimeException('Unable to authenticate to AFTT. The data portal did not confirm the login; check the stored AFTT license and password.');
        }

        return ['cookies' => $cookies];
    }

    public function getDashboard(array $cookies, ?int $license = null): string
    {
        $baseUrl = rtrim((string) env('AFTT_BASE_URL', 'https://data.aftt.be'), '/');
        $url = $baseUrl . '/tools/fiche.php?licenceID=' . ($license ?? $this->license);

        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])
            ->withCookies($cookies, (string) parse_url($url, PHP_URL_HOST))
            ->timeout(30)
            ->get($url)
            ->body();
    }

    protected function isSuccessfulLoginResponse(string $body): bool
    {
        $normalized = strtolower($body);

        return (str_contains($normalized, 'logout.php') || str_contains($normalized, 'déconnexion'))
            && ! str_contains($normalized, 'name="sidebar_login"');
    }
}