@extends('layouts.web')

@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>

    <div class="left-sidebar">
        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-header" style="text-align: center">
                        <div  style="text-align: center;clear: both;font-size: 16px;" >
                            <b>{{$project->project_name}}</b>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div id="dt_example" class="example_alt_pagination">
                                <table class="layui-table layui-form">
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
                                        <td class="pro-title">建筑数量(栋)</td>
                                        <td >{{$engineering->build_number}}</td>
                                    </tr>

                                    </tbody>
                                </table>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="widget">
                    <div class="widget-header" style="text-align: center">
                        <div  style="font-size: 16px;" >
                            <b>采购状态变更</b>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div id="dt_example" class="example_alt_pagination">
                            <form method="post" action="/progress/postEditProgress/{{$engin_id}}">
                            <table class="layui-table layui-form">
                                <tbody>
                                    <tr>
                                        <td>施工状态	</td>
                                        <td>
                                            <select name="build_status" id="build_status" class="input-medium span8" style="min-width: 80px">
                                                @if($progress->build_status == 0) <option value="0" selected="selected">未施工 </option> @else <option value="0" >未施工 </option> @endif
                                                @if($progress->build_status == 1) <option value="1" selected="selected">施工中</option> @else <option value="1" >施工中</option> @endif
                                                @if($progress->build_status == 2) <option value="2" selected="selected">竣工验收</option> @else <option value="2" >竣工验收</option> @endif
                                            </select>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>进度状态	</td>
                                        <td><select name="progress_status" id="progress_status" class="input-medium span8" style="min-width: 80px">
                                                @if($progress->progress_status ==1) <option value="1" selected="selected">正常</option> @else <option value="1" >正常</option> @endif
                                                @if($progress->progress_status ==2) <option value="2" selected="selected">延期 </option> @else <option value="2" >延期 </option> @endif
                                            </select>
                                        </td>
                                    </tr>
                                    @if(isset($userList))
                                    <tr>
                                        <td>采购负责人</td>
                                        <td>
                                            <select name="progress_uid" id="progress_uid" class="input-medium span8" style="min-width: 80px">
                                                <option value="0" > </option>
                                                @foreach($userList as $val)
                                                @if($val->id == $engineering->progress_uid)
                                                <option value="{{$val->id}}" selected="selected">{{$val->name}}</option>
                                                @else
                                                <option value="{{$val->id}}">{{$val->name}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>备注</td>
                                        <td><input name="remark" class="span12" value="{{isset($progress->remark)?$progress->remark:''}}"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="clearfix"></div>

                            <div class="layui-form-item" style="float: right;clear: left">
                                <label for="L_repass" class="layui-form-label"></label>
                                <button class="btn btn-success" lay-filter="add" type="submit" lay-submit="" >确认/保存</button>
                            </div>
                            <div class="layui-form-item" style="float: right;clear: left">
                                <a href="javascript:history.go(-1)">
                                    <label for="L_repass" class="layui-form-label"></label>
                                    <span class="btn btn-success" lay-filter="add" lay-submit="">返回/取消</span>
                                </a>
                            </div>

                            <div class="clearfix"></div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>




@endsection
