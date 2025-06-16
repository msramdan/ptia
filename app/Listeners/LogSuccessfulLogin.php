<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct() {}

    public function handle(Login $event): void
    {
        activity('auth')
            ->causedBy($event->user)
            ->log('User logged in');
    }
}
