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
        <div class="metro-nav">
            @if(in_array(200101,$pageauth))
            <div class="metro-nav-block nav-block-blue" style=" outline: 2px rgba(0, 0, 0, 0.75) solid;">
                <a href="/budget/budgetStart/{{$id}}">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/1.png">洽谈工程
                        @if(isset($project))({{$project->start_count}}) @endif
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(200102,$pageauth))
            <div class="metro-nav-block nav-block-green">
                <a href="/budget/budgetConduct/{{$id}}">
                    <div class="fs1"  ><img src="/img/nav/2.png">实施工程
                        @if(isset($project))({{$project->conduct_count}})@endif
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(200103,$pageauth))
            <div class="metro-nav-block nav-block-yellow">
                <a href="/budget/budgetCompleted/{{$id}}">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/3.png">竣工工程
                        @if(isset($project))({{$project->completed_count}})@endif
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(200104,$pageauth))
            <div class="metro-nav-block nav-block-red">
                <a href="/budget/budgetTermination/{{$id}}">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/4.png">终止工程
                        @if(isset($project))({{$project->termination_count}})@endif
                    </div>
                </a>
            </div>
            @endif
        </div>

    </div>


    <div class="row-fluid">
        <div class="span12">
            <div class="widget">
                <div class="widget-header">
                    <div class="title">
                        洽谈工程<a id="dynamicTable"></a>
                    </div>
                    <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                        <label>
                            @if(isset($project))
                                {{$project->project_name}}
                            @else
                                <form class="form-search" action="/budget/budgetStart" method="get">
                                    项目名称:<input type="text" name="project_name" value="{{ $project_name }}" class="input-medium search-query">
                                    项目地点:<input type="text" name="address" value="{{ $address }}" class="input-medium search-query">
                                    预算总负责人:<input type="text" name="budget_username" value="{{ $budget_username }}" class="input-medium search-query">
                                    <button type="submit" class="btn">搜索</button>
                                </form>
                            @endif

                        </label>
                    </div>
                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">

                        <table class="layui-table layui-form">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>项目名称</th>
                                <th>工程名称</th>
                                <th>建筑面积(m²)</th>
                                <th>建筑数量</th>
                                <th>预算金额(元)(单栋)</th>
                                <th>预算金额(元)(合计)</th>
                                <th>预算单编号</th>
                                <th>预算负责人</th>
                                <th>预算状态</th>
                                <th>审核状态</th>
                                @if(in_array(200103,$manageauth))
                                    <th>审核操作</th>
                                @endif

                                <th>执行操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $k=>$val)
                                <tr>
                                    <td>{{ $k+1 }}</td>
                                    <td >{{ $val->project_name }}</td>
                                    <td>{{ $val->engineering_name }}</td>
                                    <td>{{ $val->build_area }}</td>
                                    <td>{{ $val->build_number }}</td>
                                    <td>{{ round($val->total_budget_price,2) }}</td>
                                    <td>{{ round($val->total_budget_price*$val->build_number,2) }}</td>

                                    <td>{{ $val->budget_order_number }}</td>
                                    <td>{{ $val->budget_username }}</td>
                                    @if(empty($val->budget_order_number))
                                        <td><span class="btn btn-danger">未完成</span></td>
                                    @else
                                        <td><span class="btn btn-info">已完成</span></td>
                                    @endif
                                    @if($val->budget_status ==1)
                                        <td><span class="btn btn-info">已审核</span></td>
                                    @else
                                        <td><span class="btn btn-danger">待审核</span></td>
                                    @endif
                                    @if(in_array(200103,$manageauth))
                                    <td>
                                    @if(!empty($val->budget_order_number))
                                        @if($val->budget_status ==1)
                                            <div class="btn btn-warning" onclick="emainStatus({{$val->budget_id}},0)">取消审核</div>
                                        @else
                                            <div class="btn btn-success" onclick="emainStatus({{$val->budget_id}},1)">审核通过</div>
                                        @endif
                                    @endif
                                    </td>
                                    @endif
                                    <td class="td-manage">

                                        @if( (in_array(20010102,$pageauth) && $val->budget_uid == $uid ) || in_array(200101,$manageauth))
                                            @if(!empty($val->budget_id))
                                            <a title="查看详情" class="btn btn-info"  href="/budget/budgetStartDetail/{{ $val->engin_id }}">
                                                <i class="layui-icon">详情</i>
                                            </a>
                                            <a title="导出"  target="_blank" class="btn btn-success"  target="_blank" href="/budget/budgetStartDetail/{{ $val->engin_id }}?download=1" onclick="return checkStatus({{$val->is_conf_architectural}})">
                                                <i class="layui-icon">导出</i>
                                            </a>
                                            @endif
                                        @endif
                                        @if((in_array(20010101,$pageauth) && $val->budget_uid == $uid ) || in_array(200102,$manageauth))
                                            @if($val->budget_status != 1)
                                                @if(empty($val->budget_id))
                                                    <a title="创建" class="btn btn-success"  href="/budget/editStartBudget/{{ $val->engin_id }}" onclick="return checkStatus({{$val->is_conf_architectural}},{{$val->is_conf_param}})">
                                                        <i class="layui-icon">创建</i>
                                                    </a>
                                                @else
                                                    <a title="编辑" class="btn btn-success"  href="/budget/editStartBudget/{{ $val->engin_id }}" >
                                                        <i class="layui-icon">编辑</i>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                            <div>
                                @php
                                echo $page;
                                @endphp

                            </div>
                        <div class="clearfix">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


    <script>
        //一般直接写在一个js文件中

    function checkStatus(status ,paramstatus) {
        if(status ==0 || paramstatus ==0){
            showMsg('请先进行项目设计参数和工况配置后才能创建预算')
            return false;
        }
        return true;
    }
    //审核状态修改
    function emainStatus(id,status) {

        $.ajax({
            url:'/budget/examineStartBudget/'+id+'/'+status,
            type:'post',
            // contentType: 'application/json',
            success:function(data){
                console.log(data);
                if(data.status ==1){
                    showMsg('审核更改成功');
                }else{
                    showMsg(data.info)
                }
                setTimeout(function(){ location.href=location.href; }, 1000);
            },
        });
    }

   function showMsg(str){
       layui.use('layer', function(){
           var layer = layui.layer;
           layer.msg(str);
       });
   }
    </script>

@endsection
