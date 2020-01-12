<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function($user){
            try {
                $userCurrent = JWTAuth::parseToken()->authenticate();
                return $userCurrent['account_type']=='admin' ;
            } catch (TokenExpiredException $e) {
                return response()->json(['token_expired'], 400);
            } catch (TokenInvalidException $e) {
                return response()->json(['token_invalid'], 400);
            } catch (JWTException $e) {
                return response()->json(['token_absent'], 400);
            }
        });
    }
}
