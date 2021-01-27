@extends('layouts.web')

@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>

    <style type="text/css">
        .layui-form input[type=checkbox], .layui-form input[type=radio], .layui-form select {
            display: inline;
        }
        .pro-title{
            background: #e6e6e6;
        }
        .layui-table td, .layui-table th {
            border: solid 1px #ccc;
        }
        select{
            width: auto;
        }
        .radio, .checkbox {
            min-height: 20px;
            float: left;
            padding:0 20px;
        }
    </style>
<div class="left-sidebar">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget">
                <div class="widget-header" style="text-align: center">
                    <div  style="font-size: 16px;" >
                        <b>{{$project->project_name}}</b>（工程报价清单）
                    </div>
                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">
                        <table class="layui-table layui-form">
                            <thead>
                            <tr>
                                <th colspan="6"><span class="btn btn-info">项目概况</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td  class="pro-title">项目名称</td>
                                <td  >{{$project->project_name}}</td>
                                <td  class="pro-title">项目地点</td>
                                <td >{{$project->province}}{{$project->city}}{{$project->county}}{{$project->address_detail}}{{$project->foreign_address}}
                                </td>
                                <td  class="pro-title">工程名称</td>
                                <td  >{{$engineering->engineering_name}}</td>
                            </tr>

                            <tr>
                                <td class="pro-title">建筑面积(m²)</td>
                                <td >{{$engineering->build_area}}</td>
                                <td class="pro-title">建筑楼层(层数)</td>
                                <td >{{$engineering->build_floor}}</td>
                                <td class="pro-title">建筑高度(m)</td>
                                <td >{{$engineering->build_height}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title">报价日期</td>
                                <td >{{isset($offer->quotation_date)?$offer->quotation_date:''}}</td>
                                <td class="pro-title">报价有效期限(天)</td>
                                <td >{{isset($offer->quotation_limit_day)?$offer->quotation_limit_day:''}}</td>
                                <td class="pro-title">建筑数量(栋)</td>
                                <td>{{$engineering->build_number}}</td>                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        <table class="layui-table layui-form">
                            <thead>
                            <tr>
                                <th colspan="6"><span class="btn btn-info">建筑设计指标</span></th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td class="pro-title">建筑使用寿命(年)</td>
                                <td >{{isset($param->use_time)?$param->use_time:''}}</td>
                                <td class="pro-title">抗震设防烈度(度)</td>
                                <td >{{isset($param->seismic_grade)?$param->seismic_grade:''}}</td>
                                <td class="pro-title">屋面防水等级</td>
                                <td >{{isset($param->waterproof_grade)?$param->waterproof_grade:''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title">建筑耐火等级</td>
                                <td >{{isset($param->waterproof_grade)?$param->waterproof_grade:''}}</td>
                                <td class="pro-title">建筑隔声等级</td>
                                <td >{{isset($param->waterproof_grade)?$param->waterproof_grade:''}}</td>
                                <td class="pro-title">建筑节能标准(%)</td>
                                <td >{{isset($param->waterproof_grade)?$param->waterproof_grade:''}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        <table class="layui-table layui-form">
                            <thead>
                            <tr>
                                <th colspan="4"><span class="btn btn-info">建筑荷载设计指标</span></th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td class="pro-title">设计基本风压(kN/m²)</td>
                                <td >{{isset($param->basic_wind_pressure)?$param->basic_wind_pressure:''}}</td>
                                <td class="pro-title">设计基本雪压(kN/m²)</td>
                                <td >{{isset($param->basic_snow_pressure)?$param->basic_snow_pressure:''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title">屋面活载荷(kN/m²)</td>
                                <td >{{isset($param->roof_load)?$param->roof_load:''}}</td>
                                <td class="pro-title">楼面活载荷(kN/m²)</td>
                                <td >{{isset($param->floor_load)?$param->floor_load:''}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>

                        <table class="layui-table layui-form table111">
                            <thead>
                            <tr>
                                <th colspan="10"><span class="btn btn-info">报价清单列表</span></th>
                            </tr>
                            </thead>
                            <thead>
                            <tr>
                                <th style="width:5%">序号</th>
                                <th style="width:15%">材料名称</th>
                                <th style="width:15%">规格特性要求</th>
                                <th style="width:6%">报价单位</th>
                                <th style="width:9%">工程量(图纸)</th>
                                <th style="width:7%">损耗(%)</th>
                                <th style="width:8%">工程量(实际)</th>
                                <th style="width:14%">品牌</th>
                                <th style="width:8%">单价</th>
                                <th style="width:6%">合计</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                            $system_code ='';
                            $xuhao=0;
                            @endphp
                            @foreach($engin_system as $v)
                                @if($system_code != $v->system_code)
                                    <tr class="pro-title gradeX warning odd">
                                        <td colspan="10">{{$v->system_name}}({{$v->engin_name}})</td>
                                    </tr>
                                    @php( $system_code = $v->system_code)
                                @endif
                            <tr class="sub_arch_{{$v->sub_arch_id}} gradeA success odd">
                                <td  colspan="10"> &nbsp;&nbsp;&nbsp;{{$v->sub_system_name}} <span style="color:#1d52f6">工况：{{$v->work_code}}</span> 编码：{{$v->sub_system_code}}</td>

                            </tr>
                            @if(isset($offer_item[$v->sub_arch_id]))
                                @foreach($offer_item[$v->sub_arch_id] as $k=>$mate)
                                    @php
                                    $xuhao++;
                                    @endphp
                                <tr class="materialList sub_arch_{{$mate->sub_arch_id}}" id="mater_{{$xuhao}}">
                                    <td class="sub_arch_material_{{$mate->sub_arch_id}}">{{$k+1}}</td>
                                    <td>{{ $mate->material_name}}</td>
                                    <td>{{ $mate->characteristic }}</td>
                                    <td>{{ $mate->offer_unit }}</td>
                                    <td>{{ number_format($mate->drawing_quantity, 2, '.', '') }}</td>
                                    <td>{{ number_format($mate->loss_ratio, 2, '.', '') }}</td>
                                    <td>{{ number_format($mate->engineering_quantity, 2, '.', '') }}</td>
                                    <td>{{ $mate->brand_name}}</td>
                                    <td>{{ number_format($mate->offer_price, 2, '.', '') }}</td>
                                    <td>{{ number_format($mate->total_material_price, 2, '.', '') }}</td>
                                </tr>
                                @endforeach
                            @endif
                            @endforeach
                            <tr>
                                <td colspan="10" class="pro-title">其他费用</td>
                            </tr>

                            <tr>
                                <td class="pro-title" colspan="3">运输费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td >{{isset($offer->freight_price)?number_format($offer->freight_price, 2, '.', ''):''}}</td>
                                <td id="freight_price_sum">{{isset($offer->freight_charge)?number_format($offer->freight_charge, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">包装费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td >{{isset($offer->package_price)?number_format($offer->package_price, 2, '.', ''):''}}</td>
                                <td id="package_price_sum">{{isset($offer->package_charge)?number_format($offer->package_charge, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">装箱费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td >{{isset($offer->packing_price)?number_format($offer->packing_price, 2, '.', ''):''}}</td>
                                <td  id="packing_price_sum">{{isset($offer->packing_charge)?number_format($offer->packing_charge, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="9" style="text-align: center;font-weight: bold;">材料费合计</td>

                                <td  id="total_material">{{isset($offer->material_total_price)?number_format($offer->material_total_price, 2, '.', ''):''}}</td>
                            </tr>

                            <tr>
                                <td class="pro-title" colspan="3">施工安装费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td >{{isset($offer->construction_price)?number_format($offer->construction_price, 2, '.', ''):''}}</td>
                                <td >{{isset($offer->construction_charge)?number_format($offer->construction_charge, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td colspan="9" class="pro-title" style="text-align: center;font-weight: bold;">工程造价(直接)</td>
                                <td>{{isset($offer->direct_project_cost)?number_format($offer->direct_project_cost, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">利润</td>
                                <td class="pro-title" colspan="2">元</td>
                                <td colspan="2"></td>
                                <td >%</td>
                                <td >{{isset($offer->profit_ratio)?number_format($offer->profit_ratio, 2, '.', ''):''}}</td>
                                <td >{{isset($offer->profit)?number_format($offer->profit, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">税费</td>
                                <td class="pro-title" colspan="2">元</td>
                                <td colspan="2"></td>
                                <td >%</td>
                                <td >{{isset($offer->tax_ratio)?number_format($offer->tax_ratio, 2, '.', ''):''}}</td>
                                <td >{{isset($offer->tax)?number_format($offer->tax, 2, '.', ''):''}}</td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="9" style="text-align: center;font-weight: bold;">工程单价(元/m²)</td>
                                <td id="unit_price">{{isset($offer->total_offer_price)?number_format($offer->total_offer_price/$engineering->build_area, 2, '.', ''):''}}</td>

                            </tr>
                            <tr>
                                <td class="pro-title" colspan="9" style="text-align: center;font-weight: bold;">工程总价(元)</td>
                                <td >{{isset($offer->total_offer_price)?number_format($offer->total_offer_price, 2, '.', ''):''}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>


                        <div class="layui-form-item" style="float: right;clear: left">
                            <a href="javascript:history.go(-1)">
                                <label for="L_repass" class="layui-form-label"></label>
                                <span class="btn btn-success" lay-filter="add" lay-submit="">返回/取消</span>
                            </a>
                        </div>

                        <div class="clearfix"></div>
                            <hr>
                        <div style="margin: 10px">
                            <div>报价说明：</div>
                            <div>1、工程量=图质量*(100+损耗)/100</div>
                            <div>2、品牌价格信息来自部件集成管理板块</div>
                            <div>3、合价=工程量*单价</div>
                            <div>4、材料费合计=运输费+包装费+装箱费+材料费用</div>
                            <div>5、施工安装费=施工单价*建筑面积</div>
                            <div>6、工程造价(直接)=材料费合计+施工安装费</div>
                            <div>7、利润=工程造价(直接)* 利润费比率</div>
                            <div>8、税费=(工程造价(直接)+利润)* 税费比率</div>
                            <div>9、工程造价总计=工程造价(直接)+利润+税费</div>
                            <div>10、工程单价=工程造价总计/建筑面积</div>
                        </div>
                            <div class="clearfix"></div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


    <script>
        function submitStatus() {
            sta =$('#project_status').val();
            if(sta == 1){
                showMsg('当前状态未更改，不能提交')
                return false;
            }
            return true;
        }
        function showMsg(str){
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.msg(str);
            });
        }

    </script>

@endsection
