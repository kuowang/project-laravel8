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
                @if(in_array(200201,$pageauth))
                    <div class="metro-nav-block nav-block-blue" >
                        <a href="/offer/offerStart/{{$id}}">
                            <div class="fs1" aria-hidden="true" ><img src="/img/nav/1.png">洽谈工程
                                @if(isset($project))({{$project->start_count}}) @endif
                            </div>
                        </a>
                    </div>
                @endif
                @if(in_array(200202,$pageauth))
                    <div class="metro-nav-block nav-block-green">
                        <a href="/offer/offerConduct/{{$id}}">
                            <div class="fs1"  ><img src="/img/nav/2.png">实施工程
                                @if(isset($project))({{$project->conduct_count}})@endif
                            </div>
                        </a>
                    </div>
                @endif
                @if(in_array(200203,$pageauth))
                    <div class="metro-nav-block nav-block-yellow" style=" outline: 2px rgba(0, 0, 0, 0.75) solid;">
                        <a href="/offer/offerCompleted/{{$id}}">
                            <div class="fs1" aria-hidden="true"  ><img src="/img/nav/3.png">竣工工程
                                @if(isset($project))({{$project->completed_count}})@endif
                            </div>
                        </a>
                    </div>
                @endif
                @if(in_array(200204,$pageauth))
                    <div class="metro-nav-block nav-block-red">
                        <a href="/offer/offerTermination/{{$id}}">
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
                            竣工工程<a id="dynamicTable"></a>
                        </div>
                        <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                            <label>
                                @if(isset($project))
                                    {{$project->project_name}}
                                @else
                                    <form class="form-search" action="/offer/offerCompleted" method="get">
                                        项目名称:<input type="text" name="project_name" value="{{ $project_name }}" class="input-medium search-query">
                                        项目地点:<input type="text" name="address" value="{{ $address }}" class="input-medium search-query">
                                        预算负责人:<input type="text" name="budget_username" value="{{ $budget_username }}" class="input-medium search-query">
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
                                    <th>建筑数量(栋)</th>
                                    <th>预算金额(万元)(单栋)</th>
                                    <th>报价金额(万元)(单栋)</th>
                                    <th>毛利润(万元)(单栋)</th>
                                    <th>毛利率(%)</th>
                                    <th>报价金额合计(万元)</th>
                                    <th>毛利润合计(万元)</th>
                                    <th>预算负责人</th>
                                    <th>报价状态</th>
                                    <th>报价审核状态</th>
                                    <th>操作</th>
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

                                        <td>{{ empty($val->total_budget_price)?'':round($val->total_budget_price/10000,2) }}</td>
                                        <td>{{ empty($val->total_offer_price)?'':round($val->total_offer_price/10000,2) }}</td>
                                        <td>{{ empty($val->total_offer_price)?'':round($val->total_offer_price/10000 - $val->total_budget_price/10000,2)}}</td>
                                        <td >{{empty($val->total_offer_price)?'':round(($val->total_offer_price -$val->total_budget_price)/$val->total_offer_price *100 ,2)}}</td>
                                        <td>{{ empty($val->total_offer_price)?'':round($val->total_offer_price/10000*$val->build_number,2) }}</td>
                                        <td>{{ empty($val->total_offer_price)?'':round($val->total_offer_price/10000 - $val->total_budget_price/10000,2)*$val->build_number}}</td>

                                        <td>{{ $val->budget_username }}</td>
                                        @if(empty($val->offer_order_number))
                                            <td><span class="btn btn-danger">未完成</span></td>
                                        @else
                                            <td><span class="btn btn-info">已完成</span></td>
                                        @endif
                                        @if($val->offer_status ==1)
                                            <td><span class="btn btn-info">已审核</span></td>
                                        @elseif($val->offer_status ==0)
                                            <td><span class="btn btn-success">待审核</span></td>
                                        @elseif($val->offer_status == -1)
                                            <td><span class="btn btn-danger">已取消</span></td>
                                        @endif
                                        <td class="td-manage">
                                            @if( (in_array(20020301,$pageauth) && $val->budget_uid == $uid ) || in_array(200207,$manageauth))
                                                @if(!empty($val->offer_order_number))
                                                <a title="查看详情" class="btn btn-info"  href="/offer/offerCompletedDetail/{{ $val->engin_id }}">
                                                    <i class="layui-icon">详情</i>
                                                </a>
                                                <a title="导出"  target="_blank" class="btn btn-success"  href="/offer/offerCompletedDetail/{{ $val->engin_id }}?download=1" >
                                                    <i class="layui-icon">导出</i>
                                                </a>
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
                            <div class="clearfix"></div>
                            <hr>
                            <div style="margin: 10px">
                                <div>1、毛利润=报价金额 - 预算金额</div>
                                <div>2、毛利率=(报价金额 - 预算金额)\报价金额 * 100</div>
                                <div>3、预算金额、报价金额、毛利额均是单栋建筑的金额</div>

                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection
