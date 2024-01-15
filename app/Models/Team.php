<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Ensure you use the correct namespace

class Team extends Model
{
    use HasFactory;
    protected $fillable = ['name','token']; // Specify the fields that can be mass-assigned

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'team_id');
    }
}
