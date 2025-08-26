<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        // Return the authenticated user's notifications
        return $this->success([
            'notifications' => NotificationResource::collection($request->user()->notifications->reverse()),
            'read_count' => $request->user()->readNotifications->count(),
            'unread_count' => $request->user()->unreadNotifications->count()
        ], 'Notifications fetched successfully');
    }

    public function markAsRead(Request $request, $id)
    {
        // Mark a specific notification as read
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->success([], 'Notification marked as read');
    }

    public function markAllAsRead(Request $request)
    {
        // Mark all notifications as read
        $request->user()->unreadNotifications->markAsRead();

        return $this->success([], 'All notifications marked as read');
    }
}
