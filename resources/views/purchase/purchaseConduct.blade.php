@extends('layouts.web')

@section('content')
    <style type="text/css">
        th{
            text-align: center;
        }

    </style>
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

                @if(in_array(200102,$pageauth))
                    <div class="metro-nav-block nav-block-green" style=" outline: 2px rgba(0, 0, 0, 0.75) solid;">
                        <a href="/purchase/purchaseConduct/{{$id}}">
                            <div class="fs1"  ><img src="/img/nav/2.png">实施工程
                                @if(isset($project)) ({{$project->conduct_count}})@endif
                            </div>
                        </a>
                    </div>
                @endif
                @if(in_array(200103,$pageauth))
                    <div class="metro-nav-block nav-block-yellow">
                        <a href="/purchase/purchaseCompleted/{{$id}}">
                            <div class="fs1" aria-hidden="true" ><img src="/img/nav/3.png">竣工工程
                                @if(isset($project)) ({{$project->completed_count}})@endif
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
                            实施工程<a id="dynamicTable"></a>
                        </div>
                        <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                            <label>
                                <form class="form-search" action="/purchase/purchaseConduct" method="get">
                                    项目名称:<input type="text" name="project_name" value="{{ $project_name }}" class="input-medium search-query">
                                    工程名称:<input type="text" name="engineering_name" value="{{ $engineering_name }}" class="input-medium search-query">
                                    采购负责人:<input type="text" name="purchase_username" value="{{ $purchase_username }}" class="input-medium search-query">
                                    <button type="submit" class="btn">搜索</button>
                                </form>
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
                                    <th>建筑数量(栋)</th>
                                    <th>合同编号</th>
                                    <th>采购负责人</th>
                                    <th>采购状态</th>
                                    <th>物流状态</th>
                                    <th colspan="3">采购管理</th>
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
                                        
                                        <td>{{ $val->contract_code }}</td>
                                        <td>{{$val->purchase_username}}</td>
                                        <td>
                                            @if($val->purchase_status == 0)<span class="btn btn-danger">未采购</span>
                                            @elseif($val->purchase_status == 1)<span class="btn btn-success">采购中</span>
                                            @elseif($val->purchase_status == 2)<span class="btn btn-info">采购完</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($val->logistics_status == 0)<span class="btn btn-danger">未发货</span>
                                            @elseif($val->logistics_status == 1)<span class="btn btn-success">运输中</span>
                                            @elseif($val->logistics_status == 2)<span class="btn btn-info">已到达</span>
                                            @endif
                                        </td>
                                        <td>@if($val->budget_id != 0 && ((in_array(250102,$pageauth) && $val->purchase_uid == $uid ) || in_array(250102,$manageauth)))
                                                <a href="/purchase/purchaseBatchManage/{{$val->engin_id}}">
                                                    <div class="btn btn-success"> 批次管理 </div>
                                                </a>
                                              @else
                                                批次管理
                                            @endif</td>
                                        <td>
                                            @if($val->batch_status ==1 && ((in_array(250103,$pageauth) && $val->purchase_uid == $uid ) || in_array(250103,$manageauth)))
                                                <a href="/purchase/purchaseOrderManage/{{$val->engin_id}}">
                                                <div class="btn btn-success">订单管理</div>
                                                </a>
                                            @else
                                                订单管理
                                            @endif

                                        </td>
                                        <td>
                                            @if($val->batch_status ==1 && ((in_array(250104,$pageauth) && $val->purchase_uid == $uid ) || in_array(250104,$manageauth)))
                                                <a href="/purchase/purchaseLogisticsManage/{{$val->engin_id}}">
                                                <div class="btn btn-success">物流管理</div>
                                                </a>
                                            @else
                                                物流管理
                                            @endif
                                        </td>

                                        <td class="td-manage">
                                            @if((in_array(250101,$pageauth) && $val->purchase_uid == $uid ) || in_array(250101,$manageauth))
                                                <a title="编辑" class="btn btn-success"  href="/purchase/editPurchase/{{ $val->engin_id }}" onclick="return checkStatus({{$val->budget_id}})">
                                                    <i class="layui-icon">编辑</i>
                                                </a>
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
                            <div class="clearfix"></div>
                            <div>
                                <div>注：1.采购负责人由采购部门经理指派</div>
                                <div>2.采购负责人只能操作自己负责的项目</div>
                                <div>3.工程进入实施状态并且预算单已完成审核才能进行采购批次设置</div>
                                <div>4.工程完成采购批次设置，才能进行订单管理和物流管理</div>
                                <div>5.采购状态和物流状态是由执行操作中编辑来完成，与订单管理和批次管理没有关系</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script>
        //一般直接写在一个js文件中
        function checkStatus(status) {
            if(status ==0 || status ==''){
                showMsg('需要先填写预算单才能编辑')
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
