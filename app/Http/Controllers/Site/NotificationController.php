<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function addNotification(Request $request)
    {
        $user = Auth::user();
        $data = $request->input('data');

        try {
            $user->notifications()->create([
                'data' => json_encode($data),
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
}
