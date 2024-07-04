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
        $batches = EmailBatch::whereIn('status', ['pending', 'in-progress'])->get();

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
        $emailLogs = EmailLog::where('batch_id', $batch->id)->where('status', 'pending')->get();

        foreach ($emailLogs as $emailLog) {
            $profile = Profile::findOrFail($emailLog->profile_id);

            Mail::send([], [], function ($message) use ($batch, $profile) {
                $message->to($profile->email)
                        ->subject($batch->mail->subject)
                        ->setBody($batch->mail->Emailbody, 'text/html');
            });

            $emailLog->status = 'sent';
            $emailLog->sent_at = now();
            $emailLog->save();

            sleep($batch->interval_minutes * 60); // Wait for the specified interval
        }

        $batch->quantity--;

        if ($batch->quantity > 0) {
            $batch->status = 'in-progress';
          
        }
        if($batch->quantity == 0){
            $batch->status = 'completed';
        }
        $batch->save();
    }
}
