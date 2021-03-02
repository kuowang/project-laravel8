<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserTool extends  Facade
{
    /**
     * eg.
     *      UserTool::getUserInfo($uid) //获取用户信息
     *
     *
     *
     *
     *
     */





    /**
     * @return string
     *
     * UserTool 为App\Providers\UserServiceProvider ::register() 中的单例名称
     *
     */
    protected static function getFacadeAccessor()
    {
        return 'UserTool';
    }
}