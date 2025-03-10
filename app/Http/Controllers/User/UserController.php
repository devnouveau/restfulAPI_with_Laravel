<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    // TODO : (프로젝트 전체) request class, service class 로직 분리
    // TODO : (프로젝트 전체) validation rule 보강 (최대길이, 타입 string, nullable, 필드존재시 빈값 허용앉는 filled 등)
    // TODO : (프로젝트 전체) error msg 언어파일로 분리

    public function __construct()
    {
        $this->middleware('client.credentials')->only(['resend']);
        $this->middleware('auth:api')->except(['showRegisterForm', 'store', 'verify', 'resend']);
        $this->middleware('transform.input:' . UserTransformer::class)->only(['update']);
        $this->middleware('scope:manage-account')->only(['show', 'update']);
        $this->middleware('can:view,user')->only('show');
        $this->middleware('can:update,user')->only('update');
        $this->middleware('can:delete,user')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponsea
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $request->validate($rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        return $this->showOne($user, 201);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        $request->validate($rules);

        if ($request->has('name') && $user->name !== $request->name) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email !== $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }

            $user->admin = $request->admin;
        }

        if ($user->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 422);

        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->showOne($user); // TODO : 삭제된 유저 반환되지 않도록?
    }

    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('The account has been verified succesfully');
    }

    public function resend(User $user)
    {
        if ($user->isVerified()) {
            return $this->errorResponse('This user is already verified', 409);
        }

        retry(5, function() use ($user) {
            Mail::to($user)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('The verification email has been resend');
    }
}
