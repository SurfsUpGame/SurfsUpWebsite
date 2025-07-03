<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\SteamOpenID;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;

final class SteamAuthController
{
    private SteamOpenID $steam;
    private AuthManager $authManager;
    private Redirector $redirector;

    public function __construct(
        SteamOpenID $steam,
        AuthManager $authManager,
        Redirector $redirector
    ) {
        $this->steam = $steam;
        $this->authManager = $authManager;
        $this->redirector = $redirector;
    }

    public function login(): RedirectResponse
    {
        return $this->redirector->to($this->steam->getAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        // Log::info('Steam callback received', $request->all());

        $steamId = $this->steam->validate($request->all());

        // Log::info('Steam ID from validation', ['steam_id' => $steamId]);

        if (!$steamId) {
            return $this->redirector->route('filament.admin.auth.login')
                ->with('error', 'Steam authentication failed.');
        }

        $steamUser = $this->steam->getUserInfo($steamId);

        // Log::info('Steam user info', ['steam_user' => $steamUser]);

        if (!$steamUser) {
            return $this->redirector->route('filament.admin.auth.login')
                ->with('error', 'Could not retrieve Steam user information.');
        }

        $user = $this->firstOrCreate($steamId, $steamUser);

        // Log::info('User created/found', ['user' => $user->toArray()]);

        $this->authManager->login($user, true);

        return $this->redirector->intended(route('home', absolute: false).'?verified=1');
    }

    private function firstOrCreate(string $steamId, array $steamUser): User
    {
        try {
            $user = User::firstOrCreate([
                'steam_id' => $steamId,
            ], [
                'name' => $steamUser['personaname'],
                'avatar' => $steamUser['avatarfull'],
                'email' => $steamId . '@steamauth.local',
                'password' => bcrypt(str()->random(32)),
            ]);

            // Update avatar, name, and last login time on every login
            $user->update([
                'avatar' => $steamUser['avatarfull'],
                'name' => $steamUser['personaname'],
                'last_login_at' => now(),
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to create/find user', [
                'error' => $e->getMessage(),
                'steam_id' => $steamId,
                'steam_user' => $steamUser
            ]);
            throw $e;
        }
    }
}
