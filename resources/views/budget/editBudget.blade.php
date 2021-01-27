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
                        {{$project->project_name}}<b>工程预算清单</b>
                    </div>
                </div>
                <div class="widget-body">
                    <div id="dt_example" class="example_alt_pagination">
                        <form method="post" action="/budget/postEditBudget/{{ $engin_id }}">
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
                                    <td ><input type="text" name="quotation_date" id="quotation_date" value="{{isset($budget->quotation_date)?$budget->quotation_date:''}}"  lay-skin="primary" class="notempty span8"></td>
                                    <td class="pro-title">报价有效期限(天)</td>
                                    <td ><input type="text" name="quotation_limit_day" id="quotation_limit_day" value="{{isset($budget->quotation_limit_day)?$budget->quotation_limit_day:''}}" lay-skin="primary" class="notempty span8" onclick="return key(this)"></td>
                                    <td class="pro-title">建筑数量(栋)</td>
                                    <td>{{$engineering->build_number}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                            @if(count($project_file) > 0)
                                <table class="layui-table layui-form">
                                    <thead>
                                    <tr>
                                        <th colspan="4"><span class="btn btn-info">项目文件</span></th>
                                        <th>
                                    <span class="title" style="float: right;">
                                        @if(count($project_file) > 3)
                                            <a class="btn btn-success" id="addProjectFileID" attr="1000" onclick="show_list()">
                                            <i class="layui-icon" id="show_ids">显示更多</i> >>
                                        </a>
                                        @endif
                                    </span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="projectFileList">
                                    <tr>
                                        <td class="pro-title">文件类型</td>
                                        <td class="pro-title">序号</td>
                                        <td class="pro-title">文件名</td>
                                        <td class="pro-title">创建时间</td>
                                        <td class="pro-title">文件描述</td>
                                    </tr>

                                    @foreach($project_file as $k=>$file)
                                        @if($k >2)
                                            <tr style="display: none" class="show_tr_list">
                                        @else
                                            <tr>
                                                @endif

                                                <td>{{$file->file_type}}</td>
                                                <td class="pro-title">{{++$k}}</td>
                                                <td>
                                                    <div id="uploadfiletitle{{$k}}">{{$file->uploadfile}}
                                                        <a href="/project/projectFileDownload/{{$file->file_key}}"  style="color: red">
                                                            (下载)
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>{{$file->created_at}}</td>
                                                <td>
                                                    {{$file->file_name}}
                                                </td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                                <div class="clearfix"></div>
                            @endif


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
                                <th colspan="11"><span class="btn btn-info">预算清单列表</span></th>
                            </tr>
                            </thead>
                            <thead>
                            <tr>
                                <th style="width:5%">序号</th>
                                <th style="width:15%">材料名称</th>
                                <th style="width:15%">规格特性要求</th>
                                <th style="width:6%">预算单位</th>
                                <th style="width:9%">工程量(图纸)</th>
                                <th style="width:7%">损耗(%)</th>
                                <th style="width:8%">工程量(实际)</th>
                                <th style="width:14%">品牌</th>
                                <th style="width:8%">单价</th>
                                <th style="width:6%">合计</th>
                                <th style="width:8%">操作</th>
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
                                        <td colspan="11">{{$v->system_name}}({{$v->engin_name}})</td>
                                    </tr>
                                    @php( $system_code = $v->system_code)
                                @endif
                            <tr class="sub_arch_{{$v->sub_arch_id}} gradeA success odd">
                                <td  colspan="10"> &nbsp;&nbsp;&nbsp;<span class="btn btn-primary">{{$v->sub_system_name}}</span> <span style="color:#1d52f6">工况：{{$v->work_code}}</span> 编码：{{$v->sub_system_code}}</td>
                                <td > <span class="btn btn-success" onclick="addMaterial('{{ $v->sub_arch_id }}')">选择材料</span></td>
                            </tr>
                            @if(isset($budget_item[$v->sub_arch_id]))
                                @if(isset($materlist[$v->sub_arch_id]))
                                    @foreach($materlist[$v->sub_arch_id] as $k=>$list)
                                    @if(isset($brandlist[$list->id]))


                                        <tr class="materialList sub_arch_{{$list->architectural_sub_id}}" id="mater_{{$k}}">
                                            <td class="sub_arch_material_{{$list->architectural_sub_id}}">
                                                @if(isset($budget_item[$v->sub_arch_id][$list->id]))
                                                    <input type="checkbox" name="material_id[{{$k}}]" checked="checked" id="material_id_{{$k}}" value="{{$list->id}}">
                                                @else
                                                    <input type="checkbox" name="material_id[{{$k}}]" value="{{$list->id}}"  id="material_id_{{$k}}" >
                                                @endif
                                            </td>
                                            <td>{{$list->material_name}}</td>
                                            <td>{{ $list->characteristic }}</td>
                                            <td>{{ $list->material_budget_unit }}</td>
                                            <td>
                                                @if(isset($budget_item[$v->sub_arch_id][$list->id]))
                                                    <input type="text" lay-skin="primary" class="notempty span12 drawing_quantity"      value="{{$budget_item[$v->sub_arch_id][$list->id]['drawing_quantity']}}"  name="drawing_quantity[{{$k}}]" id="drawing_quantity" onclick="selectDrawing({{$k}},this)" ></td>
                                                @else
                                                    <input type="text" lay-skin="primary" class="notempty span12 drawing_quantity"      value=""  name="drawing_quantity[{{$k}}]" id="drawing_quantity" onclick="selectDrawing({{$k}},this)" ></td>
                                                @endif
                                            <td><input type="text" lay-skin="primary" class=" span12 loss_ratio"           disabled  value="{{ $list->waste_rate }}"  name="loss_ratio[{{$k}}]" id="loss_ratio" ></td>
                                            <td>
                                                @if(isset($budget_item[$v->sub_arch_id][$list->id]))
                                                    <input type="text" lay-skin="primary" class=" span12 engineering_quantity" disabled value="{{$budget_item[$v->sub_arch_id][$list->id]['engineering_quantity']}}"  name="engineering_quantity[{{$k}}]" id="engineering_quantity" ></td>
                                                @else
                                                    <input type="text" lay-skin="primary" class=" span12 engineering_quantity" disabled value=""  name="engineering_quantity[{{$k}}]" id="engineering_quantity" ></td>
                                                @endif
                                            <td>
                                                <select  name="mbs_id[{{$k}}]" onchange="selectbrand({{$k}},this)" class=" notempty mbs_id  span12" >
                                                    <option value="0" ></option>
                                                    @if(isset($brandlist[$list->id]))
                                                        @foreach($brandlist[$list->id] as $list11)
                                                            @if(isset($budget_item[$v->sub_arch_id][$list->id]))
                                                                <option value="{{$list11->id}}" selected="selected" class="mbs_id_{{$list11->id}}" budget_unit_price="{{$list11->budget_unit_price}}"  title="{{$list11->supplier}}"  >{{$list11->brand_name}}({{$list11->supplier}})</option>
                                                            @else
                                                                <option value="{{$list11->id}}" class="mbs_id_{{$list11->id}}" budget_unit_price="{{$list11->budget_unit_price}}"  title="{{$list11->supplier}}"  >{{$list11->brand_name}}({{$list11->supplier}})</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </td>
                                            <td>
                                                @if(isset($budget_item[$v->sub_arch_id][$list->id]))
                                                    <input type="text" lay-skin="primary" class=" span12 budget_price"         disabled value="{{$budget_item[$v->sub_arch_id][$list->id]['budget_price']}}"  name="budget_price[{{$k}}]" id="budget_price" >
                                                @else
                                                    <input type="text" lay-skin="primary" class=" span12 budget_price"         disabled value=""  name="budget_price[{{$k}}]" id="budget_price" >
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($budget_item[$v->sub_arch_id][$list->id]))
                                                    <input type="text" lay-skin="primary" class=" span12 total_material_price" disabled value="{{$budget_item[$v->sub_arch_id][$list->id]['total_material_price']}}"  name="total_material_price[{{$k}}]" id="total_material_price" >
                                                @else
                                                    <input type="text" lay-skin="primary" class=" span12 total_material_price" disabled value=""  name="total_material_price[{{$k}}]" id="total_material_price" >
                                                @endif

                                            </td>
                                            <td ><span class="btn btn-danger" onclick="deleteTrRow(this)">删除</span></td>
                                        </tr>
                                    @endif
                                    @endforeach
                                @endif

                            @endif
                            @endforeach
                            <tr>
                                <td colspan="11" class="pro-title">其他费用</td>
                            </tr>

                            <tr>
                                <td class="pro-title" colspan="3">运输费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td ><input type="text" name="freight_price" value="{{isset($budget->freight_price)?$budget->freight_price:''}}" id="freight_price" lay-skin="primary" class="notempty span12" onchange="return selectPrice(this)"></td>
                                <td id="freight_price_sum">{{isset($budget->freight_charge)?$budget->freight_charge:''}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">包装费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td ><input type="text" name="package_price" value="{{isset($budget->package_price)?$budget->package_price:''}}" id="package_price" lay-skin="primary" class="notempty span12" onchange="return selectPrice(this)"></td>
                                <td id="package_price_sum">{{isset($budget->package_charge)?$budget->package_charge:''}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">装箱费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td ><input type="text" name="packing_price" value="{{isset($budget->packing_price)?$budget->packing_price:''}}" id="packing_price" lay-skin="primary" class="notempty span12" onchange="return selectPrice(this)"></td>
                                <td  id="packing_price_sum">{{isset($budget->packing_charge)?$budget->packing_charge:''}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="9" style="text-align: center;font-weight: bold;">材料费合计</td>

                                <td  id="total_material">{{isset($budget->material_total_price)?$budget->material_total_price:''}}</td>
                                <td></td>
                            </tr>

                            <tr>
                                <td class="pro-title" colspan="3">施工安装费</td>
                                <td class="pro-title" colspan="2">(元/m²)</td>
                                <td colspan="3"></td>
                                <td ><input type="text" name="construction_price" value="{{isset($budget->construction_price)?$budget->construction_price:''}}" id="construction_price" lay-skin="primary" class="notempty span12" onchange="return selectPrice(this)"></td>
                                <td ><input type="text" name="construction_charge" value="{{isset($budget->construction_charge)?$budget->construction_charge:''}}" id="construction_charge" lay-skin="primary" class="span12" disabled></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="9" class="pro-title" style="text-align: center;font-weight: bold;">工程造价(直接)</td>
                                <td><input type="text" name="direct_project_cost" value="{{isset($budget->direct_project_cost)?$budget->direct_project_cost:''}}" readonly='readonly' id="direct_project_cost"  value="" style='width:100px;background: #f0f0f0;'/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">利润</td>
                                <td class="pro-title" colspan="2">元</td>
                                <td colspan="2"></td>
                                <td >%</td>
                                <td ><input type="text" name="profit_ratio" value="{{isset($budget->profit_ratio)?$budget->profit_ratio:''}}" id="profit_ratio" lay-skin="primary" class="notempty span12"  onchange="return selectPrice(this)"></td>
                                <td ><input type="text" name="profit" value="{{isset($budget->profit)?$budget->profit:''}}" id="profit" lay-skin="primary" class="span12" disabled></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="3">税费</td>
                                <td class="pro-title" colspan="2">元</td>
                                <td colspan="2"></td>
                                <td >%</td>
                                <td ><input type="text" name="tax_ratio" value="{{isset($budget->tax_ratio)?$budget->tax_ratio:''}}" id="tax_ratio" lay-skin="primary" class="notempty span12"  onchange="return selectPrice(this)"></td>
                                <td ><input type="text" name="tax" value="{{isset($budget->tax)?$budget->tax:''}}" id="tax" lay-skin="primary" class="span12" disabled></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="9" style="text-align: center;font-weight: bold;">工程单价(元/m²)</td>
                                <td id="unit_price"></td>
                                <td ></td>
                            </tr>
                            <tr>
                                <td class="pro-title" colspan="9" style="text-align: center;font-weight: bold;">工程总价(元)</td>
                                <td ><input type="text" name="total_budget_price" value="{{isset($budget->total_budget_price)?round($budget->total_budget_price,2):''}}" id="total_budget_price" lay-skin="primary" class=" span12" disabled></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        @if($budget)
                            <div style="color: red;float:left"> 数据发生变化,将导致已生成的报价单失效，请谨慎操作？ </div>
                        @endif
                        <div class="layui-form-item" style="float: right;clear: left">
                            <label for="L_repass" class="layui-form-label"></label>
                            <button class="btn btn-success" lay-filter="add" type="submit" lay-submit="" onclick='return form_submit()'>确认/保存</button>
                        </div>
                        <div class="layui-form-item" style="float: right;clear: left">
                            <a href="javascript:history.go(-1)">
                                <label for="L_repass" class="layui-form-label"></label>
                                <span class="btn btn-success" lay-filter="add" lay-submit="">返回/取消</span>
                            </a>
                        </div>

                        <div class="clearfix"></div>
                            <hr>
                        <div style="margin: 10px">
                            <div>预算说明：</div>
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
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


    <script>
        var floor_area = {{$engineering->build_area}} ; //建筑面积
        var material_list=[];

        function addMaterial(id) {
            $.ajax({
                url:'/budget/getEnginMaterialList/'+id,
                type:'get',
                // contentType: 'application/json',
                success:function(data){
                    console.log(data);
                    if(data.status == 1){
                        $.each( data.data, function(index,content){ //材料id
                            addmaterialDetail(id,content);
                        })
                    }else{
                        showMsg('该工程下没有材料信息，不能添加材料');
                        return false;
                    }
                },
            });
        }
        //添加材料信息
        function addmaterialDetail(id,option){
            if($("#mater_"+option.id).length > 0){
                console.log(option.id+'已存在');
                return true;
            }
            var optionstr ='';
            $.each( option.brandlist, function(index,content){
                optionstr +=`<option value="`+content.mbs_id+`" class="mbs_id_`+content.mbs_id+`" budget_unit_price="`+content.budget_unit_price+`" title="`+content.supplier+`">`+content.brand_name+`</option>`;
            })

           str = `<tr class="materialList sub_arch_`+id+`" id="mater_`+option.id+`">
                     <td class="sub_arch_material_`+id+`">
                <input type="checkbox" name="material_id[`+option.id+`]" value="`+option.id+`"  id="material_id_`+option.id+`" >
                </td>
                <td>`+option.material_name+`</td>
                <td>`+option.characteristic+`</td>
                <td>`+option.material_budget_unit+`</td>
                <td>
                <input type="text" lay-skin="primary" class="notempty span12 drawing_quantity"      value=""  name="drawing_quantity[`+option.id+`]" id="drawing_quantity" onclick="selectDrawing(`+option.id+`,this)" ></td>
                <td><input type="text" lay-skin="primary" class=" span12 loss_ratio"   value="`+option.waste_rate+ `"        disabled  name="loss_ratio[`+option.id+`]" id="loss_ratio" ></td>
                <td>
                <input type="text" lay-skin="primary" class=" span12 engineering_quantity" disabled value=""  name="engineering_quantity[`+option.id+`]" id="engineering_quantity" ></td>
                <td>
                   <select  name="mbs_id[`+option.id+`]" onchange="selectbrand(`+option.id+`,this)" class=" notempty mbs_id  span12" >
                   <option value="0" ></option>
                    `+optionstr+`
                    </select>
                </td>
                <td><input type="text" lay-skin="primary" class=" span12 budget_price"         disabled   name="budget_price[`+option.id+`]" id="budget_price" >
                </td>
                <td><input type="text" lay-skin="primary" class=" span12 total_material_price " disabled value=""  name="total_material_price[`+option.id+`]" id="total_material_price" >
                </td>
                <td ><span class="btn btn-danger" onclick="deleteTrRow(this)">删除</span></td>
            </tr>`;

            //添加材料到指定位置
            $(".sub_arch_"+id+":last").after(str);
            //key(this);
            /**更改材料序号
            $(".sub_arch_material_"+id).each(function(index,element){
                $(this).html(index +1);
            });
             * */
            //点击文本框设置背景色
            $("input").focus(function(){
                $(this).css("background-color","#fff");
            });
        }

        //日期选择器
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            //常规用法
            laydate.render({
                elem: '#quotation_date'
            });
        });
        //点击只能输入数字
        function key(th){
            $(th).keyup(function(){
                $(this).val($(this).val().replace(/[^0-9.]/g,''));
            }).bind("paste",function(){  //CTR+V事件处理
                $(this).val($(this).val().replace(/[^0-9.]/g,''));
            }).css("ime-mode", "disabled"); //CSS设置输入法不可用
            va =$(th).val();
            if(va > 1000000000 || va < 0) {
                $(th).val(0);
            }
        }
        //删除材料
        function deleteTrRow(tr){
            $(tr).parent().parent().remove();
            total_price();
        }

        //选择材料
        function selectMaterial(id,th) {
            console.log($(th).val());
            material_id =$(th).val();
            if(material_id == 0 || material_id ==''){
                showMsg('请选择材料');
                $('#mater_'+id+' input').val('');
                return false;
            }
            //获取材料信息和品牌信息
            $.ajax({
                url:'/budget/getMaterialBrandList/'+material_id,
                type:'get',
                // contentType: 'application/json',
                success:function(data){
                    console.log(data);
                    if(data.status == 1){
                        //填充材料和品牌信息
                        fillMaterialBrand(id,data.data.material,data.data.brand);
                        $('#mater_'+intid+' .budget_price').val('');
                        $('#mater_'+intid+' .total_material_price').val('');
                    }else{
                        showMsg('该材料下没有品牌信息，请选择其他材料');
                        return false;
                    }
                },
            });
        }
        //补充对应的材料和品牌信息
        function fillMaterialBrand(intid,material,brand) {
            $('#mater_'+intid+' .mbs_id').empty();
            $('#mater_'+intid+' .characteristic').val(material.characteristic);
            $('#mater_'+intid+' .material_budget_unit').val(material.material_budget_unit);
            $('#mater_'+intid+' .loss_ratio').val(material.waste_rate);
            var option ='<option value="0" ></option>';
            $.each( brand, function(index,content){
                option +='<option value="'+content.mbs_id+'" class="mbs_id_'+content.mbs_id+'" budget_unit_price="'+content.budget_unit_price+'"> '+content.brand_name +'('+content.supplier+')</option>';
            });
            $('#mater_'+intid+' .mbs_id').append(option);
            jisuanprice(intid);
        }
        //显示提示信息
        function showMsg(str){
            layui.use('layer', function(){
                var layer = layui.layer;
                layer.msg(str);
            });
        }
        //选择品牌
        function selectbrand(intid,th){
            mbs_id =$(th).val();
            budget_unit_price =$('.mbs_id_'+mbs_id).attr('budget_unit_price');
            $('#mater_'+intid+' .budget_price').val(budget_unit_price);
            console.log(budget_unit_price);
            $("#material_id_"+intid).prop('checked','checked');
            $(th).css("background-color","#fff");
            jisuanprice(intid);
            total_price();
        }
        //填写工程量
        function selectDrawing(intid,th) {
            key(th); //只能输入数字和小数点
            $(th).blur(function(){
              //计算实际工程量
                jisuanprice(intid);
            })
            $("#material_id_"+intid).prop('checked','checked');
            total_price();
        }

        //计算当前材料的金额
        function jisuanprice(intid){
            drawing_quantity = $('#mater_'+intid+' .drawing_quantity').val();
            loss_ratio      = $('#mater_'+intid+' .loss_ratio').val();
            budget_price    = $('#mater_'+intid+' .budget_price').val();
            if(drawing_quantity =='' ||loss_ratio =='' ||budget_price ==''){
                $('#mater_'+intid+' .engineering_quantity').val('');
                $('#mater_'+intid+' .total_material_price').val('');
                return false;
            }
            //实际工程量
            engineering_quantity = drawing_quantity *(100 * 1 + loss_ratio * 1)/100
            $('#mater_'+intid+' .engineering_quantity').val(engineering_quantity.toFixed(2));
            //实际预算金额
            total_material_price = engineering_quantity * budget_price;
            $('#mater_'+intid+' .total_material_price').val(total_material_price.toFixed(2));
            total_price();
        }
        //选择金额
        function selectPrice(th) {
            key(th);
            total_price();
        }
        //计算金额
        function total_price(){
            console.log('aaa');
            freight_price= $("#freight_price").val();
            package_price= $("#package_price").val();
            packing_price= $("#packing_price").val();
            //施工安装费
            construction_price=$("#construction_price").val();
            $("#freight_price_sum").html((freight_price*floor_area).toFixed(2));
            $("#package_price_sum").html((package_price*floor_area).toFixed(2));
            $("#packing_price_sum").html((packing_price*floor_area).toFixed(2));
            $("#construction_charge").val((construction_price*floor_area).toFixed(2));
            var sum='';
            $(".total_material_price").each(function(){
                sum =sum *1 + $(this).val() * 1;
            });
            //材料总计费用
            sum =sum*1+freight_price*floor_area+package_price*floor_area+packing_price*floor_area;
            $('#total_material').html(sum.toFixed(2)) ;

            //工程造价(直接)
            sum =  sum *1+ construction_price*floor_area;
            $('#direct_project_cost').val(sum.toFixed(2));

            //利润额
            profit_ratio=$("#profit_ratio").val();
            profit=sum * profit_ratio/100;
            $("#profit").val(profit.toFixed(2));
            $('#profit_ratio').css('background','#fff');

            //税费=(工程造价(直接)+利润额)*税率
            tax_ratio=$("#tax_ratio").val();
            tax=(sum +profit)* tax_ratio/100;
            $("#tax").val(tax.toFixed(2));
            $('#tax_ratio').css('background','#fff');

            //工程造价总计(元)
            sum =sum+tax+profit;
            $('#total_budget_price').val(sum.toFixed(2));
            //工程单价
            unit_price=sum/floor_area;
            $('#unit_price').html(unit_price.toFixed(2));

        }
        //提交时的数据验证
        function form_submit(){
            $('input[type=checkbox]').prop('checked', 'checked');
            var sum=0;
            $(".notempty").each(function(){
                if($(this).val()){
                }else{
                    $(this).css('background','orange');
                    sum=1;
                }
            });
            $("select.notempty").each(function(){
                console.log($(this).val());
                if($(this).val() > 0){
                }else{
                    $(this).css('background','orange');
                    sum=1;
                }
            });

            if(sum == 1){
                showMsg('请将内容补充完全再提交')
                return false;
            }
            return true;
        }

        //点击文本框设置背景色
        $("input").focus(function(){
            $(this).css("background-color","#fff");
        });


        function submitStatus() {
            sta =$('#project_status').val();
            if(sta == 0){
                showMsg('当前状态未更改，不能提交')
                return false;
            }
            return true;
        }


        jQuery.fn.rowspan = function(colIdx) { //封装的一个JQuery小插件
            return this.each(function(){
                var that;
                $('tr', this).each(function(row) {
                    $('td:eq('+colIdx+')', this).filter(':visible').each(function(col) {
                        if (that!=null && $(this).html() == $(that).html()) {
                            rowspan = $(that).attr("rowSpan");
                            if (rowspan == undefined) {
                                $(that).attr("rowSpan",1);
                                rowspan = $(that).attr("rowSpan"); }
                            rowspan = Number(rowspan)+1;
                            $(that).attr("rowSpan",rowspan);
                            $(this).hide();
                        } else {
                            that = this;
                        }
                    });
                });
            });
        }

        $(function() {
            $("#projectFileList").rowspan(0);//传入的参数是对应的列数从0开始，哪一列有相同的内容就输入对应的列数值
            //$("#table1").rowspan(2);

        });
        function show_list() {
            $('.show_tr_list').toggle();
            $("#projectFileList").rowspan(0);
            htm =$('#show_ids').html();
            if(htm =='显示更多'){
                $('#show_ids').html('折叠');
            }else{
                $('#show_ids').html('显示更多');
            }
        }


    </script>

@endsection
