<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
//slug to hide params in the url header
use Cviebrock\EloquentSluggable\Services\SlugService;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{
    public function __construct()
    {
        //exempt Login from requiring token
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        //use |JWTAuth instead of Auth to return a token not a null token
        $token =  \JWTAuth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        } else {
            $user = Auth::user();

            //check user type

            if (Gate::allows('isAdmin', $user)) {
                return $this->sendResponse($user, $token, 'admin');
            } elseif (Gate::allows('isManager', $user)) {
                return  $this->sendResponse($user, $token, 'manager');
            } elseif (Gate::allows('isUser', $user)) {
                return $this->sendResponse($user, $token, 'standard user');
            }
        }
    }




    //use return to get the response
    //json responses
    public function sendResponse($user, $token, $User_Type)
    {
        $response = [
           'success' => true,
           'user' => new UserResource($user),
           'token type' => 'bearer',
           'token' => $token,

        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
