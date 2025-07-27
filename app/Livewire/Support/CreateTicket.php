<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SupportLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends Component
{
    public string $subject = '';
    public string $first_message = '';
    public string $priority = 'medium';

    protected array $rules = [
        'subject' => 'required|string|max:255',
        'first_message' => 'required|string|max:2000',
        'priority' => 'required|string|in:low,medium,high,urgent',
    ];

    public function createTicket(): void
    {
        $validated = $this->validate();

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_replied_at' => now(),
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['first_message'],
        ]);

        SupportLog::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'type' => 'ticket_created',
        ]);

        session()->flash('success', __('Your support ticket has been created!'));

        $this->redirect(route('support.tickets.show', $ticket), navigate: true);
    }

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('Support'), 'url' => route('support.tickets.index')],
            ['label' => __('Tickets'), 'url' => route('support.tickets.index')],
            ['label' => __('Create New Ticket')],
        ];

        return view('livewire.support.create-ticket', ['breadcrumbs' => $breadcrumbs]);
    }
}