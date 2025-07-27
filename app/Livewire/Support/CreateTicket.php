<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends Component
{
    public string $subject = '';
    public string $description = '';
    public string $priority = 'medium';

    protected array $rules = [
        'subject' => 'required|string|max:255',
        'description' => 'nullable|string|max:2000',
        'priority' => 'required|string|in:low,medium,high,urgent',
    ];

    public function createTicket(): void
    {
        $validated = $this->validate();

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_replied_at' => now(),
        ]);

        session()->flash('success', __('Your support ticket has been created!'));

        $this->redirect(route('support.tickets.show', $ticket), navigate: true);
    }

    public function render()
    {
        return view('livewire.support.create-ticket');
    }
}