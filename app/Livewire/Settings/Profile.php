<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public User $user;
    public string $username = '';
    public ?string $name_first = null;
    public ?string $name_last = null;
    public string $email = '';
    public ?string $bio = null;
    public ?string $location = null;
    public ?string $website = null;
    public ?string $handle = null;

    public $newAvatar = null;

    public function mount(): void
    {
        $this->user = Auth::user();

        $this->username = $this->user->username;
        $this->name_first = $this->user->name_first;
        $this->name_last = $this->user->name_last;
        $this->email = $this->user->email;
        $this->bio = $this->user->bio;
        $this->location = $this->user->location;
        $this->website = $this->user->website;
        $this->handle = $this->user->handle;
    }

    public function updateProfileInformation()
    {
        $validated = $this->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
            'name_first' => ['nullable', 'string', 'max:255'],
            'name_last' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', 'url'],
            'handle' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique(User::class)->ignore($this->user->id)],
            'newAvatar' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        if (isset($validated['handle']) && $validated['handle'] === '') {
            $validated['handle'] = null;
        }

        if ($this->newAvatar) {
            $path = $this->newAvatar->storePublicly('avatars', 'public');
            $this->user->avatar_url = \Storage::url($path);
        }

        $this->user->fill($validated);

        if ($this->user->isDirty('email')) {
            $this->user->email_verified_at = null;
        }

        $this->user->save();

        $this->newAvatar = null;

        $this->dispatch('profile-updated',
            username: $this->user->username,
            name_first: $this->user->name_first,
            name_last: $this->user->name_last,
            email: $this->user->email,
            bio: $this->user->bio,
            location: $this->user->location,
            website: $this->user->website,
            handle: $this->user->handle,
            avatar_url: $this->user->avatar_url,
        );

        return redirect()->to(route('settings.profile'));
    }

    public function removeNewAvatar(): void
    {
        $this->newAvatar = null;
    }

    public function resendVerificationNotification(): void
    {
        if ($this->user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $this->user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}