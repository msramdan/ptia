<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {
        $user = $event->user;
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->header('User-Agent'),
            ])
            ->log('User logged in');
    }
}
