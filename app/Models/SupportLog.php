<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'type',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedMessageAttribute(): string
    {
        $userName = $this->user ? $this->user->display_name : __('System');
        $data = $this->data ?? [];

        switch ($this->type) {
            case 'status_change':
                return __(':user changed status from :old_status to :new_status.', [
                    'user' => $userName,
                    'old_status' => __($data['old_status']),
                    'new_status' => __($data['new_status']),
                ]);
            case 'priority_change':
                return __(':user changed priority from :old_priority to :new_priority.', [
                    'user' => $userName,
                    'old_priority' => __($data['old_priority']),
                    'new_priority' => __($data['new_priority']),
                ]);
            case 'assignment_change':
                return __(':user changed assignment from :old_agent to :new_agent.', [
                    'user' => $userName,
                    'old_agent' => $data['old_agent_name'] ?? __('None'),
                    'new_agent' => $data['new_agent_name'] ?? __('None'),
                ]);
            case 'closed_ticket':
                return __(':user closed the ticket.', ['user' => $userName]);
            case 'reopened_ticket':
                return __(':user reopened the ticket.', ['user' => $userName]);
            case 'ticket_created':
                return __(':user created the ticket.', ['user' => $userName]);
            default:
                return __(':user performed an action.', ['user' => $userName]);
        }
    }
}