<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $username = '';
    public ?string $name_first = null;
    public ?string $name_last = null;
    public string $email = '';

    public ?string $bio = null;
    public ?string $location = null;
    public ?string $website = null;
    public ?string $handle = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->username = $user->username;
        $this->name_first = $user->name_first;
        $this->name_last = $user->name_last;
        $this->email = $user->email;
        $this->bio = $user->bio;
        $this->location = $user->location;
        $this->website = $user->website;
        $this->handle = $user->handle;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'name_first' => ['nullable', 'string', 'max:255'],
            'name_last' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', 'url'],
            'handle' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique(User::class)->ignore($user->id)],
        ]);

        if (isset($validated['handle']) && $validated['handle'] === '') {
            $validated['handle'] = null;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated',
            username: $user->username,
            name_first: $user->name_first,
            name_last: $user->name_last,
            email: $user->email,
            bio: $user->bio,
            location: $user->location,
            website: $user->website,
            handle: $user->handle,
        );
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}