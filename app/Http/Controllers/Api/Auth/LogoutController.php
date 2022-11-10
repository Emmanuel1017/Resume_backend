<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function logout()
    {
        try{

        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);

       $token = \JWTAuth::parseToken();
       \JWTAuth::manager()->invalidate(
       new \Tymon\JWTAuth\Token($token->token),
       $forever    );
        } catch(Exception $ex){
            return response()->json([
                'status' => 'Error',
                'message' => $ex,
            ]);
        }
        return Redirect('login');
    }
}
