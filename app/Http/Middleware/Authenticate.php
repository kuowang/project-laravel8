<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{

    public function handle($request, Closure $next) {

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }else {
            $ip = '0.0.0.0';
        }
        if(isset($this->auth) && isset(Auth::user()->id)){
            $uid = Auth::user()->id;
        }else{
            $uid= 0;
        }
        //记录操作日志
        if($uid == 0){
            Log::info('Request ', ['cookie' => $request->cookie(),'header'=>$request->header(),'params'=>$request->all()]);
        }

        DB::table('system_operation_log')->insert([
            'uid'=>$uid,
            'url'=>$request->decodedPath(),
            'user_agent'=>$request->header('User-Agent'),
            'ip'=>$ip,
            'param'=>json_encode($request->all()),
            'created_at'=>date('Y-m-d H:i:s')
        ]);

        if(Auth::user()){ //补充用户权限问题
            if( Auth::user()->status == 0 ){
                session()->flush();
                session(['message' => '待审核用户，不能访问']);
                return redirect('/login');
            }elseif( Auth::user()->status == -1){
                session()->flush();
                session(['message' => '用户申请被禁止，不能访问']);
                return redirect('/login');
            }elseif( Auth::user()->status == -2){
                session()->flush();
                session(['message' => '用户被禁止，不能访问']);
                return redirect('/login');
            }
        }

        return $next($request);
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {


    }
}
