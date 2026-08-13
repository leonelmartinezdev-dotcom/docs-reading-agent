<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use IlluminateAuthEventsLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateChatToken
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $token = $event->user
            ->createToken('chat-agent-token', ["chat:agent"])
            ->plainTextToken;

        session([
            'chat-agent-token' => $token,
        ]);
    }
}
