<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\SupportTicket;

#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    public int $totalUsers;
    public int $totalTickets;
    public int $openTickets;
    public int $closedTickets;

    public function mount(): void
    {
        $this->totalUsers = User::count();
        $this->totalTickets = SupportTicket::count();
        $this->openTickets = SupportTicket::where('status', 'open')->count();
        $this->closedTickets = SupportTicket::where('status', 'closed')->count();
    }

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('nav.admin_dashboard')],
        ];

        return view('livewire.admin.dashboard', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}