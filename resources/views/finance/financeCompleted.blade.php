@extends('layouts.web')

@section('content')
    <!-- 你的HTML代码 -->
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>

<div class="left-sidebar">
    <div class="row-fluid">
        <div class="metro-nav">
            @if(in_array(5001,$pageauth))
            <div class="metro-nav-block nav-block-blue">
                <a href="/finance/financeStart">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/1.png">洽谈工程</div>
                </a>
            </div>
            @endif
            @if(in_array(5002,$pageauth))
            <div class="metro-nav-block nav-block-green" >
                <a href="/finance/financeConduct">
                    <div class="fs1"  ><img src="/img/nav/2.png">实施工程</div>
                </a>
            </div>
            @endif
            @if(in_array(5003,$pageauth))
            <div class="metro-nav-block nav-block-yellow" style=" outline: 2px rgba(0, 0, 0, 0.75) solid;">
                <a href="/finance/financeCompleted">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/3.png">竣工工程</div>
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
                        竣工工程<a id="dynamicTable"></a>
                    </div>
                    <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                        <label>
                            <form class="form-search" action="/finance/financeCompleted" method="get">
                                项目名称:<input type="text" name="project_name" value="{{ $project_name }}" class="input-medium search-query">
                                项目地点:<input type="text" name="engin_name" value="{{ $engin_name }}" class="input-medium search-query">
                                预算负责人:<input type="text" name="project_leader" value="{{ $project_leader }}" class="input-medium search-query">

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
                                <th>合同编号</th>
                                <th>合同签署时间</th>
                                <th>项目负责人</th>
                                <th>原始合同总额（万元）</th>
                                <th>变更合同金额</th>
                                <th>付款进度状态</th>
                                <th>执行操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $k=>$val)
                                <tr>
                                    <td>{{ $k+1 }}</td>
                                    <td >{{ $val->project_name }}</td>
                                    <td>{{ $val->engineering_name }}</td>
                                    <td>{{$val->contract_code}}</td>
                                    <td>{{ $val->contract_at }}</td>
                                    <td>{{ $val->project_leader }}</td>
                                    <td>{{ $val->contract_amount }}</td>
                                    <td >{{$val->changed_contract_amount}}</td>
                                    <td>{{$val->status}}</td>
                                    <td class="td-manage">
                                        @if((in_array(500301,$pageauth)) || in_array(500301,$manageauth))
                                            <a title="详情" class="btn btn-success"   href="/finance/getFinanceInfo/{{$val->engin_id}}" >
                                                <i class="layui-icon">详情</i>
                                            </a>
                                        @endif
                                        @if((in_array(500301,$pageauth)) || in_array(500301,$manageauth))
                                            <a title="编辑" class="btn btn-success"   href="/finance/editFinanceInfo/{{$val->engin_id}}" >
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
                        <div class="clearfix">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



    <script>

    </script>

@endsection
