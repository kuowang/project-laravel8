@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="/layui/css/login.css">
    <link rel="stylesheet" href="/layui/css/amazeui.min.css">

    <div class="am-g myapp-login">
        <div class="myapp-login-bg">
            <div data-am-widget="tabs" class="am-tabs am-tabs-d2">
                <div class="am-tabs-bd">
                    <div data-tab-panel-0 class="am-tab-panel am-active">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                            {{ csrf_field() }}
                            <fieldset>
                                <div class="am-form-group">
                                    <input type="text" id="doc-vld-name" name="email" placeholder="请输入账号" class="am-form-field" value="{{ old('email') }}"  required autofocus/>
                                @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif

                                </div>
                                <div class="am-form-group">
                                    <input id="doc-vld-name" type="password" placeholder="请输入密码"  class=" am-form-field"  name="password" required>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <button class="myapp-login-button am-btn am-btn-secondary" type="submit"> 登      录</button>
                            </fieldset>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="footerinfo">
        {{ config('app.project_logo_foot_info') }}
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {{ config('app.project_logo_foot_telphone') }}
    </div>
    <script src="/js/app.js"></script>
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>

<script type="text/javascript">
    <?php
    $message =session('message');
    session()->flush();
    ?>
    @isset($message)
    @if($message != '')
      $().ready(function () {
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.msg('{{$message}}', {
                time: 2000, //20s后自动关闭
            });
        });
    });
    @endif
    @endisset



</script>




@endsection
