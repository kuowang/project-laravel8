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
            @if(in_array(1502,$pageauth))
            <div class="metro-nav-block nav-block-blue" style=" outline: 2px rgba(0, 0, 0, 0.75) solid;">
                <a href="/project/projectEnginStart">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/1.png">洽谈工程
                    @if(isset($engincount[0]))
                        ({{$engincount[0]}})
                    @else
                        (0)
                    @endif
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(1503,$pageauth))
            <div class="metro-nav-block nav-block-green">
                <a href="/project/projectEnginConduct">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/2.png">实施工程
                        @if(isset($engincount[1]))
                            ({{$engincount[1]}})
                        @else
                            (0)
                        @endif
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(1504,$pageauth))
            <div class="metro-nav-block nav-block-yellow">
                <a href="/project/projectEnginCompleted">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/3.png">竣工工程
                        @if(isset($engincount[2]))
                            ({{$engincount[2]}})
                        @else
                            (0)
                        @endif
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(1505,$pageauth))
            <div class="metro-nav-block nav-block-red">
                <a href="/project/projectEnginTermination">
                    <div class="fs1" aria-hidden="true" ><img src="/img/nav/4.png">终止工程
                        @if(isset($engincount[4]))
                            ({{$engincount[4]}})
                        @else
                            (0)
                        @endif
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
                        洽谈项目<a id="dynamicTable"></a>
                        @if(in_array(1501,$pageauth) || in_array(1501,$manageauth))
                        <a class="btn btn-success" title="新增项目"  href="/project/createdProject">
                            <i class="layui-icon">新增项目</i>
                        </a>
                        @endif
                    </div>
                    <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                        <label>
                            <form class="form-search" action="/project/projectStart" method="get">
                                项目名称:<input type="text" name="project_name" value="{{ $project_name }}" class="input-medium search-query">
                                项目地点:<input type="text" name="address" value="{{ $address }}" class="input-medium search-query">
                                项目负责人:<input type="text" name="project_leader" value="{{ $project_leader }}" class="input-medium search-query">
                                洽谈指数:<select name="success_level" id="stateAndCity" class="input-medium search-query" style="min-width: 80px;border-radius: 15px;color:red">
                                    <option value="0" ></option>
                                    @if($success_level ==1) <option value="1" selected="selected">★</option> @else <option value="1">★</option> @endif
                                    @if($success_level ==2) <option value="2" selected="selected">★★</option> @else <option value="2">★★</option> @endif
                                    @if($success_level ==3) <option value="3" selected="selected">★★★</option> @else <option value="3">★★★</option> @endif
                                    @if($success_level ==4) <option value="4" selected="selected">★★★★</option> @else <option value="4">★★★★</option> @endif
                                    @if($success_level ==5) <option value="5" selected="selected">★★★★★</option> @else <option value="5">★★★★★</option> @endif
                                </select>
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
                                <th>项目种类</th>
                                <th>工程数量</th>
                                <th>项目总面积</th>
                                <th>项目地址</th>
                                <th>项目总负责人</th>
                                <th>销售总负责人</th>
                                <th>设计总负责人</th>
                                <th>预算报价总负责人</th>
                                <th>合约总负责人</th>
                                <th>洽谈指数</th>
                                <th>创建时间</th>
                                <th>执行操作</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($data as $k=>$val)
                                <tr>
                                    <td>{{ $k+1 }}</td>
                                    <td >{{ $val->project_name }}</td>
                                    <td>{{$val->type}}</td>
                                    <td>{{$val->start_count}}</td>
                                    <td>{{$val->project_area}}</td>
                                    <td>{{ $val->address_detail }}</td>
                                    <td>{{ $val->project_leader }}</td>
                                    <td>{{ $val->sale_username }}</td>
                                    <td>{{ $val->design_username }}</td>
                                    <td>{{ $val->budget_username }}</td>
                                    <td>{{ $val->technical_username }}</td>

                                    <td style="color:red">{{ str_repeat('★', $val->success_level) }}</td>
                                    <td>{{ $val->created_at }}</td>
                                    <td class="td-manage">
                                        @if( (in_array(150201,$pageauth) && $val->created_uid == $uid ) || in_array(150201,$manageauth))
                                            <a title="查看详情" class="btn btn-info"  href="/project/projectDetail/{{ $val->id }}?type=start">
                                                <i class="layui-icon">详情</i>
                                            </a>
                                        @endif
                                        @if((in_array(150202,$pageauth) && $val->created_uid == $uid ) || in_array(150202,$manageauth))
                                            <a title="编辑" class="btn btn-success"  href="/project/editProject/{{ $val->id }}">
                                                <i class="layui-icon">编辑</i>
                                            </a>
                                        @endif
                                        @if((in_array(150203,$pageauth) && $val->created_uid == $uid ) || in_array(150203,$manageauth))
                                            <a title="项目信息管理" class="btn btn-success"  href="/project/projectEnginStart/{{ $val->id }}">
                                                <i class="layui-icon">项目信息管理</i>
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
        //一般直接写在一个js文件中




    </script>

@endsection
