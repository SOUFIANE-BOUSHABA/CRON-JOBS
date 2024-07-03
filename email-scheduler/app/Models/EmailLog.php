<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'batch_id', 'profile_id', 'status', 'sent_at'
    ];

    // Relationships
    public function batch()
    {
        return $this->belongsTo(EmailBatch::class, 'batch_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}

