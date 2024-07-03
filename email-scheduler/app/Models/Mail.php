<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $fillable = [
        'subject', 'mailHeader', 'Emailbody', 'Emailfooter', 'attachedFile'
    ];

    // Relationships
    public function emailBatches()
    {
        return $this->hasMany(EmailBatch::class, 'mail_id');
    }
}
