<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SupportLog;
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
            session()->flash('error', __('support.cannot_send_on_closed'));
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

        if ($oldStatus !== $this->selectedStatus) {
            $this->ticket->status = $this->selectedStatus;
            $updatesMade = true;
            SupportLog::create([
                'support_ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
                'type' => 'status_change',
                'data' => [
                    'old_status' => $oldStatus,
                    'new_status' => $this->selectedStatus,
                ],
            ]);
        }
        if ($oldPriority !== $this->selectedPriority) {
            $this->ticket->priority = $this->selectedPriority;
            $updatesMade = true;
            SupportLog::create([
                'support_ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
                'type' => 'priority_change',
                'data' => [
                    'old_priority' => $oldPriority,
                    'new_priority' => $this->selectedPriority,
                ],
            ]);
        }
        if ($oldAssignedTo !== $this->selectedAgentId) {
            $this->ticket->assigned_to = $this->selectedAgentId;
            $updatesMade = true;
            SupportLog::create([
                'support_ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
                'type' => 'assignment_change',
                'data' => [
                    'old_agent_id' => $oldAssignedTo,
                    'old_agent_name' => User::find($oldAssignedTo)?->display_name ?? __('strings.none'),
                    'new_agent_id' => $this->selectedAgentId,
                    'new_agent_name' => User::find($this->selectedAgentId)?->display_name ?? __('strings.none'),
                ],
            ]);
        }

        if ($updatesMade) {
            $this->ticket->save();
            session()->flash('settings_updated', __('support.settings_updated_success'));
            $this->dispatch('messageSent');
        } else {
            session()->flash('info', __('strings.no_changes_detected'));
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
            SupportLog::create([
                'support_ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
                'type' => 'closed_ticket',
            ]);
            session()->flash('status_updated', __('support.ticket_closed_success'));
            $this->dispatch('messageSent');
        } else {
            session()->flash('info', __('support.ticket_already_closed'));
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
            SupportLog::create([
                'support_ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
                'type' => 'reopened_ticket',
            ]);
            session()->flash('status_updated', __('support.ticket_reopened_success'));
            $this->dispatch('messageSent');
        } else {
            session()->flash('info', __('support.ticket_not_closed_resolved'));
        }
    }

    public function getListeners()
    {
        return ['messageSent' => '$refresh'];
    }

    public function render()
    {
        $this->ticket->load(['messages.user', 'user', 'assignedAgent', 'logs.user']);

        $combinedFeed = $this->ticket->messages->map(function ($item) {
            $item->is_log = false;
            return $item;
        })->merge($this->ticket->logs->map(function ($item) {
            $item->is_log = true;
            return $item;
        }))->sortBy('created_at');

        $supportAgents = [];
        if (Auth::user()->isAdmin()) {
            $supportAgents = User::where('role', 'admin')->get(['id', 'display_name']);
        }

        $breadcrumbs = [
            ['label' => __('support.general_title'), 'url' => route('support.tickets.index')],
            ['label' => __('strings.tickets'), 'url' => route('support.tickets.index')],
            ['label' => $this->ticket->subject],
        ];

        return view('livewire.support.ticket-show', [
            'supportAgents' => $supportAgents,
            'breadcrumbs' => $breadcrumbs,
            'combinedFeed' => $combinedFeed,
        ]);
    }
}