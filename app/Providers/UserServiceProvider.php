<?php


namespace App\Providers;

use App\Common\Auth\UserTool;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * @example app('UserTool')->getUserInfo($uid) //获取用户信息
     *
     *
     *
     */



    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     * @return UserTool
     */
    public function register()
    {
        $this->app->singleton('UserTool', function ($app) {
            return $this->app->make(UserTool::class);
        });
    }
}