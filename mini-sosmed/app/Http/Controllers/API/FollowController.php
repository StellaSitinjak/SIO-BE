<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserFollow;

use App\Http\Resources\BaseResource;
use App\Http\Resources\FollowResource;

use App\Http\Requests\IdRequest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;

class FollowController extends Controller
{
    /**
     * follow
     *
     * @param  mixed $user
     * @return void
     */
    public function follow($user)
    {
        try {
            $transaction = DB::transaction(function () use ($user) {        
                $user = User::find($user);

                $userFollow = UserFollow::create([
                    'user_following_id' => $user->id,
                    'user_follower_id'  => Auth::user()->id,
                    'status'            => true,
                ]);

                $user = User::find(Auth::user()->id);
                return new BaseResource(true, 'User berhasil diikuti!', $user->load('followings'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * unfollow
     *
     * @param  mixed $user
     * @return void
     */
    public function unfollow($user)
    {
        try {
            $transaction = DB::transaction(function () use ($user) {        
                $userFollow = UserFollow::where('user_following_id', $user)->delete();

                $user = User::find(Auth::user()->id);
                return new BaseResource(true, 'Berhasil dihapus!', $user->load('followings'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * following
     *
     * @return void
     */
    public function following()
    {
        $user = User::find(Auth::user()->id);

        return new BaseResource(true, 'Akun yang Diikuti.', $user->load('followings.userFollowing.userDetails'));
    }

    /**
     * follower
     *
     * @return void
     */
    public function follower()
    {
        $user = User::find(Auth::user()->id);

        return new BaseResource(true, 'Akun yang Mengikuti.', $user->load('followers.userFollower.userDetails'));
    }
}
