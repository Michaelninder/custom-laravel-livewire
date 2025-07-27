<?php

namespace App\Livewire\Support;

use App\Models\SupportTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TicketsIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = SupportTicket::query()->with(['user', 'assignedAgent']);

        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $tickets = $query->latest('last_replied_at')->paginate(10);

        $breadcrumbs = [
            ['label' => __('Support'), 'url' => route('support.tickets.index')],
            ['label' => __('Tickets')],
        ];

        return view('livewire.support.tickets-index', [
            'tickets' => $tickets,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}