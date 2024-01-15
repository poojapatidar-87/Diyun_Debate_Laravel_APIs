<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;
    protected $fillable = ['team_id', 'email', 'role', 'invite_message', 'notify_by_email', 'token'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
