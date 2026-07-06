<?php

namespace App\Events;

use App\Models\Office;
use App\Models\Visitor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueStatsUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $adminPayload;
    public array $officePayload;
    public int $officeId;

    public function __construct(int $officeId)
    {
        $this->officeId = $officeId;
        $today = now()->toDateString();

        // Admin payload
        $this->adminPayload = [
            'visitorsToday' => Visitor::whereDate('created_at', $today)->count(),
            'activeQueues' => Office::whereHas('visitors', function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->whereIn('status', ['waiting', 'serving']);
            })->count(),
            'offices' => Office::withCount([
                'visitors as waiting' => function ($q) use ($today) {
                    $q->whereDate('created_at', $today)
                      ->where('status', 'waiting');
                },
            ])->get()->map(fn ($o) => [
                'id' => $o->id,
                'waiting' => $o->waiting,
            ])->toArray(),
        ];

        // Office/staff payload
        $servingVisitor = Visitor::where('office_id', $officeId)
            ->whereDate('created_at', $today)
            ->where('status', 'serving')
            ->first();

        $this->officePayload = [
            'currentServing' => $servingVisitor?->ticket_number,
            'waiting' => Visitor::where('office_id', $officeId)
                ->whereDate('created_at', $today)
                ->where('status', 'waiting')
                ->count(),
            'skipped' => Visitor::where('office_id', $officeId)
                ->whereDate('created_at', $today)
                ->where('status', 'skipped')
                ->count(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('queues.admin'),
            new Channel("queues.office.{$this->officeId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.stats.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'admin'  => $this->adminPayload,
            'office' => $this->officePayload,
        ];
    }
}
