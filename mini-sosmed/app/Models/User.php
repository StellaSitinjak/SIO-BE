<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function userDetails(): HasMany
    {
        return $this->hasMany(UserDetail::class, 'user_id', 'id');
    }

    public function followings(): HasMany
    {
        return $this->hasMany(UserFollow::class, 'user_follower_id');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(UserFollow::class, 'user_following_id');
    }

    public function postCreator(): HasMany
    {
        return $this->hasMany(Post::class, 'created_by');
    }

    public function postUpdater(): HasMany
    {
        return $this->hasMany(Post::class, 'updated_by');
    }

    public function commentCreator(): HasMany
    {
        return $this->hasMany(PostComment::class, 'created_by');
    }

    public function commentUpdater(): HasMany
    {
        return $this->hasMany(PostComment::class, 'updated_by');
    }

    public function postLike(): HasMany
    {
        return $this->hasMany(PostLike::class, 'created_by');
    }
}
