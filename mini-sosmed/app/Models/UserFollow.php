<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_following_id',
        'user_follower_id',
        'status',
    ];

    public function userFollowing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_following_id');
    }

    public function userFollower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_follower_id');
    }
}
