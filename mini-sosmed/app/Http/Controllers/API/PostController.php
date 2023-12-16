<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostComment;
use App\Models\PostLike;

use App\Http\Resources\BaseResource;

use App\Http\Requests\Post\PostRequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = null;
        if (Post::all()) {
            $posts = Post::latest()
                        ->paginate(5)
                        ->load('images', 'comments');
        }

        return new BaseResource(true, 'List Data Posts', $posts);
    }
    
    /**
     * get only authenticated user post
     *
     * @return void
     */
    public function home()
    {
        $posts = null;
        if (Post::where('created_by', auth()->user->id)) {
            $posts = Post::where('created_by', auth()->user->id)
                        ->paginate(5)
                        ->load('images', 'comments');
        }

        return new BaseResource(true, 'List Data Posts', $posts);
    }

    /**
     * store
     *
     * @param  mixed $postRequest
     * @return void
     */
    public function store(PostRequest $postRequest)
    {
        try {
            $transaction = DB::transaction(function () use ($postRequest) {
                $post = Post::create([
                    'caption'    => $postRequest->caption,
                    'created_by' => Auth::user()->id,
                ]);
                
                if ($postRequest->hasFile('images')) {
                    foreach ($postRequest->file('images') as $uploadedImage) {
                        $uploadedImage->storeAs('public/posts', $uploadedImage->hashName());
                    
                        $postImage = $post->images()->create([
                            'image' => $uploadedImage->hashName(),
                        ]);
                    }                    
                }
        
                return new BaseResource(true, 'Data Post Berhasil Ditambahkan!', $post->load('images'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }
    
    /**
     * show
     *
     * @param  mixed $post
     * @return void
     */
    public function show($post)
    {
        $post = Post::find($post);
        
        return new BaseResource(true, 'Detail Data Post!', $post->load('images'));
    }
    
    /**
     * update
     *
     * @param  mixed $post
     * @param  mixed $postRequest
     * @return void
     */
    public function update(PostRequest $postRequest, $post)
    {
        try {
            $transaction = DB::transaction(function () use ($postRequest, $post) {
                $post = Post::find($post);

                $post->update([
                    'caption'   => $postRequest->caption,
                    'updated_by'=> Auth::user()->id,
                ]);
        
                return new BaseResource(true, 'Caption Post Berhasil Diubah!', $post->load('images'));
            });
    
            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy($post)
    {
        try {
            $post = Post::find($post);
    
            $postImages = PostImage::where('post_id', $post)->get();
            foreach ($postImages as $postImage) {
                Storage::delete('public/posts/'.basename($postImage->image));
                $postImage->delete();
            }

            $post->likes()->delete();
            $post->comments()->delete();
            $post->delete();
    
            return new BaseResource(true, 'Data Post Berhasil Dihapus!', null);
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * like
     *
     * @param  mixed $post
     * @return void
     */
    public function like($post)
    {
        $post = Post::find($post);

        $postLike = PostLike::where([['post_id', $post->id], ['created_by', Auth::user()->id]])->first();

        if ($postLike) {
            $postLike->delete();            
        } else {
            $post->likes()->create([
                'created_by' => Auth::user()->id,
            ]);
        }

        $post->like_count = $post->countLikes();

        return new BaseResource(true, 'Like berhasil diperbarui!', $post->load('likes.creator'));
    }
}