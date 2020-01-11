<?php

namespace App\Http\Services;
use App\Models\User;
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
        try {
            if (!$token = JWTAuth::attempt($data)) {
                return [
                    'error' => 'invalid_credentials',
                    'status_code' => 400
                ];
            }
        } catch (JWTException $e) {
            return [
                'error' => 'could_not_create_token',
                'status_code' => 500
            ];
        }
        return [
            'data' => $token,
            'status_code' => 200
        ];
    }
}
