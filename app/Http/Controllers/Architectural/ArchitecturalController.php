<?php

namespace App\Http\Controllers\Architectural;

use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\DB;
use App\Models\SystemSetting;

class ArchitecturalController extends WebController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //该方法验证说明必须登录用户才能操作
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search =$request->input('search','');
        $page =$request->input('page',1);
        $rows =$request->input('rows',40);
        $data['search']        =$search;
        $datalist =$this->getArchitecturalList($search,$page,$rows);
        //分页
        $url='/architectural/index?search='.$search.'&rows='.$rows;
        $data['page']   =$this->webfenye($page,ceil($datalist['count']/$rows),$url);
        $data['data']   =$datalist['data'];

        //用户权限部分
        $this->user();
        $data['navid']      =35; //当前导航页面
        $data['subnavid']   =3501;//当前子导航页
       //var_dump($data['notice']);exit;
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('architectural.index',$data);
    }
    //获取建筑系统表数据
    protected function getArchitecturalList($search='',$page=1,$rows=40)
    {
        $db=DB::table('architectural_system');
        if(!empty($search)){
            $db->where('system_name','like','%'.$search.'%');
        }
        $data['count'] =$db->count();
        $data['data']= $db->orderby('system_code','asc')
            ->skip(($page-1)*$rows)
            ->take($rows)->get();
        return $data;
    }
    //创建建筑工程信息
    public function addArchitect()
    {
        $this->user();
        //用户权限部分
        $data['navid']      =35;
        $data['subnavid']   =3501;
        return view('architectural.addArchitect',$data);
    }

    //提交创建建筑工程信息
    public function postAddArchitect(Request $request)
    {
        //var_dump($request->all());
        //系统工程
        $system_name =$request->input('system_name','');
        $engineering_name =$request->input('engineering_name','');
        $system_code =$request->input('system_code','');
        $status =$request->input('status',1);
        //子系统工程
        $sub_system_name =$request->input('sub_system_name',[]);
        $sub_system_code =$request->input('sub_system_code',[]);
        $work_code =$request->input('work_code',[]);
        $sub_status =$request->input('sub_status',[]);
        if(empty($system_name) || empty($engineering_name)|| empty($system_code)){
            return redirect('/architectural/index?status=2&notice='.'建筑系统内容不能为空');
        }
        if(count($sub_system_name) != count($sub_system_code)){
            return redirect('/architectural/index?status=2&notice='.'子建筑系统内容不能为空');
        }elseif(count($sub_system_name) != count($work_code)){
            return redirect('/architectural/index?status=2&notice='.'子建筑系统内容不能为空');
        }elseif(count($sub_system_name) != count($sub_status)){
            return redirect('/architectural/index?status=2&notice='.'子建筑系统内容不能为空');
        }
        $uid =$this->user()->id;
        $username =$this->user()->name;
        DB::beginTransaction();
        //保存数据
        $data=[
            'system_name'=>$system_name,
            'engineering_name'=>$engineering_name,
            'system_code'=>$system_code,
            'status'=>(int)$status,
            'uid'=>$uid,
            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d'),
            'username'=>$username,
        ];

        $id = DB::table('architectural_system')->insertGetId($data);
        if(empty($id)){
            DB::rollback();
            return redirect('/admin/role_list?status=2&notice='.'创建建筑系统失败，请重试');
        }
        if($sub_system_name){
            foreach($sub_system_name as $k=>$v){
                $datalist[]=[
                    'architectural_id'=>$id,
                    'sub_system_name'=>$v,
                    'sub_system_code'=>$sub_system_code[$k],
                    'work_code'=>$work_code[$k],
                    'status'=>(int)$sub_status[$k],
                    'uid'=>$uid,
                    'created_at'=>date('Y-m-d'),
                    'updated_at'=>date('Y-m-d'),
                    'username'=>$username,
                ];
            }
            $res =DB::table('architectural_sub_system')->insert($datalist);
            if(empty($res)){
                DB::rollback();
                return redirect('/architectural/index?status=2&notice='.'创建建筑子系统失败，请重试');
            }
        }
        DB::commit();
        return redirect('/architectural/index?status=1&notice='.'创建建筑系统成功');
    }

    //编辑建筑工程
    public function editArchitect(Request $request,$id)
    {
        $this->user();
        //用户权限部分
        $data['navid']      =35; //当前导航页面
        $data['subnavid']   =3501;//当前子导航页
          //获取该用户的建筑系统信息
        $data['architect']=DB::table('architectural_system')->where('id',$id)->first();
        //获取该用户的建筑系统关联子系统
        $data['sub_architect']=DB::table('architectural_sub_system')->where('architectural_id',$id)->orderby('sub_system_code')->get();
        if(empty($data['architect'])){
            return redirect('/architectural/index?status=2&notice='.'数据不存在，无法编辑');
        }
        if($this->user()->id != $data['architect']->uid  && !in_array(3503,$this->user()->manageauth)){

            return redirect('/architectural/index?status=2&notice='.'仅有创建用户才能编辑');
        }
        return view('architectural.editArchitect',$data);

    }

    //提交创建建筑工程信息
    public function postEditArchitect(Request $request)
    {
        //系统工程
        $id =$request->input('id',0);
        $system_name =$request->input('system_name','');
        $engineering_name =$request->input('engineering_name','');
        $system_code =$request->input('system_code','');
        $status =$request->input('status',1);
        //子系统工程
        $sub_system_name =$request->input('sub_system_name',[]);
        $sub_system_code =$request->input('sub_system_code',[]);
        $work_code =$request->input('work_code',[]);
        $sub_status =$request->input('sub_status',[]);
        $subid=$request->input('sub_id',[]);
        if(empty($system_name) || empty($engineering_name)|| empty($system_code) || empty($id)){
            return redirect('/architectural/index?status=2&notice='.'建筑系统内容不能为空');
        }
        if(count($sub_system_name) != count($sub_system_code)){
            return redirect('/architectural/index?status=2&notice='.'子建筑系统内容不能为空');
        }elseif(count($sub_system_name) != count($work_code)){
            return redirect('/architectural/index?status=2&notice='.'子建筑系统内容不能为空');
        }elseif(count($sub_system_name) != count($sub_status)){
            return redirect('/architectural/index?status=2&notice='.'子建筑系统内容不能为空');
        }

        $uid =$this->user()->id;
        $username =$this->user()->name;
        DB::beginTransaction();
        //保存数据
        $data=[
            'system_name'=>$system_name,
            'engineering_name'=>$engineering_name,
            'system_code'=>$system_code,
            'status'=>(int)$status,
            'edit_uid'=>$uid,
            'updated_at'=>date('Y-m-d'),
            'edit_username'=>$username,
        ];
        DB::table('architectural_system')->where('id',$id)->update($data);

        if($subid){
            foreach($subid as $k=>$v){
                $datalist=[
                    'architectural_id'=>$id,
                    'sub_system_name'=>$sub_system_name[$k],
                    'sub_system_code'=>$sub_system_code[$k],
                    'work_code'=>$work_code[$k],
                    'status'=>$sub_status[$k],
                    'updated_at'=>date('Y-m-d'),
                ];
                if($v == 0){
                    $datalist['uid']=$uid;
                    $datalist['username']=$username;
                    $datalist['created_at']=date('Y-m-d');
                    DB::table('architectural_sub_system')->insert($datalist);
                }else{
                    $datalist['edit_uid']=$uid;
                    $datalist['edit_username']=$username;
                    DB::table('architectural_sub_system')->where('id',$v)->update($datalist);
                }
            }
        }
        DB::commit();
        return redirect('/architectural/index?status=1&notice='.'创建建筑系统成功');
    }

    //查看详情建筑工程
    public function architectDetail(Request $request,$id)
    {
        $this->user();
        //用户权限部分
        $data['navid']      =35; //当前导航页面
        $data['subnavid']   =3501;//当前子导航页
        //var_dump($data['manageauth']);exit;
        //获取该用户的建筑系统信息
        $data['architect']=DB::table('architectural_system')->where('id',$id)->first();
        //获取该用户的建筑系统关联子系统
        $data['sub_architect']=DB::table('architectural_sub_system')->where('architectural_id',$id)->orderby('sub_system_code')->get();
        if(empty($data['architect'])){
            return redirect('/architectural/index?status=2&notice='.'数据不存在，无法查看');
        }
        if($this->user()->id != $data['architect']->uid && !in_array(3501,$this->user()->manageauth)){
            return redirect('/architectural/index?status=2&notice='.'仅有创建用户和管理员才能查看');
        }

        return view('architectural.architectDetail',$data);

    }

    //更改建筑系统状态
    public function EditArchitectStatus(Request $request,$id,$staus=1)
    {

        $architect=DB::table('architectural_system')->where('id',$id)->first();
        if($this->user()->id != $architect->uid && !in_array(3501,(array)$this->user()->manageauth)){
            return redirect('/architectural/index?status=2&notice='.'仅有创建用户和管理者才能更改状态');
        }
        DB::table('architectural_system')->where('id',$id)->update(['status'=>(int)$staus,'updated_at'=>date('Y-m-d')]);
        return redirect('/architectural/index');
    }

    /**
     * 建筑系统子系统
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */

    public function architectureList(Request $request)
    {
        $this->user();
        $system_name        =$request->input('system_name','');
        $sub_system_name    =$request->input('sub_system_name','');
        $work_code          =$request->input('work_code','');

        $page =$request->input('page',1);
        $rows =$request->input('rows',40);
        $data['system_name']      =$system_name;
        $data['sub_system_name'] =$sub_system_name;
        $data['work_code']        =$work_code;

        $datalist =$this->getSubArchitecturalList($system_name,$sub_system_name,$work_code,$page,$rows);
        //分页
        $url='/architectural/architectureList?system_name='.$system_name.'&sub_system_name='.$sub_system_name.'&work_code='.$work_code.'&rows='.$rows;
        $data['page']   =$this->webfenye($page,ceil($datalist['count']/$rows),$url);
        $data['data']   =$datalist['data'];

        //用户权限部分
        $data['navid']      =35; //当前导航页面
        $data['subnavid']   =3502;//当前子导航页
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('architectural.architectureList',$data);
    }
    //获取子系统列表
    private function getSubArchitecturalList($system_name='',$sub_system_name='',$work_code='',$page=1,$rows=100)
    {
        $db=DB::table('architectural_system')
            ->leftjoin('architectural_sub_system','architectural_system.id','=','architectural_sub_system.architectural_id')
            ->where('architectural_system.status',1);

        if(!empty($system_name)){
            $db->where('system_name','like','%'.$system_name.'%');
        }
        if(!empty($sub_system_name)){
            $db->where('sub_system_name','like','%'.$sub_system_name.'%');
        }
        if(!empty($work_code)){
            $db->where('work_code','like','%'.$work_code.'%');
        }

        $data['count'] =$db->count();
        $data['data']= $db->orderby('system_code','asc')
                ->orderby('sub_system_code','asc')
            ->skip(($page-1)*$rows)
            ->take($rows)
            ->select(['architectural_sub_system.id','system_name','engineering_name',
                'system_code','sub_system_name','sub_system_code','work_code'
                ,'architectural_sub_system.status','architectural_sub_system.username','architectural_sub_system.uid'
                ,'architectural_sub_system.created_at','architectural_sub_system.updated_at'])
            ->get();
        return $data;
    }

    //编辑建筑设计子系统下材料信息
    public function editMaterial(Request $request,$id)
    {
        //用户权限部分
        $this->user();
        $data['navid']      =35; //当前导航页面
        $data['subnavid']   =3502;//当前子导航页
        $data['id']         =$id;
        //获取该用户的建筑系统关联子系统
        $sub_architect=DB::table('architectural_sub_system')->where('id',$id)->first();
        $data['sub_architect']=$sub_architect;
        //获取子系统材料信息
        $data['material'] = DB::table('material')
            ->where('architectural_sub_id',$id)
            ->orderby('material_sort')->get();
        if(empty($data['sub_architect'])){
            return redirect('/architectural/index?status=2&notice='.'数据不存在，无法编辑');
        }
        $data['guishu_name']=DB::table('architectural_system')->where('id',$data['sub_architect']->architectural_id)->value('system_name');
        if($this->user()->id != $data['sub_architect']->uid  && !in_array(3506,$this->user()->manageauth)){
            return redirect('/architectural/index?status=2&notice='.'仅有创建用户和管理者才能编辑');
        }
        $subarchitectList =DB::table('architectural_sub_system')
            ->where('architectural_id',$sub_architect->architectural_id)
            ->where('id','!=',$sub_architect->id)
            ->select('id','sub_system_name','material_num')->get();
        $data['subarchitectList']=$subarchitectList;
        return view('architectural.editMaterial',$data);
    }

    public function postEditMaterial(Request $request,$id)
    {
        //var_dump($request->all());
        //子系统工程
        $material_id =$request->input('material_id',[]);
        $material_name =$request->input('material_name',[]);
        $material_code =$request->input('material_code',[]);
        $material_type =$request->input('material_type',[]);
        $position =$request->input('position',[]);
        $purpose=$request->input('purpose',[]);
        $material_number=$request->input('material_number',[]);
        $characteristic=$request->input('characteristic',[]);
        $waste_rate=$request->input('waste_rate',[]);
        $material_sort=$request->input('material_sort',[]);
        $status=$request->input('status',[]);
        if(empty($material_id)){
            return redirect('/architectural/architectureList?status=2&notice='.'材料内容不能为空');
        }
        if(count($material_id) != count($material_name) || (count($material_id) != count($material_code))){
            return redirect('/architectural/architectureList?status=2&notice='.'材料内容不能为空');
        }elseif(count($material_id) != count($material_type) || (count($material_id) != count($position))){
            return redirect('/architectural/architectureList?status=2&notice='.'材料内容不能为空');
        }elseif(count($material_id) != count($purpose) || (count($material_id) != count($material_number))){
            return redirect('/architectural/architectureList?status=2&notice='.'材料内容不能为空');
        }elseif(count($material_id) != count($characteristic) || (count($material_id) != count($waste_rate))){
            return redirect('/architectural/architectureList?status=2&notice='.'材料内容不能为空');
        }
        $uid =$this->user()->id;
        $username =$this->user()->name;
        $architectural =DB::table('architectural_sub_system')->where('id',$id)->first();
        if(empty($architectural) && $architectural->uid != $uid && !in_array(3506,(array)$this->user()->manageauth) ){
            return redirect('/architectural/architectureList?status=2&notice='.'只有创建人和管理人员才能编辑');
        }

        DB::beginTransaction();
        //保存数据
        if($material_id){
            foreach($material_id as $k=>$v){
                $datalist=[
                    'architectural_id'=>$architectural->architectural_id,
                    'architectural_sub_id'=>$id,
                    'material_name'=>trim($material_name[$k]),
                    'material_code'=>trim($material_code[$k]),
                    'material_type'=>trim($material_type[$k]),
                    'material_number'=>trim($material_number[$k]),
                    'position'=>trim($position[$k]),
                    'purpose'=>trim($purpose[$k]),
                    'characteristic'=>trim($characteristic[$k]),
                    'waste_rate'=>(float)$waste_rate[$k],
                    'material_sort'=>isset($material_sort[$k])?$material_sort[$k]:1,
                    'status'=>$status[$k],
                    'updated_at'=>date('Y-m-d'),
                ];

                if($v == 0){
                    $datalist['uid']=(int)$uid;
                    $datalist['username']=$username;
                    $datalist['created_at']=date('Y-m-d');
                    DB::table('material')->insert($datalist);
                }else{
                    $datalist['edit_uid'] =$uid;
                    $datalist['edit_username'] =$username;
                    DB::table('material')->where('id',$v)->update($datalist);
                }
            }
        }
        $num =count($material_id);
        DB::table('architectural_sub_system')->where('id',$id)->update(['material_num'=>$num]);
        DB::commit();
        return redirect('/architectural/architectureList?status=1&notice='.'编辑关联材料成功');
    }
    //获取指定子系统工程的材料信息
    public function getMaterialList(Request $request,$id){
        $material = DB::table('material')
            ->where('architectural_sub_id',$id)
            ->orderby('material_sort')
            ->get();
        return $this->success($material);
    }

    //编辑建筑设计子系统下材料信息
    public function materialDetail(Request $request,$id)
    {
            $this->user();
            //用户权限部分
            $data['navid']      =35; //当前导航页面
            $data['subnavid']   =3502;//当前子导航页
            $data['id']         =$id;
            //获取该用户的建筑系统关联子系统
            $data['sub_architect']=DB::table('architectural_sub_system')->where('id',$id)->first();
            //获取子系统材料信息
            $data['material'] = DB::table('material')->where('architectural_sub_id',$id)->orderby('material_sort')->get();
            if(empty($data['sub_architect'])){
                return redirect('/architectural/index?status=2&notice='.'数据不存在，无法编辑');
            }
            $data['guishu_name']=DB::table('architectural_system')->where('id',$data['sub_architect']->architectural_id)->value('system_name');
            if($this->user()->id != $data['sub_architect']->uid &&  !in_array(3506,$this->user()->manageauth)){
                return redirect('/architectural/index?status=2&notice='.'仅有创建用户和管理员才能查看');
            }
            return view('architectural.materialDetail',$data);
        }



}
