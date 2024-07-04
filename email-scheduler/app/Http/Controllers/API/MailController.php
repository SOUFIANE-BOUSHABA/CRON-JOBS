<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Mail;
use App\Models\EmailBatch;
use App\Models\EmailLog;
use App\Models\Profile;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required|string',
            'mailHeader' => 'required|string',
            'Emailbody' => 'required|string',
            'Emailfooter' => 'required|string|max:500',
            'attachedFile' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'interval_minutes' => 'required|integer|min:1',
            'profile_id' => 'required|array',
            'profile_id.*' => 'exists:profiles,id',
        ]);

        $mail = Mail::create([
            'subject' => $validatedData['subject'],
            'mailHeader' => $validatedData['mailHeader'],
            'Emailbody' => $validatedData['Emailbody'],
            'Emailfooter' => $validatedData['Emailfooter'],
            'attachedFile' => $validatedData['attachedFile'],
        ]);

        $emailBatch = EmailBatch::create([
            'mail_id' => $mail->id,
            'quantity' => $validatedData['quantity'],
            'interval_minutes' => $validatedData['interval_minutes'],
            'status' => 'pending',
        ]);

        foreach ($validatedData['profile_id'] as $profileId) {
            EmailLog::create([
                'batch_id' => $emailBatch->id,
                'profile_id' => $profileId,
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'mail' => $mail,
            'emailBatch' => $emailBatch,
        ], 201);
    }
}
