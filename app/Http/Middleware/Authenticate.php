<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\DB;
use Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }else {
            $ip = '0.0.0.0';
        }
        if(isset($this->auth) && isset($this->auth->user()->id)){
            $uid =      $this->auth->user()->id;
        }else{
            $uid=0;
        }
        //记录操作日志
        if($uid ==0){
            Log::info('Request ', ['cookie' => $request->cookie(),'header'=>$request->header()]);
        }
        DB::table('system_operation_log')->insert([
            'uid'=>$uid,
            'url'=>$request->decodedPath(),
            'user_agent'=>$request->header('User-Agent'),
            'ip'=>$ip,
            'param'=>json_encode($request->all()),
            'created_at'=>date('Y-m-d H:i:s')
        ]);

        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
