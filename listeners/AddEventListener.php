<?php


namespace App\listeners;


use App\core\Event;


abstract class AddEventListener
{
    protected static array $eventListener = [
        'email-verify' => [EmailVerificationListener::class, 'handle']
    ];

}