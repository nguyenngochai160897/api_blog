<?php

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

function responseHelper($data){
    return response()->json(
        [array_key_first($data) => $data[array_key_first($data)]], $data['status_code']);
}

function isAdmin(){
    // try {
        $userCurrent = JWTAuth::parseToken()->authenticate();
        return $userCurrent['account_type']=='admin' ? true : false;
    // } catch (TokenExpiredException $e) {
    //     return [
    //         'error' => 'token_expired',
    //         'status_code' => 400
    //     ];
    // } catch (TokenInvalidException $e) {
    //     return [
    //         'error' => 'token_invalid',
    //         'status_code' => 400
    //     ];
    // } catch (JWTException $e) {
    //     return [
    //         'error' => 'token_absent',
    //         'status_code' => 400
    //     ];
    // }
}
