<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserProfileFollowersList extends Component
{
    public string $userId;
    public ?User $profileUser = null;
    public bool $showModal = false;

    protected $listeners = ['openFollowersModal'];

    public function mount(string $userId): void
    {
        $this->userId = $userId;
        $this->profileUser = User::find($userId);

        if (!$this->profileUser) {
            $this->showModal = false;
        }
    }

    public function openFollowersModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $followers = collect();
        if ($this->profileUser) {
            $followers = $this->profileUser->followers()
                ->where(function ($query) {
                    $query->whereJsonContains('settings->follower_visibility', true)
                          ->orWhereNull('settings->follower_visibility');
                })
                ->get();
        }

        return view('livewire.user-profile-followers-list', [
            'followers' => $followers,
        ]);
    }
}