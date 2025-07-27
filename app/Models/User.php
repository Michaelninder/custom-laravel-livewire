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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'username',
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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'settings' => 'array',
    ];

    public function getAvatarAttribute()
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
        return $this->name_first . ' ' . $this->name_last;
    }

    public function getProfileLinkAttribute(): string
    {
        return $this->handle ? route('profile', ['handle' => $this->handle]) : route('profile', ['id' => $this->id]);
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
}