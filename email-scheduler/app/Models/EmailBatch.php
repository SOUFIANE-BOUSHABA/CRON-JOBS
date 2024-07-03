<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailBatch extends Model
{
    protected $fillable = [
        'mail_id', 'quantity', 'interval_minutes', 'status'
    ];

    // Relationships
    public function mail()
    {
        return $this->belongsTo(Mail::class, 'mail_id');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'batch_id');
    }
}
