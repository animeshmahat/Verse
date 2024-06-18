<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        $user->notifications()->where('read', 0)->update(['read' => 1]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }
    public function addNotification(Request $request)
    {
        $user = Auth::user();
        $data = json_encode($request->input('data'));

        try {
            $user->notifications()->create([
                'data' => $data,
                'read' => false,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            // Duplicate entry
            if ($ex->getCode() === '23000') { // 23000 is the SQLSTATE code for integrity constraint violation
                return response()->json(['message' => 'Duplicate notification'], 409);
            }
            throw $ex;
        }

        return response()->json(['message' => 'Notification added']);
    }

    public function markNotificationsAsRead()
    {
        $user = Auth::user();
        $user->notifications()->where('read', false)->update(['read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'Notifications marked as read']);
    }
}
