@extends('layouts.web')

@section('content')
    <!-- 你的HTML代码 -->
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>
    @if($status == 2)
    <div class="alert alert-block alert-error fade in">
        <button data-dismiss="alert" class="close" type="button">
            ×
        </button>
        <h4 class="alert-heading">
           失败
        </h4>
        <p>
            {{$notice}}
        </p>
    </div>
    @elseif($status ==1)
    <div class="alert alert-block alert-success fade in">
        <button data-dismiss="alert" class="close" type="button">
            ×
        </button>
        <h4 class="alert-heading">
            成功!
        </h4>
        <p>
            {{$notice}}
        </p>
    </div>
    @endif

<div class="left-sidebar">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget">
                <div class="widget-header">
                    <div class="title">
                        用户列表<a id="dynamicTable"></a>
                        @if(in_array(100202,$pageauth))
                        <a class="btn btn-success" title="新增用户"   href="/admin/add_user_info/">
                            <i class="layui-icon">新增用户</i>
                        </a>
                        @endif
                    </div>
                    @if(in_array(100201,$pageauth))
                    <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                        <label>
                            <form class="form-search">
                                用户名:<input type="text" name="search" value="{{ $search }}" class="input-medium search-query">
                                <button type="submit" class="btn">搜索</button>
                            </form></label>
                    </div>
                    @endif

                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">
                        <table class="table table-condensed table-striped table-hover table-bordered pull-left" id="data-table">

                            <thead>
                            <tr>
                                <th>用户ID</th>
                                <th>用户名称</th>
                                <th>邮箱名</th>
                                <th>角色</th>
                                <th>部门</th>
                                <th>状态</th>
                                <th>创建时间</th>
                                <th>修改时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($data as $k =>$val)
                                @if($k%2 ==1)
                                    <tr class="gradeC">
                                @else
                                    <tr class="gradeA success">
                                @endif

                                    <td>
                                        {{ $val->id }}
                                    </td>
                                    <td>{{ $val->name }}</td>
                                    <td>{{ $val->email }}</td>
                                    <td>
                                        @if (isset($userRoleList[$val->id]))
                                            @foreach ($userRoleList[$val->id] as $v)
                                                {{ $v->role_name }} &nbsp;&nbsp;
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($department[$val->department_id]))
                                        {{$department[$val->department_id]}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($val->status ==0)
                                            待审核
                                        @elseif($val->status ==1)
                                            活跃
                                        @elseif($val->status ==-1)
                                            审核未通过
                                        @elseif($val->status ==-2)
                                            禁用
                                        @endif
                                    </td>

                                    <td>{{ $val->created_at }}</td>
                                    <td>{{ $val->updated_at }}</td>
                                    <td class="td-manage">
                                        @if(in_array(100203,$pageauth))
                                        <a class="btn btn-success" title="编辑用户"  href="/admin/edit_user_info/{{ $val->id }}">
                                            <i class="layui-icon">编辑用户</i>
                                        </a>
                                        @endif
                                        @if(in_array(100204,$pageauth))
                                            @if($val->status == 1)
                                        <a class="btn btn-warning" title="禁用用户"  href="/admin/ban_user/{{ $val->id }}">
                                            <i class="layui-icon">禁用用户</i>
                                        </a>
                                            @elseif($val->status ==0)
                                        <a class="btn btn-success" title="待审核用户" >
                                            <i class="layui-icon">待审核用户</i>
                                        </a>
                                            @else
                                        <a class="btn btn-success" title="开启用户"  href="/admin/no_ban_user/{{ $val->id }}">
                                            <i class="layui-icon">开启用户</i>
                                        </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                        @php
                        echo $page;
                        @endphp

                        <div class="clearfix">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
    <style>
        .dashboard-wrapper .left-sidebar {
            margin:auto;
        }
    </style>



@endsection
