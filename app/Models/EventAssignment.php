<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAssignment extends Model
{
    protected $fillable = ['event_id', 'member_id', 'status', 'confirmed_at', 'notified'];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'notified' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }
}
