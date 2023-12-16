<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PostCommentReply extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'post_comment_id',
        'comment',
        'created_by',
        'updated_by',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(PostComment::class);
    }
}
