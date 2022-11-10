<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Api\LoginController;
use Validator;
use App\Http\Resources\UserResource;
use PHPOpenSourceSaver\JWTAuth\JWTAuth as JWTAuth;

class RegisterController extends Controller
{
    public function __construct()
    {
        //login class instance
        //$_Login =  new LoginController;
        //exempt register from requiring token
        $this->middleware('auth:api', ['except' => ['register']]);
    }



    public function Register(RegisterRequest $request)
    {
        if ($user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'password' => Hash::make($request->password),
              'role' => 'user'
          ])) {
            //request token resp[onse upon creation by a login validation
            $credentials = $request->only('email', 'password');
            //use |JWTAuth instead of Auth to return a token not a null token
            $token = \JWTAuth::attempt($credentials);
            return $this->sendResponse($user, $token);
        } else {
        }
    }

    //json responses
    public function sendResponse($user, $token)
    {
        $response = [
            'success' => true,
            'user' => new UserResource($user)
            ,
                'token' => $token,
                'type' => 'bearer',

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
