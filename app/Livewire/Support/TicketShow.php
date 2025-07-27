<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TicketShow extends Component
{
    public SupportTicket $ticket;
    public string $messageContent = '';

    public ?string $selectedStatus = null;
    public ?string $selectedPriority = null;
    public ?string $selectedAgentId = null;

    protected array $rules = [
        'messageContent' => 'required|string|max:2000',
    ];

    public function mount(SupportTicket $ticket): void
    {
        if (!Auth::user()->isAdmin() && $ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $this->ticket = $ticket;
        $this->selectedStatus = $ticket->status;
        $this->selectedPriority = $ticket->priority;
        $this->selectedAgentId = $ticket->assigned_to;
    }

    public function sendMessage(): void
    {
        $this->validateOnly('messageContent');

        SupportMessage::create([
            'support_ticket_id' => $this->ticket->id,
            'user_id' => Auth::id(),
            'message' => $this->messageContent,
        ]);

        $this->ticket->update(['last_replied_at' => now(), 'status' => 'open']);

        $this->messageContent = '';
        $this->dispatch('messageSent');
    }

    public function updateTicketSettings(): void
    {
        if (!Auth::user()->isAdmin()) {
            return;
        }

        $this->validate([
            'selectedStatus' => ['required', Rule::in(['open', 'pending', 'closed', 'resolved'])],
            'selectedPriority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'selectedAgentId' => ['nullable', Rule::exists('users', 'id')],
        ]);

        $this->ticket->update([
            'status' => $this->selectedStatus,
            'priority' => $this->selectedPriority,
            'assigned_to' => $this->selectedAgentId,
        ]);

        session()->flash('settings_updated', __('Ticket settings updated successfully.'));
    }

    public function getListeners()
    {
        return [
            'messageSent' => '$refresh',
        ];
    }

    public function render()
    {
        $this->ticket->load(['messages.user', 'user', 'assignedAgent']);

        $supportAgents = [];
        if (Auth::user()->isAdmin()) {
            $supportAgents = User::where('role', 'admin')->get(['id', 'username']);
        }


        return view('livewire.support.ticket-show', [
            'supportAgents' => $supportAgents,
        ]);
    }
}