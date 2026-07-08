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

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->asForm()->post(env('AFTT_BASE_URL') . '/index.php', [
            'licence' => $this->license,
            'password' => $this->password,
        ]);

        // 🔥 conversion CookieJar -> array compatible withCookies()
        $cookies = [];

        foreach ($response->cookies()->toArray() as $cookie) {
            $cookies[$cookie['Name']] = $cookie['Value'];
        }

        return [
            'cookies' => $cookies,
        ];
    }

    public function getDashboard(array $cookies): string
    {
        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])
            ->withCookies($cookies, parse_url(env('AFTT_BASE_URL'), PHP_URL_HOST))
            ->get(env('AFTT_BASE_URL') . '/tools/index_registered.php')
            ->body();
    }
}