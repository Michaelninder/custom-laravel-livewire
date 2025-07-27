<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserProfile extends Component
{
    public ?User $user = null;
    public ?string $handle = null;

    public function mount($handle = null, User $user = null)
    {
        if ($handle) {
            $this->user = User::where('handle', $handle)->firstOrFail();
        } elseif ($user) {
            $this->user = $user;
        } else {
            abort(404);
        }
    }

    public function render()
    {
        if (!$this->user) {
            abort(404);
        }
        return view('livewire.user-profile');
    }
}