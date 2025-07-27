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
        $userName = $this->user ? $this->user->display_name : __('strings.system');
        $data = $this->data ?? [];

        switch ($this->type) {
            case 'status_change':
                return __('support.log_status_change', [
                    'user' => $userName,
                    'old_status' => __('strings.status_' . $data['old_status']),
                    'new_status' => __('strings.status_' . $data['new_status']),
                ]);
            case 'priority_change':
                return __('support.log_priority_change', [
                    'user' => $userName,
                    'old_priority' => __('strings.priority_' . $data['old_priority']),
                    'new_priority' => __('strings.priority_' . $data['new_priority']),
                ]);
            case 'assignment_change':
                return __('support.log_assignment_change', [
                    'user' => $userName,
                    'old_agent' => $data['old_agent_name'] ?? __('strings.none'),
                    'new_agent' => $data['new_agent_name'] ?? __('strings.none'),
                ]);
            case 'closed_ticket':
                return __('support.log_closed_ticket', ['user' => $userName]);
            case 'reopened_ticket':
                return __('support.log_reopened_ticket', ['user' => $userName]);
            case 'ticket_created':
                return __('support.log_ticket_created', ['user' => $userName]);
            default:
                return __(':user performed an action.', ['user' => $userName]);
        }
    }
}