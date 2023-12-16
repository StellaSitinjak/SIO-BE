<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostCommentReply;

use App\Http\Requests\Comment\PostCommentReplyIdRequest;
use App\Http\Requests\Comment\CommentRequest;

use App\Http\Resources\BaseResource;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;

class CommentController extends Controller
{
    /**
     * store
     *
     * @param  mixed $commentRequest
     * @param  mixed $post
     * @return void
     */
    public function store(CommentRequest $commentRequest, $post)
    {
        try {
            $transaction = DB::transaction(function () use ($commentRequest, $post) {
                $post = Post::find($post);

                $postComment = $post->comments()->create([
                    'comment' => $commentRequest->comment,
                    'created_by' => Auth::user()->id,
                ]);

                return new BaseResource(true, 'Komentar Berhasil Ditambahkan!', $post->load('comments.replies'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * reply
     *
     * @param  mixed $commentRequest
     * @param  mixed $post
     * @param  mixed $comment
     * @return void
     */
    public function reply(CommentRequest $commentRequest, $post, $comment)
    {
        try {
            $transaction = DB::transaction(function () use ($commentRequest, $post, $comment) {
                $postComment = PostComment::find($comment);

                $commentReply = $postComment->replies()->create([
                    'comment' => $commentRequest->comment,
                    'created_by' => Auth::user()->id,
                ]);

                return new BaseResource(true, 'Berhasil menambah balasan komentar!', $postComment->load('replies'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * update
     *
     * @param  mixed $commentRequest
     * @param  mixed $post
     * @param  mixed $comment
     * @return void
     */
    public function update(CommentRequest $commentRequest, $post, $comment)
    {
        try {
            $transaction = DB::transaction(function () use ($commentRequest, $post, $comment) {
                $postComment = PostComment::find($comment);
                
                if ($commentRequest->id) {
                    $commentReply = PostCommentReply::find($commentRequest->id);
                    
                    $commentReply->update([
                        'comment'    => $commentRequest->comment,
                        'updated_by' => Auth::user()->id,
                    ]);
                } else {
                    $postComment->update([
                        'comment'    => $commentRequest->comment,
                        'updated_by' => Auth::user()->id,
                    ]);
                }

                return new BaseResource(true, 'Komentar Berhasil Diubah!', $postComment->load('replies'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $postCommentReplyIdRequest
     * @param  mixed $post
     * @param  mixed $comment
     * @return void
     */
    public function destroy(PostCommentReplyIdRequest $postCommentReplyIdRequest, $post, $comment)
    {
        try {
            $postComment = PostComment::find($comment);
            
            if ($postCommentReplyIdRequest->id) {
                $commentReply = PostCommentReply::find($postCommentReplyIdRequest->id);

                $commentReply->delete();
            } else {
                $commentReply = PostCommentReply::where('post_comment_id', $comment);

                $commentReply->delete();
                $postComment->delete();
            }

            return new BaseResource(true, 'Komentar Berhasil Dihapus!', null);
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }
}
