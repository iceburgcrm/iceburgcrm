<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubpanelModuleRecordSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private Array $modules;
    private Array $relationship;
    private String $record_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($modules, $relationship, $record_id)
    {
        $this->modules=$modules;
        $this->relationship=$relationship;
        $this->record_id=$record_id;
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
