<?php

namespace App\core;

class Event {
    private static array $events = [];

    public static function Listen(string $name, callable $callback) {
        self::$events[$name][] = $callback;
    }

    public static function Dispatch($name, $argument = null) {
        foreach (self::$events[$name] as $event => $callback) {
            if($argument && is_array($argument)) {
                call_user_func_array($callback, $argument);
            }
            elseif ($argument && !is_array($argument)) {
                call_user_func($callback, $argument);
            }
            else {
                call_user_func($callback);
            }
        }
    }
}

class User {
    public function login() {
        return true;
    }

    public function logout() {
        return true;
    }

    public function updated() {
        return true;
    }
}

// Usage
// ==================================

Event::Listen('login', function(){
    echo 'Event user login fired! <br>';
});

$user = new User();

if($user->login()) {
    Event::Dispatch('login');
}

// Usage with param
// ==================================

Event::Listen('logout', function($param){
    echo 'Event '. $param .' logout fired! <br>';
});

if($user->logout()) {
    Event::Dispatch('logout', 'user');
}


// Usage with param as array
// ==================================

Event::Listen('updated', function($param1, $param2){
    echo 'Event ('. $param1 .', '. $param2 .') updated fired! <br>';
});

if($user->updated()) {
    Event::Dispatch('updated', ['x', 'y']);
}