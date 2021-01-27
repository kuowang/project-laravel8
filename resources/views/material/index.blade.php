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
                        部件集成列表<a id="dynamicTable"></a>

                    </div>
                    @if(in_array(4001,$pageauth) || in_array(4001,$manageauth))
                    <div class="dataTables_filter" id="data-table_filter" style="text-align: center;">
                        <label>
                            <form class="form-search" action="/material/materialList" method="get">
                                系统工程: &nbsp;<input type="text" name="system_name" value="{{ $system_name }}" class="input-medium search-query">
                                子系统工程: &nbsp;<input type="text" name="sub_system_name" value="{{ $sub_system_name }}" class="input-medium search-query">
                                材料名称: &nbsp;<input type="text" name="material_name" value="{{ $material_name }}" class="input-medium search-query">
                                <button type="submit" class="btn">搜索</button>
                            </form></label>
                    </div>
                    @endif

                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">
                        <table class="table table-condensed table-striped table-hover layui-table layui-form" id="data-table">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>材料名称</th>
                                <th>材料编码</th>
                                <th>规格特性</th>
                                <th>预算单位</th>
                                <th>采购单位</th>
                                <th>包装规格</th>
                                <th>包装要求</th>
                                <th style="width: 120px">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                            $syatem_code =0;
                            $syatem_sub_code =0;
                            @endphp
                            @foreach ($data as $k =>$val)
                                <!--补充系统工程-->
                                @if($val->system_code != $syatem_code  )
                                <tr class="gradeX  odd">
                                    <td colspan="10"><span class="btn btn-info" >{{$val->system_name}}</span></td>
                                </tr>
                                @php
                                $syatem_code = $val->system_code;
                                @endphp
                                @endif
                                <!--补充子系统工程-->
                                @if($val->sub_system_code != $syatem_sub_code  )
                                    <tr class="gradeA success odd" style="border-bottom: #1599b5 2px solid">
                                        <td></td>
                                        <td colspan="7">{{$val->sub_system_name}}</td>
                                        <td onclick="showcontent('{{$val->sub_system_code}}')"><span class="btn-default btn"  id="sub_code_{{$val->sub_system_code}}">展开材料</span></td>
                                    </tr>
                                    @php
                                        $syatem_sub_code = $val->sub_system_code;
                                    $i=1;
                                    @endphp
                                @else
                                    @php
                                    $i=1+$i;
                                    @endphp
                                @endif

                                <tr class="gradeC mater_sub_code_{{$syatem_sub_code}}" style="display: none">
                                        <td class="brand_id_{{ $val->id }}">
                                            {{ $i }}
                                        </td>
                                        <td class="brand_name_{{ $val->id }}">{{$val->material_name}}</td>
                                        <td >{{$val->material_code}}</td>
                                        <td >{{$val->characteristic}}</td>
                                        <td >{{$val->material_budget_unit}}</td>
                                        <td >{{$val->material_purchase_unit}}</td>
                                        <td >{{$val->pack_specification}}</td>
                                        <td >{{$val->pack_claim}}</td>

                                        <td class="td-manage ">
                                        @if( empty($val->material_created_uid ) || (in_array(4002,$pageauth) && $val->material_created_uid == $uid ) || in_array(4002,$manageauth))
                                            <a title="查看详情" class="btn btn-info"  href="/material/materialDetail/{{ $val->id }}">
                                                <i class="layui-icon">详情</i>
                                            </a>
                                        @endif
                                        @if(empty($val->material_created_uid ) ||(in_array(4003,$pageauth) && $val->material_created_uid == $uid ) || in_array(4003,$manageauth))
                                            <a title="编辑" class="btn btn-success"  href="/material/editMaterial/{{ $val->id }}">
                                                <i class="layui-icon">编辑</i>
                                            </a>
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

<script type="text/javascript">
    function deleteSupplierBrand(id){
        $.ajax({
            url:'/supplier/deleteSupplierBrand/'+id,
            type:'get',
            // contentType: 'application/json',
            success:function(data){
                console.log(data);
                if(data.status == 1){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.msg('删除成功');
                        setTimeout(function(){ location.href=location.href; }, 1000);
                    });
                }
            },
        });
    }

    function showcontent(sub_code) {
        $(".mater_sub_code_"+sub_code).toggle();
        str =$('#sub_code_'+sub_code).html();
        if(str =='展开材料'){
            $('#sub_code_'+sub_code).html('隐藏材料');
        }else{
            $('#sub_code_'+sub_code).html('展开材料');
        }
    }
</script>
@endsection
