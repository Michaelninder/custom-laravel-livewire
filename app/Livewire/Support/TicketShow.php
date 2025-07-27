<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
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

        $validated = $this->validate([
            'selectedStatus' => ['required', Rule::in(['open', 'pending', 'closed', 'resolved'])],
            'selectedPriority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'selectedAgentId' => ['nullable', Rule::exists('users', 'id')],
        ]);

        $oldStatus = $this->ticket->status;
        $oldPriority = $this->ticket->priority;
        $oldAssignedTo = $this->ticket->assigned_to;

        $this->ticket->update([
            'status' => $this->selectedStatus,
            'priority' => $this->selectedPriority,
            'assigned_to' => $this->selectedAgentId,
        ]);

        $logMessage = Auth::user()->display_name;

        $updates = [];
        if ($oldStatus !== $this->selectedStatus) {
            $updates[] = __('status changed from :old to :new', ['old' => __($oldStatus), 'new' => __($this->selectedStatus)]);
        }
        if ($oldPriority !== $this->selectedPriority) {
            $updates[] = __('priority changed from :old to :new', ['old' => __($oldPriority), 'new' => __($this->selectedPriority)]);
        }
        if ($oldAssignedTo !== $this->selectedAgentId) {
            $oldAgentName = User::find($oldAssignedTo)?->display_name ?? __('None');
            $newAgentName = User::find($this->selectedAgentId)?->display_name ?? __('None');
            $updates[] = __('assigned to changed from :old to :new', ['old' => $oldAgentName, 'new' => $newAgentName]);
        }

        if (!empty($updates)) {
            $logMessage .= ' ' . __('updated ticket:');
            foreach ($updates as $update) {
                $logMessage .= "\n- " . $update;
            }
            $this->createLogMessage($logMessage);
        }

        session()->flash('settings_updated', __('Ticket settings updated successfully.'));
        $this->dispatch('messageSent');
    }

    public function closeTicket(): void
    {
        if (!Auth::user()->isAdmin() && $this->ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to close this ticket.');
        }

        $this->ticket->update(['status' => 'closed', 'last_replied_at' => now()]);
        $this->selectedStatus = 'closed';
        $this->createLogMessage(Auth::user()->display_name . ' ' . __('closed the ticket.'));
        session()->flash('status_updated', __('Ticket has been closed.'));
        $this->dispatch('messageSent');
    }

    public function reopenTicket(): void
    {
        if (!Auth::user()->isAdmin() && $this->ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to reopen this ticket.');
        }

        $this->ticket->update(['status' => 'open', 'last_replied_at' => now()]);
        $this->selectedStatus = 'open';
        $this->createLogMessage(Auth::user()->display_name . ' ' . __('reopened the ticket.'));
        session()->flash('status_updated', __('Ticket has been reopened.'));
        $this->dispatch('messageSent');
    }

    private function createLogMessage(string $logText): void
    {
        SupportMessage::create([
            'support_ticket_id' => $this->ticket->id,
            'user_id' => Auth::id(),
            'message' => '[LOG] ' . $logText,
        ]);
    }

    public function getListeners()
    {
        return ['messageSent' => '$refresh'];
    }

    public function render()
    {
        $this->ticket->load(['messages.user', 'user', 'assignedAgent']);

        $supportAgents = [];
        if (Auth::user()->isAdmin()) {
            $supportAgents = User::where('role', 'admin')->get(['id', 'username']);
        }

        $breadcrumbs = [
            ['label' => __('Support'), 'url' => route('support.tickets.index')],
            ['label' => __('Tickets'), 'url' => route('support.tickets.index')],
            ['label' => $this->ticket->subject],
        ];

        return view('livewire.support.ticket-show', [
            'supportAgents' => $supportAgents,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}