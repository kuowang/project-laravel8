<?php
/**
 * Created by PhpStorm.
 * User: bsu0921360
 * Date: 2016/12/7
 * Time: 14:55
 */

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Contracts\Auth\Factory as Auth;
use Log;

class HsActiveStatus
{
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    public function handle($request, Closure $next) {
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

        if(isset($this->auth) && isset($this->auth->user()->id)){
            $phpsession =$request->session()->getId();
            $sessionid =$this->auth->user()->sessionid;
            log::info('session',[$phpsession,$sessionid]);
            if($phpsession != $sessionid){
                Log::info('用户在其他位置登录',[$this->auth->user(),$request->header()]);
                $request->session()->flush();
                session(['message' => '您在其他位置已经登录，请重新登录']);
                return redirect('/login');
            }
        }
        return $next($request);
    }
}
