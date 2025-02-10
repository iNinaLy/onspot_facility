<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationToken;

class NotificationTokenController extends Controller
{
    public function updateToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type'  => 'required|string', 
        ]);

        // Retrieve the authenticated user.
        $user = $request->user();

        // Save or update the notification token.
        NotificationToken::updateOrCreate(
            ['user_id' => $user->id], // Matching by user_id (add device_id here if needed).
            [
                'device_token' => $request->device_token,
                'device_type'  => $request->device_type,
            ]
        );

        return response()->json(['message' => 'Device token updated.'], 200);
    }
}
