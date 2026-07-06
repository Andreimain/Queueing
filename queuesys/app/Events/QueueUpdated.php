<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $officeId;
    public array $payload;

    public function __construct(int $officeId, array $payload)
    {
        $this->officeId = $officeId;
        $this->payload  = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new Channel("queues.office.{$this->officeId}");
    }

    public function broadcastAs(): string
    {
        return 'queue.updated';
    }
}
