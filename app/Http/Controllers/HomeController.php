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
                'thumbnail_url' => "https://static-cdn.jtvnw.net/previews-ttv/live_user_{$streamData['user_name']}-320x180.jpg",
                'url' => "https://twitch.tv/" . strtolower($streamData['user_name'])
            ];
        }

        return $liveStreams;
    }
}