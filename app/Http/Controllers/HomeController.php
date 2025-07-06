<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $liveStreams = $this->getLiveStreams();

        return view('home', [
            'liveStreams' => $liveStreams
        ]);
    }

    private function getLiveStreams(): array
    {
        $cachedStreams = Cache::get('twitch_posted_streams', []);
        $liveStreams = [];

        foreach ($cachedStreams as $streamData) {
            $liveStreams[] = [
                'id' => $streamData['id'],
                'user_name' => $streamData['user_name'],
                'title' => $streamData['title'],
                'posted_at' => $streamData['posted_at'],
                'thumbnail_url' => str_replace(['{width}', '{height}'], ['320', '180'], $streamData['thumbnail_url']),
                'user_login' => $streamData['user_login'],
                'url' => "https://twitch.tv/" . strtolower($streamData['user_login'] ?? $streamData['user_name'])
            ];
        }

        return $liveStreams;
    }
}
