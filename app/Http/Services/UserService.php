<?php

namespace App\Http\Services;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService{
    private $loginAfterRegister;
    public function __construct() {
        $this->loginAfterRegister = true;
    }

    public function createUser($data, $token=null){
        $data['passwordNotHash'] = $data['password'];
        $data['password'] = bcrypt($data['passwordNotHash']);

        if($token){
            $data['account_type']='admin';
        }
        else {
            $data['account_type']='member';
        }
        $user = User::create($data);
        if($this->loginAfterRegister){
            return $this->login(['email' => $data['email'], 'password' => $data['passwordNotHash']]);
        }
        return [
            'data' => $user,
            'status_code' => 200
        ];
    }

    public function login($data){
        $user = User::where('email', $data['email'])->firstOrFail();

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        }

        try {
            $customClaims = ['id' => $user->id, 'account_type' => $user->account_type]; // Here you can pass user data on claims
            $token = JWTAuth::fromUser($user, $customClaims);
        } catch (JWTException $e) {
            return response()->json(['error' => 'auth_error'], 500);
        }
        return [
            'data' => $token,
            'status_code' => 200
        ];
    }
}
