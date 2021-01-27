@extends('layouts.web')

@section('content')
    <!-- 你的HTML代码 -->
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>
<div class="left-sidebar dashboard-wrapper">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget">
                <form class="layui-form"  action="{{ url('/admin/post_role_authority/'.$id) }}" method="post">

                <div class="widget-header">
                    <div class="title">
                        系统进入权限<a id="dynamicTable"></a>
                    </div>

                    <span class="tools">
                      <a class="fs1" aria-hidden="true" data-icon="&#xe090;"></a>
                    </span>
                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">


                            <table class="layui-table layui-form table table-condensed table-striped table-hover table-bordered pull-left">
                                <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>ID</th>
                                    <th>父ID</th>
                                    <th>是否导航显示</th>
                                    <th>创建时间</th>
                                    <th>操作</th></tr>
                                </thead>
                                <tbody>

                                @foreach ($data as $val)
                                    @if($val->level ==1)
                                    <tr class="type_show_{{ $val->auth_id }}">
                                    @else
                                    <tr class="type_show_{{ $val->auth_id }}" style="display: none">
                                    @endif
                                        <td>
                                            @php
                                                echo  str_repeat('&nbsp;', 6*($val->level -1));
                                            @endphp
                                            @if (in_array($val->auth_id,$auth))
                                                <input type="checkbox" name="auth_id[]" class="auth_{{ $val->auth_id }}" value="{{ $val->auth_id }}" checked="checked" lay-filter="checkall" lay-skin="primary">
                                            @else
                                                <input type="checkbox" name="auth_id[]" class="auth_{{ $val->auth_id }}" value="{{ $val->auth_id }}" lay-filter="checkall" lay-skin="primary">
                                            @endif

                                            <div class="layui-unselect layui-form-checkbox" lay-skin="primary">
                                                <i class="layui-icon layui-icon-ok"></i>
                                            </div>
                                        {{ $val->name }}
                                        <td>
                                            {{ $val->auth_id }}
                                        </td>

                                        <td>{{ $val->parent_id }}</td>

                                        <td>
                                            @if ($val->is_show == 1)
                                                是
                                            @else
                                                否
                                            @endif
                                        </td>
                                        <td>{{ $val->created_at }}</td>
                                        <td onclick="show({{ $val->auth_id }})" style="text-align: center;" >
                                            @if($val->is_leaf == 0 && $val->level ==1)
                                            <div class="yincang_{{ $val->auth_id }} btn btn-success">显示</div>
                                            @elseif($val->is_leaf == 0)
                                            <div class="yincang_{{ $val->auth_id }} btn btn-success">隐藏</div>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach


                                </tbody>
                            </table>

                        <div class="clearfix">
                        </div>
                    </div>
                </div>
                    <div class="widget-header">
                        <div class="title">
                            系统管理权限
                            <span class="mini-title">
                                        查看详情信息
                                    </span>
                        </div>
                        <span class="tools">
                                    <a class="fs1" aria-hidden="true" data-icon=""></a>
                        </span>
                    </div>

                    <div class="widget-body" style="margin-left:15px ">
                        @foreach($managelist as $list)
                            @if($list->level ==1)
                                <div class="manage_show_{{ $list->manage_id }}">
                            @else
                                <div class="manage_show_{{ $list->manage_id }}" style="display: none">
                            @endif


                            @php
                                echo  str_repeat('&nbsp;', 6*($list->level -1));
                            @endphp
                        @if (in_array($list->manage_id,$rolemanagelist))
                            <input type="checkbox" name="manage_id[]" class="rolemanage" id="manage_{{ $list->manage_id }}" value="{{ $list->manage_id }}" checked="checked" lay-filter="checkall" lay-skin="primary">
                        @else
                            <input type="checkbox" name="manage_id[]" class="rolemanage" id="manage_{{ $list->manage_id }}" value="{{ $list->manage_id }}" lay-filter="checkall" lay-skin="primary">
                        @endif
                            {{ $list->name }}
                            <span  style="float:right;    margin-right: 30px;" onclick="manage_show({{ $list->manage_id }})">
                                @if($list->is_leaf == 0 && $list->level ==1)
                                    <div class="manage_yincang_{{ $list->manage_id }} btn btn-success">显示</div>
                                @elseif($list->is_leaf == 0)
                                    <div class="manage_yincang_{{ $list->manage_id }} btn btn-success">隐藏</div>
                                @endif
                            </span>
                            <hr>
                            </div>
                        @endforeach

                    </div>

                    <div class="clearfix">
                    </div>
                    <div class="layui-form-item" style="margin: 10px">
                        <input type="submit" class="layui-btn" lay-filter="add" lay-submit="" value="提交" style="width: 100px">
                    </div>

                    <div class="clearfix">
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
    <style>
        .dashboard-wrapper .left-sidebar {
            margin:auto;
        }
    </style>

    <script>
        layui.use(['laydate','form'], function(){
            var laydate = layui.laydate;
            var  form = layui.form;
            // 监听全选
            form.on('checkbox(checkall)', function(data){
                console.log($(this).val());
                if($(this).prop('class') == 'rolemanage'){
                    //alert($(this).val());
                    id =$(this).val();
                    //选择子集默认选中父级
                    len =id.length;
                    if(len > 2){
                        str=id.substring(0,2);
                        $("input[id='manage_"+str+"']").prop('checked',true);
                    }
                    if(len > 4){
                        str=id.substring(0,4);
                        $("input[id='manage_"+str+"']").prop('checked',true);
                    }
                    if(len > 6){
                        str=id.substring(0,6);
                        $("input[id='manage_"+str+"']").prop('checked',true);
                    }

                    if(data.elem.checked){
                        //$('tbody input').prop('checked',true);
                        $("input[id^='manage_"+id+"']").prop('checked',true);
                    }else{
                        //$('tbody input').prop('checked',false);
                        $("input[id^='manage_"+id+"']").prop('checked',false);
                    }
                    form.render('checkbox');

                }else{
                    //alert($(this).val());
                    id =$(this).val();
                    //选择子集默认选中父级
                    len =id.length;
                    if(len > 2){
                        str=id.substring(0,2);
                        $("input[class='auth_"+str+"']").prop('checked',true);
                    }
                    if(len > 4){
                        str=id.substring(0,4);
                        $("input[class='auth_"+str+"']").prop('checked',true);
                    }
                    if(len > 6){
                        str=id.substring(0,6);
                        $("input[class='auth_"+str+"']").prop('checked',true);
                    }
                    if(len > 8){
                        str=id.substring(0,8);
                        $("input[class='auth_"+str+"']").prop('checked',true);
                    }
                    if(data.elem.checked){
                        //$('tbody input').prop('checked',true);
                        $("input[class^='auth_"+id+"']").prop('checked',true);
                    }else{
                        //$('tbody input').prop('checked',false);
                        $("input[class^='auth_"+id+"']").prop('checked',false);
                    }
                    form.render('checkbox');
                }
            });
        });

        function  show(id){
            htm =$('.yincang_'+id).html();
            if(htm == '隐藏'){
                $("tr[class^='type_show_"+id+"']").hide();
                $('.yincang_'+id).html('显示');
                $("tr[class='type_show_"+id+"']").show();
            }else{
                $("tr[class^='type_show_"+id+"']").show();
                $('.yincang_'+id).html('隐藏');
            }
        }
        function  manage_show(id){
            htmd =$('.manage_yincang_'+id).html();
            if(htmd == '隐藏'){
                $("div[class^='manage_show_"+id+"']").hide();
                $('.manage_yincang_'+id).html('显示');
                $("div[class='manage_show_"+id+"']").show();
            }else{
                $("div[class^='manage_show_"+id+"']").show();
                $('.manage_yincang_'+id).html('隐藏');
            }
        }


    </script>

@endsection
