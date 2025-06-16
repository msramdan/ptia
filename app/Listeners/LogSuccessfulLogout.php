<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;

class LogSuccessfulLogout
{
    /**
     * The request instance.
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $user = $event->user;
            activity('auth')
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => $this->request->ip(),
                    'user_agent' => $this->request->header('User-Agent'),
                ])
                ->log('User logged out');
        }
    }
}
