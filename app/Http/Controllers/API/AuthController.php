<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Http;



class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/register",
     *      operationId="register",
     *      tags={"register"},
     *      summary="register the record",
     *      security= {{"Bearer_auth": ""}},
     *      description="to register",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"name","email","phone","password"},
     *             @OA\Property(property="name", type="string", format="string", example="test5"),
     *             @OA\Property(property="email", type="string", format="string", example="test5@gmail.com"),
     *             @OA\Property(property="phone", type="string", format="string", example="9845345633"),
     *             @OA\Property(property="password", type="string", format="string", example="test5"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              description="success message",
     *              example={
     *                  "status_code"=200,
     *                  "message"="register has been created successfully.",
     *                  "payload"={}
     *              }
     *          )
     *       ),
     *    )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Hash::make($request->password)
        ]);

        $token = $user->createToken('Token')->accessToken;
        return response()->json(['token' => $token, 'user'=>$user],200);

    }

    /**
     * @OA\Post(
     *      path="/login",
     *      operationId="login",
     *      tags={"login"},
     *      summary="login",
     *      security= {{"Bearer_auth": ""}},
     *      description="to login",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="string", example="xyz@gmail.com"),
     *             @OA\Property(property="password", type="string", format="string", example="xyz"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              description="success message",
     *              example={
     *                  "status_code"=200,
     *                  "message"="login has been created successfully.",
     *                  "payload"={}
     *              }
     *          )
     *       ),
     *    )
     */

    public function login(Request $request)
    {
        $response = Http::asForm()->post('http://127.0.0.1:8080/oauth/token', [
            'grant_type' => 'password',
            'client_id' => 3,
            'client_secret' => 'yrQMZyV6OQVfbaphPpRhtMKXSVEcBtqGHrtSw3LO',
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '',
        ]);
         
        return $response->json();

    }

    /**
     * @OA\Post(
     *     path="/refresh-token",
     *     tags={"refresh token"},
     *     summary="refresh token",
     *     security= {{"Bearer_auth": ""}},
     *     description="Get refresh token",
     *     operationId="refreshToken",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"refresh"},
     *             @OA\Property(property="refresh", type="string", format="string", example="..."),
     *          ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function refreshToken(Request $request)
    {
        $response = Http::asForm()->post('http://127.0.0.1:8080/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh,
            'client_id' => 3,
            'client_secret' => 'yrQMZyV6OQVfbaphPpRhtMKXSVEcBtqGHrtSw3LO',
            'scope' => '',
        ]);
         
        return $response->json();
    }

    /**
     * @OA\Get(
     *     path="/get-user",
     *     tags={"get user"},
     *     summary="Get the user",
     *     security= {{"Bearer_auth": ""}},
     *     description="Get the user",
     *     operationId="userInfo",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function userInfo()
    {
        $user = auth()->user();
        return response()->json(['user' => $user],200);
    }

    

}
