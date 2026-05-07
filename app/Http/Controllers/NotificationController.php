<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        return auth()->user()->notifications()->latest()->limit(10)->get();
    }

    public function markAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markOneAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }
}
