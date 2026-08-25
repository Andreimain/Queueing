<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueAnnounce implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $officeId,
        public string $ticket,
        public string $cashier
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("queues.office.{$this->officeId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.announce';
    }
}
