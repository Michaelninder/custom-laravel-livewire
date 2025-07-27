<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class SocialiteController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }

        $user = null;
        if ($provider === 'discord') {
            $user = User::where('discord_id', $socialUser->id)->first();
        } elseif ($provider === 'github') {
            $user = User::where('github_id', $socialUser->id)->first();
        } elseif ($provider === 'twitch') {
            $user = User::where('twitch_id', $socialUser->id)->first();
        }

        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
        }

        if ($user) {
            if ($provider === 'discord' && !$user->discord_id) {
                $user->discord_id = $socialUser->id;
                $user->discord_username = $socialUser->nickname ?? $socialUser->name;
                $user->discord_avatar = $socialUser->avatar;
            } elseif ($provider === 'github' && !$user->github_id) {
                $user->github_id = $socialUser->id;
                $user->github_username = $socialUser->nickname ?? $socialUser->name;
                $user->github_avatar = $socialUser->avatar;
            } elseif ($provider === 'twitch' && !$user->twitch_id) {
                $user->twitch_id = $socialUser->id;
                $user->twitch_username = $socialUser->nickname ?? $socialUser->name;
                $user->twitch_avatar = $socialUser->avatar;
            }

            if (!$user->avatar_url) {
                $user->avatar_url = $socialUser->avatar;
            }
            $user->email_verified_at = $user->email_verified_at ?? now();
            $user->save();
            Auth::login($user);
            return redirect(route('dashboard')); 
        } else {
            
            $newUser = User::create([
                'username' => $socialUser->nickname ?? $socialUser->name ?? Str::before($socialUser->email, '@') ?? Str::uuid(),
                'email' => $socialUser->getEmail(),
                'email_verified_at' => now(),
                'avatar_url' => $socialUser->avatar,
                
                'discord_id' => $provider === 'discord' ? $socialUser->id : null,
                'discord_username' => $provider === 'discord' ? ($socialUser->nickname ?? $socialUser->name) : null,
                'discord_avatar' => $provider === 'discord' ? $socialUser->avatar : null,
                'github_id' => $provider === 'github' ? $socialUser->id : null,
                'github_username' => $provider === 'github' ? ($socialUser->nickname ?? $socialUser->name) : null,
                'github_avatar' => $provider === 'github' ? $socialUser->avatar : null,
                'twitch_id' => $provider === 'twitch' ? $socialUser->id : null,
                'twitch_username' => $provider === 'twitch' ? ($socialUser->nickname ?? $socialUser->name) : null,
                'twitch_avatar' => $provider === 'twitch' ? $socialUser->avatar : null,
            ]);

            Auth::login($newUser);
            return redirect()->intended('/dashboard');
        }
    }
}