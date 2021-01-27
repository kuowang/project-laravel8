<?php

namespace App\Http\Controllers\Architectural;

use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\DB;


class EnginneringController extends WebController
{
    /**
     * 建筑设计人员选择对应项目的建筑设计系统信息
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //该方法验证说明必须登录用户才能操作
        $this->middleware('auth');
    }

    /**
     *洽谈项目列表
     * @return \Illuminate\Http\Response
     */
    public function enginStart(Request $request,$id=0)
    {
        $this->user();
        $data=$this->enginnering($request,$id,0);
        $data['subnavid']   =3500;
        if( !(in_array(350001,$this->user()->pageauth)) && !in_array(350701,$this->user()->manageauth)){
            return redirect('/home');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['userList']=DB::table('users')->where('status',1)->orderby('name')->select(['id','name','department_id'])->get();

        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('architectural.enginnering.enginStart',$data);
    }
    /**
     *实施项目列表
     * @return \Illuminate\Http\Response
     */
    public function enginConduct(Request $request,$id=0)
    {
        $this->user();
        $data=$this->enginnering($request,$id,1);
        $data['subnavid']   =3500;
        if( !(in_array(350002,$this->user()->pageauth)) && !in_array(350703,$this->user()->manageauth)){
            return redirect('/architectural/enginStart?status=2&notice='.'您没有操作该功能权限');
        }

        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['userList']=DB::table('users')->where('status',1)->orderby('name')->select(['id','name','department_id'])->get();

        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('architectural.enginnering.enginConduct',$data);
    }
    /**
     *竣工项目列表
     * @return \Illuminate\Http\Response
     */
    public function enginCompleted(Request $request,$id=0)
    {
        $this->user();
        $data=$this->enginnering($request,$id,2);
        $data['subnavid']   =3500;
        if( !(in_array(350003,$this->user()->pageauth)) && !in_array(350705,$this->user()->manageauth)){
            return redirect('/architectural/enginStart?status=2&notice='.'您没有操作该功能权限');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('architectural.enginnering.enginCompleted',$data);
    }
    /**
     *终止项目列表
     * @return \Illuminate\Http\Response
     */
    public function enginTermination(Request $request,$id=0)
    {
        $this->user();
        $data=$this->enginnering($request,$id,4);
        $data['subnavid']   =3500;
        if( !(in_array(350004,$this->user()->pageauth)) && !in_array(350706,$this->user()->manageauth)){
            return redirect('/architectural/enginStart?status=2&notice='.'您没有操作该功能权限');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('architectural.enginnering.enginTermination',$data);
    }

    //查询项目信息
    protected function getEngineList($id=0,$status,$project_name='',$address='',$project_leader='',$page=1,$rows=20)
    {
        $db=DB::table('project')
            ->leftjoin('engineering','project.id','=','project_id')
            ->where('engineering.status',$status);

        if(!empty($project_name)){
            $db->where('project_name','like','%'.$project_name.'%');
        }
        if($id){
            $db->where('project_id',$id);
        }
        if(!empty($address)){
            $db->Where(function ($query)use($address) {
                $query->where('province', 'like','%'.$address.'%')
                    ->orwhere('city', 'like','%'.$address.'%')
                    ->orwhere('county', 'like','%'.$address.'%')
                    ->orwhere('address_detail', 'like','%'.$address.'%')
                    ->orwhere('foreign_address', 'like','%'.$address.'%')
                ;
            });
        }
        if(!empty($project_leader)){
            $db->where('project_leader','like','%'.$project_leader.'%');
        }

        $data['count'] =$db->count();
        $data['data']= $db->orderby('project.id','desc')
            ->orderby('engineering.id','asc')
            ->select(['project.project_name','project.design_username as project_design_username',
                'engineering.id as engineering_id','engineering.*'])
            ->skip(($page-1)*$rows)
            ->take($rows)
            ->get();
        return $data;
    }
    //工程信息列表
    private function enginnering($request,$id=0,$status=0)
    {
        $project_name       =$request->input('project_name','');
        $address            =$request->input('address','');
        $project_leader     =$request->input('project_leader','');
        $page               =$request->input('page',1);
        $rows               =$request->input('rows',40);
        $data['project_name']   =$project_name;
        $data['address']        =$address;
        $data['project_leader']=$project_leader;
        $datalist=$this->getEngineList($id,$status,$project_name,$address,$project_leader,$page,$rows);
        if($status == 0){
            $url='/architectural/enginStart/'.$id.'?project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;
        }elseif($status == 1){
            $url='/architectural/enginConduct/'.$id.'?project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;
        }elseif($status == 2){
            $url='/architectural/enginCompleted/'.$id.'?project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;
        }elseif($status == 4){
            $url='/architectural/enginTermination/'.$id.'?project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;
        }else{
            $url='/architectural/enginStart/'.$id.'?project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;
        }$data['page']   =$this->webfenye($page,ceil($datalist['count']/$rows),$url);
        $data['data']   =$datalist['data'];
        $data['navid']      =35;
        $data['id']=$id;
        return $data;
    }

    //编辑工程设计详情
    public function editEngin(Request $request,$id)
    {
        $this->user();
        $data['navid']      =35;
        $data['subnavid']   =3500;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000102,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350702,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        //建筑系统信息 以及项目对应的子系统信息

        $arch_system=DB::table('architectural_sub_system as sub_s')
                       ->join('architectural_system as a','a.id','=','sub_s.architectural_id')
            ->where('sub_s.status',1)
            ->where('a.status',1)
            ->select(['a.id as arch_id',
                'a.system_name','a.engineering_name as engin_name','a.system_code',
                'sub_s.id as sub_arch_id','sub_s.sub_system_name','sub_s.sub_system_code','sub_s.work_code'])
            ->orderby('a.system_code')
            ->orderby('sub_s.sub_system_code')
            ->get();
        $data['engin_system']=DB::table('enginnering_architectural')
            ->where('engin_id',$id)
            ->pluck('work_code','sub_arch_id')->toarray();
        $data['engineering']=$engineering;
        $data['project']    =$project;
        $data['engin_id'] =$id;
        $data['arch_system']=$arch_system;
        //如果为新增工况 则出现选择模板
        //查询其他工程的模板列表
        $data['otherEngin']=DB::table('engineering')
            ->where('project_id',$engineering->project_id)
            ->where('id','!=',$id)
            ->orderby('engineering_name')
            ->select(['id','engineering_name','status','is_conf_architectural'])
            ->get();
        //获取项目文件
        $data['project_file']=DB::table('project_file')->where('status',1)
            ->where('project_id',$engineering->project_id)
            ->orderby('file_type')
            ->orderby('id')->get();
        return view('architectural.enginnering.editEngin',$data);
    }

    //获取指定工程对应的工况信息
    public function getEnginArchList(Request $request,$id){
        $data =DB::table('enginnering_architectural')->where('engin_id',$id)
            ->select(['arch_id','sub_arch_id'])->get();
        if(empty($data)){
            return $this->error('没有工况信息');
        }
        return $this->success($data);
    }
    //提交编辑工程设计详情
    public function postEditEngin(Request $request,$id)
    {
        //return $this->success($request->all());
        $sub_arch_id    =$request->input('sub_arch_id',[]);
        $engin_work_code=$request->input('engin_work_code',[]);
        if(empty($sub_arch_id) || count($sub_arch_id) != count($engin_work_code)){
            echo "<script>alert('内容不能为空');history.go(-1);</script>";
        }
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000102,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350702,$this->user()->manageauth)){
        }else{
            //设计人员和管理者可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        $enginList=DB::table('architectural_sub_system as sub_s')
            ->join('architectural_system as a','a.id','=','sub_s.architectural_id')
            ->where('sub_s.status',1)
            ->where('a.status',1)
            ->wherein('sub_s.id',array_values($sub_arch_id))
            ->select(['a.id as arch_id',
                'a.system_name','a.engineering_name as engin_name','a.system_code',
                'sub_s.id as sub_arch_id','sub_s.sub_system_name','sub_s.sub_system_code','sub_s.work_code',
               ])
            ->get();
        //$datalist['canshu']=$request->all();
        //$datalist['engin']=$enginList;
        DB::beginTransaction();
        //删除原始数据
        DB::table('enginnering_architectural')->where('engin_id',$id)->delete();
        //设置工程配置建筑设计信息
        DB::table('engineering')->where('id',$id)->update(['is_conf_architectural'=>1]);
        //添加新数据
        if($enginList){
            $time=date('Y-m-d');
            foreach($enginList as $engin){
                $data[]=[
                    'project_id'=>$engineering->project_id,
                    'engin_id'=>$id,
                    'arch_id'=>$engin->arch_id,
                    'system_name'=>$engin->system_name,
                    'engin_name'=>$engin->engin_name,
                    'system_code'=>$engin->system_code,
                    'sub_arch_id'=>$engin->sub_arch_id,
                    'sub_system_name'=>$engin->sub_system_name,
                    'sub_system_code'=>$engin->sub_system_code,
                    'work_code'=>$engin->work_code,
                    'uid'=>$this->user()->id,
                    'created_at'=>$time,
                ];
            }
            DB::table('enginnering_architectural')->insert($data);
        }
        DB::commit();
        return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=1&notice='.'编辑工程对应的建筑设计信息成功');

    }
    //编辑实施工程设计详情
    public function editConductEngin(Request $request,$id)
    {
        $this->user();
        $data['navid']      =35;
        $data['subnavid']   =3500;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginConduct?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000202,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350704,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginConduct/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        //建筑系统信息 以及项目对应的子系统信息

        $arch_system=DB::table('architectural_sub_system as sub_s')
            ->join('architectural_system as a','a.id','=','sub_s.architectural_id')
            ->where('sub_s.status',1)
            ->where('a.status',1)
            ->select(['a.id as arch_id',
                'a.system_name','a.engineering_name as engin_name','a.system_code',
                'sub_s.id as sub_arch_id','sub_s.sub_system_name','sub_s.sub_system_code','sub_s.work_code'])
            ->orderby('a.system_code')
            ->orderby('sub_s.sub_system_code')
            ->get();
        $data['engin_system']=DB::table('enginnering_architectural')
            ->where('engin_id',$id)
            ->pluck('work_code','sub_arch_id');
        $data['engineering']=$engineering;
        $data['project']    =$project;
        $data['engin_id'] =$id;
        $data['arch_system']=$arch_system;

        //获取项目文件
        $data['project_file']=DB::table('project_file')->where('status',1)
            ->where('project_id',$project->id)
            ->orderby('file_type')
            ->orderby('id')->get();

        return view('architectural.enginnering.editConductEngin',$data);
    }
    //提交编辑实施工程设计详情
    public function postConductEngin(Request $request,$id)
    {
        $sub_arch_id    =$request->input('sub_arch_id',[]);
        if(empty($sub_arch_id)){
            echo "<script>alert('没有选择任何新的工况');history.go(-1);</script>";
        }
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginConduct?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000202,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350704,$this->user()->manageauth)){
        }else{
            //设计人员和管理者可以操作更改工程设计详情
            return redirect('/architectural/enginConduct/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        $enginList=DB::table('architectural_sub_system as sub_s')
            ->join('architectural_system as a','a.id','=','sub_s.architectural_id')
            ->where('sub_s.status',1)
            ->where('a.status',1)
            ->wherein('sub_s.id',array_values($sub_arch_id))
            ->select(['a.id as arch_id',
                'a.system_name','a.engineering_name as engin_name','a.system_code',
                'sub_s.id as sub_arch_id','sub_s.sub_system_name','sub_s.sub_system_code','sub_s.work_code',
            ])
            ->get();
        //$datalist['canshu']=$request->all();
        //$datalist['engin']=$enginList;
        DB::beginTransaction();
        //删除原始数据
        //DB::table('enginnering_architectural')->where('engin_id',$id)->delete();
        //设置工程配置建筑设计信息
        DB::table('engineering')->where('id',$id)->update(['is_conf_architectural'=>1]);
        //添加新数据
        if($enginList){
            $time=date('Y-m-d');
            foreach($enginList as $engin){
                $data=[
                    'project_id'=>$engineering->project_id,
                    'engin_id'=>$id,
                    'arch_id'=>$engin->arch_id,
                    'system_name'=>$engin->system_name,
                    'engin_name'=>$engin->engin_name,
                    'system_code'=>$engin->system_code,
                    'sub_arch_id'=>$engin->sub_arch_id,
                    'sub_system_name'=>$engin->sub_system_name,
                    'sub_system_code'=>$engin->sub_system_code,
                    'work_code'=>$engin->work_code,
                    'uid'=>$this->user()->id,
                    'created_at'=>$time,
                ];
                $info =DB::table('enginnering_architectural')->where('project_id',$engineering->project_id)
                    ->where('engin_id',$id)
                    ->where('sub_arch_id',$engin->sub_arch_id)
                    ->first();
                if($info){
                    unset($data['uid']);
                    unset($data['created_at']);
                    $data['edit_uid']=$this->user()->id;
                    $data['updated_at'] =$time;
                    DB::table('enginnering_architectural')->where('engin_id',$id)
                        ->where('project_id',$engineering->project_id)
                        ->where('sub_arch_id',$engin->sub_arch_id)
                        ->update($data);
                }else{
                    DB::table('enginnering_architectural')->insert($data);
                }
            }
        }
        DB::commit();
        return redirect('/architectural/enginConduct/'.$engineering->project_id.'?status=1&notice='.'编辑工程对应的建筑设计信息成功');
    }

    //查看洽谈工程设计详情
    public function enginStartDetail(Request $request,$id)
    {
        $this->user();
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000101,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350701,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限查看该工程信息');
        }
        return $this->enginDetail($engineering,$project,$id);
    }
    //查看实施工程设计详情
    public function enginConductDetail(Request $request,$id)
    {
        $this->user();
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000201,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350703,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限查看该工程信息');
        }
        return $this->enginDetail($engineering,$project,$id);
    }
    //查看竣工工程设计信息
    public function enginCompletedDetail(Request $request,$id)
    {
        $this->user();
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000301,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350705,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限查看该工程信息');
        }
        return $this->enginDetail($engineering,$project,$id);
    }
    //查看终止项目工程设计信息
    public function enginTerminationDetail(Request $request,$id)
    {
        $this->user();
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000401,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350706,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限查看该工程信息');
        }
        return $this->enginDetail($engineering,$project,$id);
    }
    //查询详情
    protected function enginDetail($engineering,$project,$id)
    {
        $data['navid']      =35;
        $data['subnavid']   =3500;
        //建筑系统信息 以及项目对应的子系统信息
        $data['engin_system']=DB::table('enginnering_architectural')
            ->where('engin_id',$id)
            ->orderby('system_code')
            ->orderby('sub_system_code')
            ->get();
        $data['engineering']=$engineering;
        $data['project']    =$project;
        //获取项目文件
        $data['project_file']=DB::table('project_file')->where('status',1)
            ->where('project_id',$project->id)
            ->orderby('file_type')
            ->orderby('id')->get();
        return view('architectural.enginnering.enginArchitectDetail',$data);
    }
    //项目设计参数
    public function editEnginParam(Request $request,$id){
        $this->user();
        $data['navid']      =35;
        $data['subnavid']   =3500;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000102,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350702,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        $data['param'] =DB::table('engineering_param')->where('engin_id',$id)->first();
        $data['project'] =$project;
        $data['engineering']=$engineering;
        $data['engin_id'] =$id;

        if($data['param']){
            $data['storey_height']  =json_decode($data['param']->storey_height,true) ;
            $data['house_height']   =json_decode($data['param']->house_height,true) ;
            $data['house_area']     =json_decode($data['param']->house_area,true) ;
            $data['room_position']  =json_decode($data['param']->room_position,true) ;
            $data['room_name']      =json_decode($data['param']->room_name,true);
            $data['room_area']      =json_decode($data['param']->room_area,true);
        }
        $data['userList']=DB::table('users')->where('status',1)->orderby('name')->select(['id','name','department_id'])->get();
        //获取参数数据
        $params =['engin_use_time','engin_seismic_grade','engin_waterproof_grade','engin_refractory_grade','engin_insulation_sound_grade','engin_energy_grade','engin_room_name'];
        $items =DB::table('system_setting')->wherein('field',$params)->orderby('field')->orderby('sort')->select(['field','name'])->get();
        foreach($items as $item){
            $data[$item->field][] = $item->name ;
        }
        //获取项目文件
        $data['project_file']=DB::table('project_file')->where('status',1)
            ->where('project_id',$engineering->project_id)
            ->orderby('file_type')
            ->orderby('id')->get();
        return view('architectural.enginnering.editEnginParam',$data);
    }
    //提交建筑设计参数配置
    public function postEditEnginParam(Request $request,$id){

        $use_time           =$request->input('use_time',0);                   //"use_time": "70",
		$seismic_grade      =$request->input('seismic_grade',0);                 //"seismic_grade": "7",
		$waterproof_grade   =$request->input('waterproof_grade',0);                   //"waterproof_grade": "6",
		$refractory_grade   =$request->input('refractory_grade',0);                   //"refractory_grade": "6",
		$insulation_sound_grade =$request->input('insulation_sound_grade',0);                   //"insulation_sound_grade": "5",
		$energy_grade       =$request->input('energy_grade',0);                   //"energy_grade": "6",
		$basic_wind_pressure =$request->input('basic_wind_pressure',0);                 //"basic_wind_pressure": "21",
		$basic_snow_pressure =$request->input('basic_snow_pressure',0);                 //"basic_snow_pressure": "31",
		$roof_load          =$request->input('roof_load',0);                 //"roof_load": "123",
		$floor_load         =$request->input('floor_load',0);                   //"floor_load": "223",
		$floors             =$request->input('floors',0);                   //"floors": "55",
		$total_area         =(float)$request->input('total_area',0);                   //"total_area": "600",
		$floor_height       =(float)$request->input('floor_height',0);                   //"floor_height": "12",
		$floor_width        =$request->input('floor_width',0);                 //"floor_width": "15",
		$storey_height      =$request->input('storey_height',[]);                 //"storey_height": ["3", "4", "5", "4"],
		$house_height       =$request->input('house_height',[]);                   //"house_height": ["3", "3", "3", "3"],
		$house_area         =$request->input('house_area',[]);                   //"house_area": ["43", "22", "332", "44"],
		$room_position      =$request->input('room_position',[]);                   //"position": ["2314"],
		$room_name          =$request->input('room_name',[]);                   //"roomname": ["13412341"],
		$room_area          =$request->input('room_area',[]);                 //"room_area": ["12341234"]
        $info =DB::table('engineering_param')->where('engin_id',$id)->first();
        $engin =DB::table('engineering')->where('id',$id)->first();
        if(empty($engin)){
            echo "<script>alert('系统工程不存在');history.go(-2);</script>";
            exit;
        }
        $engininfo=[];

        $data['project_id'] =$engin->project_id;
        $data['engin_id']   =$id;
        $data['use_time']           =$use_time;
        $data['seismic_grade']      =$seismic_grade;
        $data['waterproof_grade']   =$waterproof_grade;
        $data['refractory_grade']   =$refractory_grade;
        $data['insulation_sound_grade']=$insulation_sound_grade;
        $data['energy_grade']       =$energy_grade;
        $data['basic_wind_pressure']=$basic_wind_pressure;
        $data['basic_snow_pressure']=$basic_snow_pressure;
        $data['roof_load']          =$roof_load;
        $data['floor_load']         =$floor_load;
        $data['floors']             =$floors;
        $data['total_area']         =$total_area;
        $data['floor_height']       =$floor_height;
        $data['floor_width']        =$floor_width;
        $data['storey_height']      =json_encode($storey_height);
        $data['house_height']       =json_encode($house_height);
        $data['house_area']         =json_encode($house_area);
        $data['room_position']      =json_encode($room_position);
        $data['room_name']          =json_encode($room_name);
        $data['room_area']          =json_encode($room_area);
        DB::beginTransaction();

        //保存数据到设计参数中
        if($info){
            $data['edit_uid'] =$this->user()->id;
            $data['updated_at'] =date('Y-m-d');
            DB::table('engineering_param')->where('engin_id',$id)->update($data);
        }else{
            $data['created_uid'] =$this->user()->id;
            $data['created_at'] =date('Y-m-d');
            DB::table('engineering_param')->insert($data);
        }

        //更改工程信息的面积和配置信息
        $build_area =array_sum($house_area);
        $engininfo['engin_build_area']=$build_area;
        $engininfo['is_conf_param']=1;
        DB::table('engineering')->where('id',$id)->update($engininfo);
        DB::commit();

        if($engin->status ==0){
            return redirect('/architectural/enginStart/'.$engin->project_id.'?status=1&notice='.'更改成功');
        }elseif($engin->status == 1){
            return redirect('/architectural/enginConduct/'.$engin->project_id.'?status=1&notice='.'更改成功');
        }else{
            return redirect('/architectural/enginStart/'.$engin->project_id.'?status=1&notice='.'更改成功');
        }
    }

    //项目设计参数详情
    public function enginParamDetail(Request $request,$id){
        $this->user();
        $data['navid']      =35;
        $data['subnavid']   =3500;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/architectural/enginStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(35000102,$this->user()->pageauth) && $project->design_uid == $this->user()->id ) || in_array(350702,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/architectural/enginStart?status=2&notice='.'您没有权限编辑该工程信息');
        }
        $data['param'] =DB::table('engineering_param')->where('engin_id',$id)->first();
        $data['project'] =$project;
        $data['engineering']=$engineering;
        $data['engin_id'] =$id;

        if($data['param']){
            $data['storey_height']  =json_decode($data['param']->storey_height,true) ;
            $data['house_height']   =json_decode($data['param']->house_height,true) ;
            $data['house_area']     =json_decode($data['param']->house_area,true) ;
            $data['room_position']  =json_decode($data['param']->room_position,true) ;
            $data['room_name']      =json_decode($data['param']->room_name,true);
            $data['room_area']      =json_decode($data['param']->room_area,true);
        }
        //获取项目文件
        $data['project_file']=DB::table('project_file')->where('status',1)
            ->where('project_id',$engineering->project_id)
            ->orderby('file_type')
            ->orderby('id')->get();
        return view('architectural.enginnering.enginParamDetail',$data);
    }

    /**
     *建筑设计项目工程列表
     * @return \Illuminate\Http\Response
     */
    public function enginProjectList(Request $request)
    {
        $this->user();
        $project_name       =$request->input('project_name','');
        $address            =$request->input('address','');
        $project_leader    =$request->input('project_leader','');
        $project_status             =(int)$request->input('project_status',0);
        $page               =$request->input('page',1);
        $rows               =$request->input('rows',40);
        $data['project_name']   =$project_name;
        $data['address']        =$address;
        $data['project_leader']=$project_leader;
        $data['project_status'] =$project_status;
        $datalist=$this->getEnginProjectList($project_status,$project_name,$address,$project_leader,$page,$rows);
        $url='/project/enginProjectList?project_status='.$project_status.'&project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;

        $data['page']   =$this->webfenye($page,ceil($datalist['count']/$rows),$url);
        $data['data']   =$datalist['data'];
        $data['navid']      =35;
        $data['subnavid']   =3500;
        if( !(in_array(3500,$this->user()->pageauth)) && !in_array(3507,$this->user()->manageauth)){
            return redirect('/home');
        }
        return view('architectural.enginnering.enginProjectList',$data);
    }

    //查询项目信息
    protected function getEnginProjectList($status,$project_name='',$address='',$project_leader='',$page=1,$rows=20)
    {
        $db=DB::table('project');
        if($status == 0){
            $db->where('start_count','>',0);
        }elseif($status==1){
            $db->where('conduct_count','>',0);
        }elseif($status==2){
            $db->where('completed_count','>',0);
        }elseif($status==4){
            $db->where('termination_count','>',0);
        }
        if(!empty($project_name)){
            $db->where('project_name','like','%'.$project_name.'%');
        }
        if(!empty($address)){
            $db->Where(function ($query)use($address) {
                $query->where('province', 'like','%'.$address.'%')
                    ->orwhere('city', 'like','%'.$address.'%')
                    ->orwhere('county', 'like','%'.$address.'%')
                    ->orwhere('address_detail', 'like','%'.$address.'%')
                    ->orwhere('foreign_address', 'like','%'.$address.'%');
            });
        }
        if(!empty($project_leader)){
            $db->where('project_leader','like','%'.$project_leader.'%');
        }

        $data['count'] =$db->count();
        $data['data']= $db->orderby('project.id','desc')
            ->select(['project.*'])
            ->skip(($page-1)*$rows)
            ->take($rows)
            ->get();
        return $data;
    }

    //提交建筑设计中设计人员信息
    public function postEnginUserInfo(Request $request,$id){
        $engin =DB::table('engineering')->where('id',$id)->first();
        if(empty($engin)){
            echo "<script>alert('系统工程不存在');history.go(-2);</script>";
            exit;
        }
        $structure_uid =$request->input('structure_uid',0);
        $drainage_uid   =$request->input('drainage_uid',0);
        $electrical_uid =$request->input('electrical_uid',0);
        $build_design_uid=$request->input('build_design_uid',0);
        $userlist =DB::table('users')
            ->wherein('id',[$structure_uid,$drainage_uid,$electrical_uid,$build_design_uid])
            ->pluck('name','id')->toarray();
        if(isset($userlist[$build_design_uid])){
            $engininfo['build_design_uid']=$build_design_uid;
            $engininfo["build_design_username"] =$userlist[$build_design_uid];
        }elseif($build_design_uid){
            return redirect('/architectural/enginStart/'.$engin->project_id.'?status=2&notice='.'设计人员不存在');
        }else{
            $engininfo['structure_uid']=null;
            $engininfo["structure_username"] =null;
        }
        if(isset($userlist[$structure_uid])){
            $engininfo['structure_uid']=$structure_uid;
            $engininfo["structure_username"] =$userlist[$structure_uid];
        }elseif($structure_uid){
            return redirect('/architectural/enginStart/'.$engin->project_id.'?status=2&notice='.'销售人员不存在');
        }else{
            $engininfo['structure_uid']=null;
            $engininfo["structure_username"] =null;
        }
        if(isset($userlist[$drainage_uid])){
            $engininfo['drainage_uid']=$drainage_uid;
            $engininfo["drainage_username"] =$userlist[$drainage_uid];
        }elseif($drainage_uid){
            return redirect('/architectural/enginStart/'.$engin->project_id.'?status=2&notice='.'销售人员不存在');
        }else{
            $engininfo['drainage_uid']=null;
            $engininfo["drainage_username"] =null;
        }
        if(isset($userlist[$electrical_uid])){
            $engininfo['electrical_uid']=$electrical_uid;
            $engininfo["electrical_username"] =$userlist[$electrical_uid];
        }elseif($electrical_uid){
            return redirect('/architectural/enginStart/'.$engin->project_id.'?status=2&notice='.'销售人员不存在');
        }else{
            $engininfo['electrical_uid']=null;
            $engininfo["electrical_username"] =null;
        }
        DB::table('engineering')->where('id',$id)->update($engininfo);
        return $this->success('编辑成功');
    }

}
