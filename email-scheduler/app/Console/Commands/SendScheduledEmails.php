<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailBatch;
use App\Models\EmailLog;
use App\Models\Profile;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendScheduledEmails extends Command
{
    protected $signature = 'email:send-scheduled';
    protected $description = 'Send scheduled emails based on batches';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $batches = EmailBatch::where('status', 'pending')
                             ->orWhere('status', 'in-progress')
                             ->get();

        foreach ($batches as $batch) {
            if ($batch->status == 'pending') {
                $batch->status = 'in-progress';
                $batch->save();
            }
            $this->processBatch($batch);
        }
    }

    protected function processBatch($batch)
    {
        // Load the batch with its mail and associated email logs
        $emailBatch = $batch->load('mail', 'emailLogs.profile');
    
        foreach ($emailBatch->emailLogs as $emailLog) {
            $profile = Profile::findOrFail($emailLog->profile_id); // Fetch the profile
    
            // Send email using Mail facade
            Mail::send([], [], function ($message) use ($emailBatch, $emailLog, $profile) {
                $message->to($profile->email)
                    ->subject($emailBatch->mail->subject)
                    ->setBody($emailBatch->mail->Emailbody, 'text/html');
            });
    
            $emailLog->status = 'sent';
            $emailLog->sent_at = now();
            $emailLog->save();
    
            sleep($emailBatch->interval_minutes * 60); 
        }
    
        $batch->quantity--;
        if ($batch->quantity == 0) {
            $batch->status = 'completed';
        }
       
        $batch->save();
    }
    

    protected function sendEmail($emailLog)
    {
        $mail = $emailLog->batch->mail;
        $profile = $emailLog->profile;

        Mail::send([], [], function ($message) use ($mail, $profile) {
            $message->to($profile->email)
                ->subject($mail->subject)
                ->setBody($mail->Emailbody, 'text/html');
        });
    }
}
