<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_picture',
        'name',
        'username',
<<<<<<< HEAD
        'role',
        'isProfilePrivate',
        'total_claims',
        'total_votes',
        'total_comments',
        'total_contributions',
        'total_received_thanks',
        'biography',
        'verification_token',
=======
        'password',
        'email',
>>>>>>> 57aed5eb73a391276cdbbddbda0f80358a55b268
        'email_verified_at', 
        'verification_token',
        'biography',
        'is_private_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
<<<<<<< HEAD
        'isProfilePrivate' => 'boolean',
=======
        'is_private_user' => 'boolean',
>>>>>>> 57aed5eb73a391276cdbbddbda0f80358a55b268
    ];

    public function thanks()
    {
        return $this->hasMany(Thanks::class);
    }

}
