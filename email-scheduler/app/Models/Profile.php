<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'firstName', 'lastName', 'email', 'tel', 'birthDay', 'sexe'
    ];
    
    // Relationships
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'profile_id');
    }
}
