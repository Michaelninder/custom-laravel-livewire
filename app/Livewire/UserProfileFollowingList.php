<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserProfileFollowingList extends Component
{
    public string $userId;
    public ?User $profileUser = null;
    public bool $showModal = false;

    protected $listeners = ['openFollowingModal'];

    public function mount(string $userId): void
    {
        $this->userId = $userId;
        $this->profileUser = User::find($userId);

        if (!$this->profileUser) {
            $this->showModal = false;
        }
    }

    public function openFollowingModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $following = collect();
        if ($this->profileUser) {
            $following = $this->profileUser->following()
                ->where(function ($query) {
                    $query->whereJsonContains('settings->following_visibility', true)
                          ->orWhereNull('settings->following_visibility');
                })
                ->get();
        }

        return view('livewire.user-profile-following-list', [
            'following' => $following,
        ]);
    }
}