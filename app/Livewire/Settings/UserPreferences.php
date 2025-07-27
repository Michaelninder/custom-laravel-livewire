<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserPreferences extends Component
{
    public $settings = [];

    public string $theme = 'system';
    public bool $email_notifications_enabled = true;
    public bool $newsletter_opt_in = false;
    public string $preferred_language = 'en';

    public function mount(): void
    {
        $user = Auth::user();

        $this->settings = $user->settings ?? [];

        $this->theme = $this->settings['theme'] ?? 'system';
        $this->email_notifications_enabled = $this->settings['email_notifications_enabled'] ?? true;
        $this->newsletter_opt_in = $this->settings['newsletter_opt_in'] ?? false;
        $this->preferred_language = $this->settings['preferred_language'] ?? 'en';
    }

    public function updatePreferences(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
            'email_notifications_enabled' => ['required', 'boolean'],
            'newsletter_opt_in' => ['required', 'boolean'],
            'preferred_language' => ['required', 'string', 'in:en,de,fr'],
        ]);

        $user->settings = array_merge($user->settings ?? [], [
            'theme' => $validated['theme'],
            'email_notifications_enabled' => $validated['email_notifications_enabled'],
            'newsletter_opt_in' => $validated['newsletter_opt_in'],
            'preferred_language' => $validated['preferred_language'],
        ]);

        $user->save();

        $this->dispatch('preferences-updated');
    }

    public function render()
    {
        return view('livewire.settings.user-preferences');
    }
}