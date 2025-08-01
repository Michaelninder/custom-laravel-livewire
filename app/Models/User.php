<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'username',
        'displayname',
        'handle',
        'name_first',
        'name_last',
        'email',
        'avatar_url',
        'role',
        'bio',
        'location',
        'website',
        'password',
        'discord_id',
        'discord_username',
        'discord_avatar',
        'github_id',
        'github_username',
        'github_avatar',
        'twitch_id',
        'twitch_username',
        'twitch_avatar',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }

    public function initials(): string
    {
        $initials = '';

        if (!empty($this->name_first)) {
            $initials .= Str::upper(Str::substr($this->name_first, 0, 1));
        }

        if (!empty($this->name_last)) {
            $initials .= Str::upper(Str::substr($this->name_last, 0, 1));
        }

        if (empty($initials) && !empty($this->username)) {
            $initials = Str::upper(Str::substr($this->username, 0, 1));
        } elseif (empty($initials) && !empty($this->email)) {
             $initials = Str::upper(Str::substr($this->email, 0, 1));
        }

        return $initials;
    }

    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        if ($this->discord_avatar) {
            return $this->discord_avatar;
        }

        if ($this->github_avatar) {
            return $this->github_avatar;
        }

        if ($this->twitch_avatar) {
            return $this->twitch_avatar;
        }

        $defaultAvatarUrl = env('DEFAULT_USER_AVATAR_URL');
        if ($defaultAvatarUrl) {
            if (!Str::startsWith($defaultAvatarUrl, ['http://', 'https://', '//'])) {
                return URL::to(asset($defaultAvatarUrl));
            }
            return $defaultAvatarUrl;
        }

        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?d=mp';
    }


    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
    }

    public function getDisplayNameAttribute(): string
    {
        $fullName = trim($this->name_first . ' ' . $this->name_last);
        if (!empty($fullName)) {
            return $fullName;
        }

        if (!empty($this->username)) {
            return $this->username;
        }

        return (string) $this->email;
    }

    public function getProfileLinkAttribute(): string
    {
        return $this->handle ? route('profile.handle', ['handle' => $this->handle]) : route('profile.id', ['user' => $this->id]);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges', 'user_id', 'badge_id')
                    ->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function hasFollower(User $user): bool
    {
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}