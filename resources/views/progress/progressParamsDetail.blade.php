@extends('layouts.web')

@section('content')
    <!-- 你的HTML代码 -->
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>
<div class="left-sidebar">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget">
                <div class="widget-header">
                    <div class="title">
                        建筑系统
                    </div>
                    <span class="tools">
                      <a class="fs1" aria-hidden="true" data-icon="&#xe090;"></a>
                    </span>
                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">
                        <table class="layui-table layui-form">
                            <thead>
                            <tr>
                                <th>系统名称</th>
                                <th>工程名称</th>
                                <th>系统编码</th>
                                <th>系统状态</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    {{ $architect->system_name }}
                                </td>
                                <td>
                                    {{ $architect->engineering_name }}
                                </td>
                                <td>
                                    {{ $architect->system_code }}
                                </td>
                                <td>
                                    有效
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix">
                        </div>
                        <div style="color: orangered">状态无效后，将导致实施安装同步不显示？请谨慎操作</div>
                    </div>
                </div>
            </div>
            <div class="widget">
                <div class="widget-header">
                    <div class="title">
                        施工安装周期
                    </div>

                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">
                        <table class="layui-table layui-form">
                            <thead>
                            <tr>
                                <th style="width: 40%">名称</th>
                                <th style="width: 20%">是否同步施工</th>
                                <th style="width: 20%">排序</th>
                                <th style="width: 10%">状态</th>
                            </thead>
                            <tbody id="zixitong">
                            @foreach($paramsList as $v)
                                @if($v->status ==0)
                                    <tr style="background:#ea66a6">
                                @elseif($v->is_synchro ==1)
                                    <tr style="background: #0c9abb;">
                                @elseif($v->is_synchro ==2)
                                    <tr style="background:#df8505">
                                @else
                                    <tr>
                                @endif

                                <td>
                                    {{ $v->name }}
                                </td>
                                <td>
                                    @if($v->is_synchro ==1)
                                        不同步
                                    @else
                                       同步
                                    @endif
                                </td>
                                <td>
                                    {{ $v->sort }}
                                </td>
                                <td>
                                    @if($v->status ==1)
                                        有效
                                    @else
                                        无效
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
             <div class="layui-form-item" style="float: right;clear: left">
                <a href="javascript:history.go(-1)">
                <label for="L_repass" class="layui-form-label"></label>
                    <span class="btn btn-success" lay-filter="add" lay-submit="">返回/取消</span>
                </a>
            </div>

        </div>
    </div>
</div>


@endsection
