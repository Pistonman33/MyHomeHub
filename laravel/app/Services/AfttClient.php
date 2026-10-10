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

        $baseUrl = $this->baseUrl();
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
        if (! preg_match('/<input\b(?=[^>]*\bname=["\']csrf_token["\'])(?=[^>]*\bvalue=["\']([^"\']+)["\'])[^>]*>/i', $initialBody, $matches)) {
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

        $dashboardResponse = $this->requestDashboard($cookies, $this->license);
        $dashboard = (string) $dashboardResponse->body();

        if (! $this->isAuthenticatedPlayerPage($dashboard, $this->license)) {
            $reason = $this->isLoginPage($dashboard)
                ? 'the portal returned its login page'
                : 'the authenticated player fiche was not returned';

            throw new \RuntimeException(sprintf(
                'Unable to authenticate to AFTT for license %d: %s (HTTP %d). Check the production AFTT URL, session handling, and stored credentials.',
                $this->license,
                $reason,
                $dashboardResponse->status()
            ));
        }

        return ['cookies' => $cookies, 'dashboard' => $dashboard];
    }

    public function getDashboard(array $cookies, ?int $license = null): string
    {
        $response = $this->requestDashboard($cookies, $license ?? $this->license);

        return (string) $response->body();
    }

    private function requestDashboard(array $cookies, ?int $license): \Illuminate\Http\Client\Response
    {
        $url = $this->baseUrl() . '/tools/fiche.php?licenceID=' . $license;

        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])
            ->withCookies($cookies, (string) parse_url($url, PHP_URL_HOST))
            ->timeout(30)
            ->get($url);
    }

    private function baseUrl(): string
    {
        $url = trim((string) config('services.aftt.base_url', 'https://data.aftt.be'));

        return rtrim($url, '/');
    }

    private function isAuthenticatedPlayerPage(string $body, int $license): bool
    {
        if ($this->isLoginPage($body)) {
            return false;
        }

        $hasPlayerLicense = str_contains($body, (string) $license);

        return $hasPlayerLicense
            && str_contains(mb_strtolower($body), 'player-profile-hero')
            && str_contains(mb_strtolower($body), 'points actuels')
            && str_contains(mb_strtolower($body), 'points de base');
    }

    private function isLoginPage(string $body): bool
    {
        $normalized = mb_strtolower($body);

        return str_contains($normalized, 'name="sidebar_login"')
            || str_contains($normalized, "name='sidebar_login'");
    }
}