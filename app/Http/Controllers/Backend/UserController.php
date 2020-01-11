<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationFormRequest;
use App\Http\Services\UserService;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function register(RegistrationFormRequest $request)
    {
        $data = $this->userService->createUser($request->all());
        return responseHelper($data);
    }

    public function createUser(RegistrationFormRequest $request){
        $data = $this->userService->createUser($request->all(), true);
        return responseHelper($data);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email','password');
        $data = $this->userService->login($credentials);
        return responseHelper($data);
    }

    public function forgotPassword($email){

    }
}
