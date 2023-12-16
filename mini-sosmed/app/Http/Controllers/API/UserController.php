<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserDetail;

use App\Http\Resources\BaseResource;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserDetailRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Auth;

class UserController extends Controller
{
    /**
     * register
     *
     * @param  mixed $userRequest
     * @param  mixed $userDetailRequest
     * @return void
     */
    public function register(UserRequest $userRequest, UserDetailRequest $userDetailRequest)
    {
        try {
            $transaction = DB::transaction(function () use ($userRequest, $userDetailRequest) {        
                if ($userDetailRequest->hasFile('image')) {
                    $image = $userDetailRequest->file('image');
                    $image->storeAs('public/userDetail', $image->hashName());
                }

                $user = User::create([
                    'name'      => $userDetailRequest->first_name,
                    'email'     => $userRequest->email,
                    'password'  => Hash::make($userRequest->password)
                ]);

                $userDetail = $user->userDetails()->create([
                    'phone_number'  => $userDetailRequest->phone_number,
                    'image'         => $image?->hashName() ?? null,
                    'username'      => $userDetailRequest->username,
                    'first_name'    => $userDetailRequest->first_name,
                    'last_name'     => $userDetailRequest->last_name,
                    'date_of_birth' => $userDetailRequest->date_of_birth,
                ]);

                return new BaseResource(true, 'User Berhasil Didaftarkan!', null);
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }
    
    /**
     * login
     *
     * @param  mixed $loginRequest
     * @return void
     */
    public function login(LoginRequest $loginRequest)
    {
        try {
            if (Auth::attempt($loginRequest->all())) {
                $user = User::find(Auth::user()->id);

                $token = $user->createToken($user->name)->accessToken;
        
                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $user,
                ], 200);
            }
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(Request $request)
    {
        try {
            if(Auth::user()->tokens()->delete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Success!',  
                ]);
            }
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * show profile
     *
     * @return void
     */
    public function show()
    {
        $user = User::find(Auth::user()->id);

        return new BaseResource(true, 'Data User Ditampilkan!', $user->load('userDetails'));
    }

    /**
     * update
     *
     * @param  mixed $userDetailRequest
     * @return void
     */
    public function update(UserDetailRequest $userDetailRequest)
    {
        try {
            $transaction = DB::transaction(function () use ($userDetailRequest) {        
                $userDetail = UserDetail::where('user_id', Auth::user()->id);
        
                if ($userDetailRequest->hasFile('image')) {
                    $image = $userDetailRequest->file('image');
                    $image->storeAs('public/userDetail', $image->hashName());
        
                    Storage::delete('public/userDetail/'.basename($userDetail->image));
                }
                
                $userDetail->update([
                    'phone_number'  => $userDetailRequest->phone_number,
                    'image'         => $image?->hashName() ?? null,
                    'username'      => $userDetailRequest->username,
                    'first_name'    => $userDetailRequest->first_name,
                    'last_name'     => $userDetailRequest->last_name,
                    'date_of_birth' => $userDetailRequest->date_of_birth,
                ]);

                $user = User::find(Auth::user()->id);
                return new BaseResource(true, 'Data User Berhasil Diubah!', $user->load('userDetails'));
            });

            return $transaction;
        } catch (Exception $e) {
            return $this->jsonResponse($e->getMessage(), true);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $searchRequest
     * @return void
     */
    public function search(SearchRequest $searchRequest)
    {
        $queryString = (string) Str::of($searchRequest->name)
            ->trim()
            ->replace(' ', '%')
            ->append('%')
            ->prepend('%');

        $user = User::where('email', 'LIKE', $queryString)
                    ->orWhereHas('userDetails', fn ($q) =>
                      $q->where('username', 'LIKE', $queryString)
                        ->orWhere(DB::raw('concat(first_name, " ", last_name)'), 'LIKE', $queryString))
                    ->get();
        
        return new BaseResource(true, 'Hasil Pencarian.', $user->load('userDetails'));
    }
}
