<?php


namespace App\Common\Auth;


use Log;

use Illuminate\Support\Facades\DB;

class UserTool
{

    public function __construct()
    {

    }

    public function getUserInfo($uid){

        $user = DB::table('users')->where('id',$uid)
            ->first();
        log::info('用户信息',[$user]);
        return $user;
    }

}