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
            'profile_id' => 'required|exists:profiles,id',
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

        $profile = Profile::findOrFail($validatedData['profile_id']);
        $this->sendEmailsToProfile($mail, $emailBatch, $profile);

        return response()->json([
            'mail' => $mail,
            'emailBatch' => $emailBatch,
        ], 201);
    }

    protected function sendEmailsToProfile($mail, $emailBatch, $profile)
    {
        for ($i = 0; $i < $emailBatch->quantity; $i++) {
            $this->sendEmail($mail, $profile);
            sleep($emailBatch->interval_minutes * 60); // Wait for the specified interval
        }

        $emailBatch->status = 'completed';
        $emailBatch->save();
    }

    protected function sendEmail($mail, $profile)
    {
        \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($mail, $profile) {
            $message->to($profile->email)
                ->subject($mail->subject)
                ->setBody($mail->Emailbody, 'text/html');
        });

        EmailLog::create([
            'batch_id' => $mail->emailBatches()->first()->id, // Assuming one batch per email for simplicity
            'profile_id' => $profile->id,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
