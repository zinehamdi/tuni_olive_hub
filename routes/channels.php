<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('listings', function ($user) {
    \Illuminate\Support\Facades\Log::info('Broadcasting auth for listings. User: ' . ($user ? $user->id : 'Guest'));
    return true;
});

Broadcast::channel('loads', function ($user) {
    \Illuminate\Support\Facades\Log::info('Broadcasting auth for loads. User: ' . ($user ? $user->id : 'Guest'));
    return true;
});

Broadcast::channel('orders', function ($user) {
    \Illuminate\Support\Facades\Log::info('Broadcasting auth for orders. User: ' . ($user ? $user->id : 'Guest'));
    return true;
});

Broadcast::channel('trips', function ($user) {
    \Illuminate\Support\Facades\Log::info('Broadcasting auth for trips. User: ' . ($user ? $user->id : 'Guest'));
    return true;
});

Broadcast::channel('threads.{threadId}', function ($user, $threadId) {
    \Illuminate\Support\Facades\Log::info('Broadcasting auth for thread: ' . $threadId . ' User: ' . ($user ? $user->id : 'Guest'));
    return !empty($user);
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    \Illuminate\Support\Facades\Log::info('Broadcasting auth for User Private Channel: ' . $id . ' Current User: ' . ($user ? $user->id : 'Guest'));
    return (int) $user->id === (int) $id;
});
