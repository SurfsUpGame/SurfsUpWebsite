<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SteamOpenID
{
    const STEAM_OPENID_URL = 'https://steamcommunity.com/openid/login';
    const STEAM_API_URL = 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/';

    private string $returnUrl;
    private ?string $apiKey;

    public function __construct()
    {
        $this->returnUrl = route('auth.steam.callback');
        $this->apiKey = config('steam-auth.api_keys')[0] ?? null;
    }

    public function getAuthUrl(): string
    {
        $params = [
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $this->returnUrl,
            'openid.realm' => url('/'),
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        return self::STEAM_OPENID_URL . '?' . http_build_query($params);
    }

    public function validate(array $request): ?string
    {
        Log::info('SteamOpenID validate called', ['request' => $request]);

        if (!isset($request['openid_mode']) || $request['openid_mode'] !== 'id_res') {
            Log::warning('Invalid openid_mode', ['mode' => $request['openid_mode'] ?? 'not set']);
            return null;
        }

        $params = [
            'openid.assoc_handle' => $request['openid_assoc_handle'],
            'openid.signed' => $request['openid_signed'],
            'openid.sig' => $request['openid_sig'],
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
        ];

        $signed = explode(',', $request['openid_signed']);
        foreach ($signed as $item) {
            $val = $request['openid_' . str_replace('.', '_', $item)];
            $params['openid.' . $item] = $val;
        }

        $params['openid.mode'] = 'check_authentication';

        Log::info('Sending validation request to Steam', ['params' => $params]);

        $response = Http::asForm()->post(self::STEAM_OPENID_URL, $params);

        Log::info('Steam validation response', ['body' => $response->body()]);

        if (!Str::contains($response->body(), 'is_valid:true')) {
            Log::warning('Steam validation failed');
            return null;
        }

        preg_match('/https:\/\/steamcommunity\.com\/openid\/id\/(\d+)/', $request['openid_claimed_id'], $matches);

        $steamId = $matches[1] ?? null;
        Log::info('Extracted Steam ID', ['steam_id' => $steamId]);

        return $steamId;
    }

    public function getUserInfo(string $steamId): ?array
    {
        if (!$this->apiKey) {
            return [
                'steamid' => $steamId,
                'personaname' => 'Steam User',
                'avatarfull' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg',
            ];
        }

        $response = Http::get(self::STEAM_API_URL, [
            'key' => $this->apiKey,
            'steamids' => $steamId,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['response']['players'][0] ?? null;
        } else {
            Log::warning('Failed to get user info from Steam API', ['response' => $response->body()]);
        }

        return null;
    }
}
