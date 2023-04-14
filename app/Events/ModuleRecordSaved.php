<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleRecordSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private array $modules;

    private array $relationship;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($modules, $relationship)
    {
        $this->modules = $modules;
        $this->relationship = $relationship;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
