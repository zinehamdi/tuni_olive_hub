<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('listings', fn() => true);
Broadcast::channel('loads', fn() => true);
Broadcast::channel('orders', fn() => true);
Broadcast::channel('trips', fn() => true);
Broadcast::channel('threads.{threadId}', function ($user, $threadId) {
    // Authorize if user appears in thread participants; simplified as true for stub
    return !empty($user);
});
