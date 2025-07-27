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
        if ($this->ticket->status === 'closed' || $this->ticket->status === 'resolved') {
            session()->flash('error', __('Cannot send messages on a closed or resolved ticket. Please reopen it.'));
            return;
        }

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

        $updatesMade = false;
        $logMessageParts = [];
        $performer = Auth::user()->display_name;

        if ($oldStatus !== $this->selectedStatus) {
            $this->ticket->status = $this->selectedStatus;
            $updatesMade = true;
            $logMessageParts[] = __('status changed from :old to :new', [
                'old' => __($oldStatus),
                'new' => __($this->selectedStatus)
            ]);
        }
        if ($oldPriority !== $this->selectedPriority) {
            $this->ticket->priority = $this->selectedPriority;
            $updatesMade = true;
            $logMessageParts[] = __('priority changed from :old to :new', [
                'old' => __($oldPriority),
                'new' => __($this->selectedPriority)
            ]);
        }
        if ($oldAssignedTo !== $this->selectedAgentId) {
            $this->ticket->assigned_to = $this->selectedAgentId;
            $updatesMade = true;
            $oldAgentName = User::find($oldAssignedTo)?->display_name ?? __('None');
            $newAgentName = User::find($this->selectedAgentId)?->display_name ?? __('None');
            $logMessageParts[] = __('assigned to changed from :old to :new', [
                'old' => $oldAgentName,
                'new' => $newAgentName
            ]);
        }

        if ($updatesMade) {
            $this->ticket->save();
            $logMessage = $performer . ' ' . __('updated ticket:');
            foreach ($logMessageParts as $part) {
                $logMessage .= "\n- " . $part;
            }
            $this->createLogMessage($logMessage);
            session()->flash('settings_updated', __('Ticket settings updated successfully.'));
            $this->dispatch('messageSent');
        } else {
            session()->flash('info', __('No changes detected.'));
        }
    }

    public function closeTicket(): void
    {
        if (!Auth::user()->isAdmin() && $this->ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to close this ticket.');
        }

        if ($this->ticket->status !== 'closed') {
            $this->ticket->update(['status' => 'closed', 'last_replied_at' => now()]);
            $this->selectedStatus = 'closed';
            $this->createLogMessage(Auth::user()->display_name . ' ' . __('closed the ticket.'));
            session()->flash('status_updated', __('Ticket has been closed.'));
            $this->dispatch('messageSent');
        } else {
            session()->flash('info', __('Ticket is already closed.'));
        }
    }

    public function reopenTicket(): void
    {
        if (!Auth::user()->isAdmin() && $this->ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to reopen this ticket.');
        }

        if ($this->ticket->status === 'closed' || $this->ticket->status === 'resolved') {
            $this->ticket->update(['status' => 'open', 'last_replied_at' => now()]);
            $this->selectedStatus = 'open';
            $this->createLogMessage(Auth::user()->display_name . ' ' . __('reopened the ticket.'));
            session()->flash('status_updated', __('Ticket has been reopened.'));
            $this->dispatch('messageSent');
        } else {
            session()->flash('info', __('Ticket is not closed or resolved.'));
        }
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
            $supportAgents = User::where('role', 'admin')->get(['id', 'display_name']);
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