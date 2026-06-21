<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AfttClient
{
    public function login(): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->asForm()->post(env('AFTT_BASE_URL') . '/index.php', [
            'licence' => env('AFTT_LICENSE'),
            'password' => env('AFTT_PASSWORD'),
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