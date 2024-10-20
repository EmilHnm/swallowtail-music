<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->get('status', null) == 'unread') {
            $notifications = \Auth::user()->unreadNotifications()->paginate(10);
        } else {
            $notifications  = \Auth::user()->notifications()->paginate(10);
        }
        return response()->json($notifications);
    }

    public function hasUnread()
    {
        $hasUnread = \Auth::user()->unreadNotifications->count();
        return response()->json(['hasUnread' => $hasUnread]);
    }

    public function markAsRead(Request $request)
    {
        $notification = \Auth::user()->notifications()->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'failed']);
    }

    public function markAllAsRead(Request $request)
    {
        $notifications = \Auth::user()->unreadNotifications->whereIn('id', $request->ids);
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json(['message' => 'success']);
    }

    public function delete(Request $request)
    {
        $notification = \Auth::user()->notifications()->where('id', $request->id)->first();
        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'failed']);
    }
}
