<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = ['error_code'=> 1, 'error_msg'=> ''];

        if(isset($request->api_token)){

            $apitoken = $request->api_token;

            if($user = User::where('api_token',$apitoken)->first()){

                $response = ['error_msg' => 'Valid api token'];
                $request->user = $user;

                return $next($request);
            }else{

                $response = ['error_msg' => 'Invalid api token'];
            }

        }else{

            $response = ['error_code'=> 0, 'error_msg'=> 'api token not introduced'];
        }

        return response()->json($response);
    }
}
