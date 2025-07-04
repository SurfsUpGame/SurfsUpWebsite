<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SteamNewsController extends Controller
{
    const SURFSUP_APP_ID = '3454830';
    const CACHE_KEY = 'steam_news';
    const CACHE_DURATION = 300; // 5 minutes

    public function index()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            try {
                $response = Http::get('https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/', [
                    'appid' => self::SURFSUP_APP_ID,
                    'count' => 10,
                    'maxlength' => 500,
                ]);

                if ($response->successful()) {
                    return response()->json($response->json());
                }

                return response()->json(['error' => 'Failed to fetch Steam news'], 500);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to fetch Steam news'], 500);
            }
        });
    }
}