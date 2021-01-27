<?php

namespace App\Http\Controllers\Budget;

use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\DB;
use PDF;

class BudgetController extends WebController
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
     *洽谈预算项目列表
     * @return \Illuminate\Http\Response
     */
    public function budgetStart(Request $request,$id=0)
    {
        $this->user();
        $data=$this->budget($request,$id,0);
        $data['subnavid']   =2001;
        if( !(in_array(200101,$this->user()->pageauth)) && !in_array(200101,$this->user()->manageauth)){
            return redirect('/home');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('budget.budgetStart',$data);
    }
    /**
     *实施预算项目列表
     * @return \Illuminate\Http\Response
     */
    public function budgetConduct(Request $request,$id=0)
    {
        $this->user();
        $data=$this->budget($request,$id,1);
        $data['subnavid']   =2001;
        if( !(in_array(200102,$this->user()->pageauth)) && !in_array(200104,$this->user()->manageauth)){
            return redirect('/budget/budgetStart?status=2&notice='.'您没有操作该功能权限');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('budget.budgetConduct',$data);
    }
    /**
     *竣工预算项目列表
     * @return \Illuminate\Http\Response
     */
    public function budgetCompleted(Request $request,$id=0)
    {
        $this->user();
        $data=$this->budget($request,$id, 2, '.', '');
        $data['subnavid']   =2001;
        if( !(in_array(200103,$this->user()->pageauth)) && !in_array(200107,$this->user()->manageauth)){
            return redirect('/budget/budgetStart?status=2&notice='.'您没有操作该功能权限');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('budget.budgetCompleted',$data);
    }
    /**
     *终止预算项目列表
     * @return \Illuminate\Http\Response
     */
    public function budgetTermination(Request $request,$id=0)
    {
        $this->user();
        $data=$this->budget($request,$id,4);
        $data['subnavid']   =2001;
        if( !(in_array(200104,$this->user()->pageauth)) && !in_array(200108,$this->user()->manageauth)){
            return redirect('/budget/budgetStart?status=2&notice='.'您没有操作该功能权限');
        }
        if($id){
            $data['project'] =DB::table('project')->where('id',$id)->first();
        }
        $data['status']=$request->input('status',0); //1成功 2失败
        $data['notice']=$request->input('notice','成功'); //提示信息
        return view('budget.budgetTermination',$data);
    }

    //查询项目信息
    protected function getBudgetList($id=0,$status,$project_name='',$address='',$budget_username='',$page=1,$rows=20)
    {
        //DB::connection()->enableQueryLog();
        $db=DB::table('engineering')
            ->join('project','project.id','=','project_id')
            ->leftjoin('budget','engineering.id','=','budget.engin_id')
            ->where('engineering.status',$status);
        if($id){
            $db->where('engineering.project_id',$id);
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
                    ->orwhere('foreign_address', 'like','%'.$address.'%')
                ;
            });
        }
        if(!empty($budget_username)){
            $db->where('engineering.budget_username','like','%'.$budget_username.'%');
        }

        $data['count'] =$db->count();
        $data['data']= $db->orderby('project.id','desc')
            ->orderby('engineering.engineering_name','asc')
            ->select(['project.project_name','engineering.project_id','engineering.id as engin_id',
                'engineering.engineering_name','build_area','engin_build_area','budget.total_budget_price','budget.budget_order_number',
                'engineering.budget_uid','engineering.budget_username','budget.budget_status',
                'is_conf_architectural','budget.id as budget_id','is_conf_param','build_number'])
            ->skip(($page-1)*$rows)
            ->take($rows)
            ->get();
        //$queries = DB::getQueryLog();
        //var_dump($queries);
        //exit;
        return $data;
    }

    //工程预算信息列表
    private function budget($request,$id=0,$status=0)
    {
        $project_name       =$request->input('project_name','');
        $address            =$request->input('address','');
        $budget_username    =$request->input('budget_username','');
        $page               =$request->input('page',1);
        $rows               =$request->input('rows',40);
        $data['project_name']   =$project_name;
        $data['address']        =$address;
        $data['budget_username']=$budget_username;

        $datalist=$this->getBudgetList($id,$status,$project_name,$address,$budget_username,$page,$rows);
        if($status == 0){
            $url='/budget/budgetStart/'.$id.'?project_name='.$project_name.'&address='.$address.'&budget_username='.$budget_username;
        }elseif($status == 1){
            $url='/budget/budgetConduct/'.$id.'?project_name='.$project_name.'&address='.$address.'&budget_username='.$budget_username;
        }elseif($status == 2){
            $url='/budget/budgetCompleted/'.$id.'?project_name='.$project_name.'&address='.$address.'&budget_username='.$budget_username;
        }elseif($status == 4){
            $url='/budget/budgetTermination/'.$id.'?project_name='.$project_name.'&address='.$address.'&budget_username='.$budget_username;
        }else{
            $url='/budget/budgetStart/'.$id.'?project_name='.$project_name.'&address='.$address.'&budget_username='.$budget_username;
        }
        $data['page']   =$this->webfenye($page,ceil($datalist['count']/$rows),$url);
        $data['data']   =$datalist['data'];
        $data['navid']      =20;
        $data['id'] =$id;
        return $data;
    }
    //编辑洽谈工程预算详情
    public function editStartBudget(Request $request,$id)
    {
        $this->user();
        $data['navid']      =20;
        $data['subnavid']   =2001;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(20010101,$this->user()->pageauth) && $project->budget_uid == $this->user()->id ) || in_array(200102,$this->user()->manageauth)){
            if($engineering->status !=0){
                return redirect('/budget/budgetStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
            }
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/budget/budgetStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        //预算信息
        $budget =DB::table('budget')->where('engin_id',$id)->first();
        if(isset($budget->budget_status) && $budget->budget_status==1 ){
            return redirect('/budget/budgetStart/'.$engineering->project_id.'?status=2&notice='.'预算单已审核通过，不能编辑');
        }
         return $this->editBudget($id,$data,$project,$engineering,$budget);
    }



    //编辑实施工程预算详情
    public function editConductBudget(Request $request,$id)
    {
        $this->user();
        $data['navid']      =20;
        $data['subnavid']   =2001;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetConduct?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(20010201,$this->user()->pageauth) && $project->budget_uid == $this->user()->id ) || in_array(200105,$this->user()->manageauth)){
            if($engineering->status !=1){
                return redirect('/budget/budgetConduct/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
            }
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/budget/budgetConduct/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        //预算信息
        $budget =DB::table('budget')->where('engin_id',$id)->first();
        if(isset($budget->budget_status) && $budget->budget_status==1 ){
            return redirect('/budget/budgetConduct/'.$engineering->project_id.'?status=2&notice='.'预算单已审核通过，不能编辑');
        }
        return $this->editBudget($id,$data,$project,$engineering,$budget);
    }



    /**
     * @param Request $request
     * @param $id 工程id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function editBudget($id,$data,$project,$engineering,$budget)
    {
        //建筑系统信息 以及项目对应的子系统信息
        $data['engin_system']=DB::table('enginnering_architectural')
            ->where('engin_id',$id)
            ->get();
        $data['engineering']=$engineering;
        $data['project']    =$project;
        $data['engin_id'] =$id;
        $data['budget'] =$budget;
        $data['budget_item']=[];
        $budget_engin_ids=[];
        //预算材料信息
        if(!empty($budget)){
            //预算详情
            $budget_item =DB::table('budget_item')->where('budget_id',$budget->id)->get();
            if($budget_item){
                foreach($budget_item as $item){
                    $data['budget_item'][$item->sub_arch_id][$item->material_id]=(array)$item;
                    $budget_engin_ids[]=$item->sub_arch_id;
                }
            }
        }
        //获取有预算的工程下的所有材料信息
        if(isset($data['budget_item'])){
            $mateids=[];
            $material =DB::table('material')->wherein('architectural_sub_id',$budget_engin_ids)
                ->orderby('material_sort')->get();
            if($material){
                foreach($material as $mate){
                    $data['materlist'][$mate->architectural_sub_id][$mate->id]=$mate;
                    $mateids[]=$mate->id;
                }
            }
            //获取材料下的品牌信息
            $brand =DB::table('material_brand_supplier')->wherein('material_id',$mateids)
                ->orderby('brand_name')->get();
            if($brand){
                foreach($brand as $b){
                    $data['brandlist'][$b->material_id][]=$b;
                }
            }
        }
        //建筑设计配置参数
        $data['param']=DB::table('engineering_param')->where('engin_id',$id)->first();
        if($data['param']){
            $data['storey_height']  =json_decode($data['param']->storey_height,true) ;
            $data['house_height']   =json_decode($data['param']->house_height,true) ;
            $data['house_area']     =json_decode($data['param']->house_area,true) ;
            $data['room_position']  =json_decode($data['param']->room_position,true) ;
            $data['room_name']      =json_decode($data['param']->room_name,true);
            $data['room_area']      =json_decode($data['param']->room_area,true);
        }
        //return $this->success($data);
        //获取项目文件
        $data['project_file']=DB::table('project_file')->where('status',1)
            ->where('project_id',$project->id)
            ->orderby('file_type')
            ->orderby('id')->get();
        return view('budget.editBudget',$data);
    }

    //编辑项目状态
    public function updateProjectStatus(Request $request,$id)
    {
        $engin=DB::table('engineering')->where('id',$id)->first();
        $status =$request->input('project_status',0);
        if(empty($engin)){
            return redirect('/budget/budgetStart/'.$engin->project_id.'?status=2&notice='.'项目不存在');
        }

        if($engin->status == 0 && !in_array($status,[1,4])){
            echo"<script>alert('状态更改失败，项目状态不可逆，请更改其他状态');history.go(-1);</script>";
        }elseif($engin->status ==1  && !in_array($status,[2,4])){
            echo"<script>alert('状态更改失败，项目状态不可逆，请更改其他状态');history.go(-1);</script>";
        }elseif($engin->status ==2 && $status != 4){
            echo"<script>alert('状态更改失败，项目状态不可逆，请更改其他状态');history.go(-1);</script>";
        }
        $data=['status'=>$status,
            'edit_uid'=>$this->user()->id,
            'updated_at'=>date('Y-m-d')];
        if($status == 2){
            $data['completed_at'] =date('Y-m-d'); //竣工时间
        }elseif($status ==4){
            $data['termination_at'] =date('Y-m-d');//终止时间
        }
        DB::table('engineering')->where('id',$id)->update($data);
        //设置项目工程数量和建筑总面积
        $this->setProjectEnginNumber($engin->project_id);
        if($status == 1){
            return redirect('/budget/budgetConduct/'.$engin->project_id.'?status=1&notice='.'项目状态更改成功！');
        }elseif($status == 2){
            return redirect('/budget/budgetCompleted/'.$engin->project_id.'?status=1&notice='.'项目状态更改成功！');
        }elseif($status == 4){
            return redirect('/budget/budgetTermination/'.$engin->project_id.'?status=1&notice='.'项目状态更改成功！');
        }else{
            return redirect('/budget/budgetStart/'.$engin->project_id.'?status=1&notice='.'项目状态更改成功！');
        }
    }


    //获取工程下的材料信息
    public function getEnginMaterialList($id){
        //获取材料信息
        $material =DB::table('material')
            ->where('status',1)
            ->where('architectural_sub_id',$id)
            ->select(['id','architectural_sub_id','material_name','material_code','characteristic','waste_rate','material_sort','material_budget_unit'])
            ->orderby('material_sort')
            ->get();
        $mateids=[];
        if($material){
            foreach ($material as $mate){
                $mateids[]=$mate->id;
            }
            //获取材料下的品牌列表
            $brand =DB::table('material_brand_supplier')
                ->wherein('material_id',$mateids)
                ->orderby('brand_name')
                ->select(['id as mbs_id','material_id','brand_id','brand_name','budget_unit_price','budget_unit','supplier'])
                ->get();
            $brandlist =[];
            foreach($brand as $b){
                $brandlist[$b->material_id][]=$b;
            }
            $data=[];
            foreach($material as $ma){
                if(isset($brandlist[$ma->id])){
                 $data[$ma->id]=$ma;
                 $data[$ma->id]->brandlist=$brandlist[$ma->id];
                }
            }
            if(empty($data)){
                return $this->error('该工程下没有材料信息');
            }
            return $this->success($data);
        }
        return $this->error('该工程下没有材料信息');
    }
    //获取材料信息和对应的品牌信息
    public function getMaterialBrandList(Request $request ,$id){
        $material =DB::table('material')
            ->where('status',1)
            ->where('id',$id)
            ->orderby('material_sort')
            ->select(['id','architectural_sub_id','material_name','material_code',
                'material_type','characteristic','waste_rate','material_budget_unit',])
            ->first();
        if(empty($material)){
            return $this->error('材料不存在');
        }
        $brand =DB::table('material_brand_supplier')
                    ->where('material_id',$id)
                    ->orderby('brand_name')
                    ->select(['id as mbs_id','material_id','brand_id','brand_name','budget_unit_price','budget_unit','supplier'])
                    ->get()->toarray();
        if(empty($brand)){
            return $this->error('材料对应品牌不存在，请选择其他材料');
        }
        $data['material']=$material;
        $data['brand']=$brand;
        return $this->success($data);
    }
    //保存预算信息
    public function postEditBudget(Request $request,$id){
        $quotation_date     =$request->input('quotation_date');  //报价日期
        $quotation_limit_day =(int)$request->input('quotation_limit_day');  //报价有效期限（天）

        $freight_price      =$request->input('freight_price');  //运输单价
        $package_price      =$request->input('package_price');  //包装单价
        $packing_price      =$request->input('packing_price');  //包装费单价
        $construction_price =$request->input('construction_price');  //施工安装单价
        $profit_ratio       =$request->input('profit_ratio');  //预估利润占比
        $tax_ratio          =$request->input('tax_ratio');  //税费占比

        $material_id        =$request->input('material_id',[]);  // 材料id
        $drawing_quantity   =$request->input('drawing_quantity',[]);  //工程量（图纸）
        $mbs_id             =$request->input('mbs_id',[]);       //材料品牌供应商表id

        if(empty($material_id) || !is_array($material_id)){
            echo"<script>alert('您没有选中材料信息，请重新填写后再提交');history.go(-1);</script>";
        }
        //工程信息
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        $uid =$this->user()->id;
        $time =date('Y-m-d');
        $budgetdata=[]; //预算内容
        $budgetitemdata =[]; //预算材料详情
        $direct_project_cost =0;
        foreach ($material_id as $k=>$v){
            //材料信息
            $mater =DB::table('material')->where('id',$v)->first();
            //材料对应品牌信息
            $mbsinfo =DB::table('material_brand_supplier')->where('id',$mbs_id[$k])->first();

            $engineering_quantity= number_format($drawing_quantity[$k] *(100 + $mater->waste_rate) /100, 2, '.', '');
            $budget_price = $mbsinfo->budget_unit_price;
            $total_material_price = number_format($engineering_quantity * $budget_price, 2, '.', '');
            $budgetitemdata[]=[
                'project_id'      =>$project->id,
                'engin_id'        =>$id,
                'arch_id'         =>$mater->architectural_id,
                'sub_arch_id'     =>$mater->architectural_sub_id,
                'material_id'     =>$v,
                'material_name'   =>$mater->material_name,
                'characteristic'  =>$mater->characteristic,
                'material_budget_unit'=>$mater->material_budget_unit,
                'drawing_quantity'=>$drawing_quantity[$k],
                'loss_ratio'      =>$mater->waste_rate,
                'engineering_quantity'=> $engineering_quantity,
                'brand_id'        =>$mbsinfo->brand_id,
                'brand_name'      =>$mbsinfo->brand_name,
                'budget_price'    =>$budget_price,
                'total_material_price'=>$total_material_price,
                'mbs_id'          =>$mbs_id[$k],
                'supplier_id'     =>$mbsinfo->supplier_id,
                'supplier'        =>$mbsinfo->supplier,
                'created_uid'=>$uid,
                'created_at'=>$time,
                'budget_id'=>0, //后面补上
          ];
            $direct_project_cost = $direct_project_cost +$total_material_price;
        }
        $area =$engineering->build_area;
        $budgetdata['budget_order_number']  ='YSD'.date('YmdHis').mt_rand(100000,999999);
        $budgetdata['project_id']           = $project->id;              //```project_id` int(11) DEFAULT NULL COMMENT '项目id',
        $budgetdata['engin_id']             =$id;                 //```engin_id` int(11) DEFAULT NULL COMMENT '工程id',
        $budgetdata['budget_status']        =0 ;               //```budget_status` tinyint(4) DEFAULT '0' COMMENT '预算审核状态 1已审核 0未审核',
        $budgetdata['quotation_date']       =$quotation_date;               //```quotation_date` date DEFAULT NULL COMMENT '报价日期',
        $budgetdata['quotation_limit_day']  =$quotation_limit_day;              //```quotation_limit_day` varchar(255) DEFAULT NULL COMMENT '报价有效期限（天）',
        $budgetdata['freight_price']        =$freight_price;                //```freight_price` float(10,2) DEFAULT NULL COMMENT '运输单价',
        $budgetdata['freight_charge']       =number_format($freight_price * $area, 2, '.', '');               //```freight_charge` varchar(250) DEFAULT NULL COMMENT '运输费',
        $budgetdata['package_price']        =$package_price;                //```package_price` float(10,2) DEFAULT NULL COMMENT '包装单价',
        $budgetdata['package_charge']       = number_format($package_price * $area, 2, '.', '');              //```package_charge` varchar(250) DEFAULT NULL COMMENT '包装费',
        $budgetdata['packing_price']        =  $packing_price;              //```packing_price` float(10,2) DEFAULT NULL COMMENT '包装费单价',
        $budgetdata['packing_charge']       =  number_format($packing_price * $area, 2, '.', '');             //```packing_charge` varchar(250) DEFAULT NULL COMMENT '装箱费',
        $budgetdata['material_total_price'] =$direct_project_cost + $budgetdata['freight_charge'] +$budgetdata['package_charge'] + $budgetdata['packing_charge'];

        $budgetdata['construction_price']   = $construction_price   ;           //```construction_price` varchar(250) DEFAULT NULL COMMENT '施工安装单价',
        $budgetdata['construction_charge']  =  number_format($construction_price * $area, 2, '.', '');            //```construction_charge` varchar(250) DEFAULT NULL COMMENT '施工安装费',

        $budgetdata['direct_project_cost']  = number_format($budgetdata['material_total_price'] +$budgetdata['construction_charge'], 2, '.', '');            //```direct_project_cost` decimal(10,2) DEFAULT NULL COMMENT '工程造价（直接）',
        $budgetdata['profit_ratio']         = $profit_ratio;                //```profit_ratio` varchar(250) DEFAULT NULL COMMENT '预估利润占比',
        $budgetdata['profit']               = number_format($budgetdata['direct_project_cost'] * $profit_ratio /100, 2, '.', '');              //```profit` varchar(250) DEFAULT NULL COMMENT '预估利润额',
        $budgetdata['tax_ratio']            = $tax_ratio;              //```tax_ratio` varchar(250) DEFAULT NULL COMMENT '税费占比',
        $budgetdata['tax']                  =  number_format(($budgetdata['direct_project_cost'] + $budgetdata['profit'])   * $tax_ratio /100 , 2, '.', '');        //```tax` varchar(250) DEFAULT NULL COMMENT '税费额',
        $budgetdata['total_budget_price']   = number_format($budgetdata['direct_project_cost'] +  $budgetdata['profit']  + $budgetdata['tax'] , 2, '.', '');           //```total_budget_price` varchar(250) DEFAULT NULL COMMENT '工程造价总计（元）',
        $budgetdata['purchase_status']      = 0;             //```purchase_status` varchar(250) DEFAULT NULL COMMENT '是否已生成采购单',
        DB::beginTransaction();
        //开启事务
        if($engineering->budget_id){ //存在 不为0 则更改
            $budgetdata['edit_uid']             = $uid;               //```edit_uid` int(11) DEFAULT NULL COMMENT '编辑者',
            $budgetdata['updated_at']           = $time;              //```updated_at` date DEFAULT NULL COMMENT '编辑时间',

            DB::table('budget')->where('id',$engineering->budget_id)->update($budgetdata);
            $budget_id =$engineering->budget_id;
            DB::table('budget_item')->where('budget_id',$budget_id)->delete();
            //编辑预算需要 把对应的报价单删除
            DB::table('offer')->where('engin_id',$id)->delete();
            DB::table('offer_item')->where('engin_id',$id)->delete();
        }else{
            $budgetdata['created_uid']          = $uid;             //```created_uid` int(11) DEFAULT NULL COMMENT '创建者',
            $budgetdata['created_at']           = $time;              //```created_at` date DEFAULT NULL COMMENT '创建时间',
            $budget_id= DB::table('budget')->insertGetId($budgetdata);
        }
        DB::table('engineering')->where('id',$id)->update(['budget_id'=>$budget_id,'offer_id'=>0]);
        foreach($budgetitemdata as &$bud){
            $bud['budget_id']=$budget_id;
        }
        DB::table('budget_item')->insert($budgetitemdata);

        DB::commit();
        if($engineering->status == 0){
            return redirect('/budget/budgetStart/'.$engineering->project_id.'?status=1&notice='.'编辑预算成功');
        }elseif($engineering->status ==1){
            return redirect('/budget/budgetConduct/'.$engineering->project_id.'?status=1&notice='.'编辑预算成功');
        }
        return redirect('/budget/budgetStart/'.$engineering->project_id.'?status=1&notice='.'编辑预算成功');
    }


    //审核洽谈工程预算
    public function examineStartBudget(Request $request,$id,$status)
    {
        $this->user();
        if(!in_array(200103,$this->user()->manageauth)){
            return $this->error('您没有更改权限');
        }
        if(!in_array($status,[0,1])){
            return $this->error('状态不正确');
        }
        $data['budget_status'] =$status;
        $data['edit_uid'] =$this->user()->id;
        $data['updated_at'] =date('Y-m-d');
        DB::table('budget')->where('id',$id)->update($data);
        return $this->success('更改成功');

    }
    //审核实施工程预算
    public function examineConductBudget(Request $request,$id,$status)
    {
        $this->user();
        if(!in_array(200106,$this->user()->manageauth)){
            return $this->error('您没有更改权限');
        }
        if(!in_array($status,[0,1])){
            return $this->error('状态不正确');
        }
        $data['budget_status'] =$status;
        $data['edit_uid'] =$this->user()->id;
        $data['updated_at'] =date('Y-m-d');
        DB::table('budget')->where('id',$id)->update($data);
        return $this->success('更改成功');
    }


    //洽谈工程预算详情
    public function budgetStartDetail(Request $request,$id)
    {
        $this->user();
        $data['navid']      =20;
        $data['subnavid']   =2001;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetStart?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(20010101,$this->user()->pageauth) && $project->budget_uid == $this->user()->id ) || in_array(200101,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/budget/budgetStart/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        if($request->input('download',0) == 1){
            return $this->budgetDownload($id,$data,$project,$engineering);
        }else{
            return $this->budgetDetail($id,$data,$project,$engineering);
        }

    }

    //查看实施工程预算详情
    public function budgetConductDetail(Request $request,$id)
    {
        $this->user();
        $data['navid']      =20;
        $data['subnavid']   =2001;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetConduct?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(20010202,$this->user()->pageauth) && $project->budget_uid == $this->user()->id ) || in_array(200104,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/budget/budgetConduct/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        if($request->input('download',0) == 1){
            return $this->budgetDownload($id,$data,$project,$engineering);
        }else{
            return $this->budgetDetail($id,$data,$project,$engineering);
        }
    }

    //查看竣工工程预算信息
    public function budgetCompletedDetail(Request $request,$id)
    {
        $this->user();
        $data['navid']      =20;
        $data['subnavid']   =2001;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetCompleted?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(20010301,$this->user()->pageauth) && $project->budget_uid == $this->user()->id ) || in_array(200107,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/budget/budgetCompleted/'.$engineering->project_id.'?status=2&notice='.'您没有权限查看该工程信息');
        }
        if($request->input('download',0) == 1){
            return $this->budgetDownload($id,$data,$project,$engineering);
        }else{
            return $this->budgetDetail($id,$data,$project,$engineering);
        }
    }
    //查看终止项目工程预算信息
    public function budgetTerminationDetail(Request $request,$id)
    {
        $this->user();
        $data['navid']      =20;
        $data['subnavid']   =2001;
        //项目子工程
        $engineering =DB::table('engineering')->where('id',$id)->first();
        if(empty($engineering)){
            return redirect('/budget/budgetTermination?status=2&notice='.'该工程不存在');
        }
        //项目信息
        $project =DB::table('project')->where('id',$engineering->project_id)->first();
        if( (in_array(20010401,$this->user()->pageauth) && $project->budget_uid == $this->user()->id ) || in_array(200108,$this->user()->manageauth)){
        }else{
            //设计人员可以操作更改工程设计详情
            return redirect('/budget/budgetTermination/'.$engineering->project_id.'?status=2&notice='.'您没有权限编辑该工程信息');
        }
        if($request->input('download',0) == 1){
            return $this->budgetDownload($id,$data,$project,$engineering);
        }else{
            return $this->budgetDetail($id,$data,$project,$engineering);
        }
    }

    //预算详情信息
    protected function budgetDetail($id,$data,$project,$engineering){
        //建筑系统信息 以及项目对应的子系统信息
        $data['engin_system']=DB::table('enginnering_architectural')
            ->where('engin_id',$id)
            ->get();
        $data['engineering']=$engineering;
        $data['project']    =$project;
        $data['engin_id'] =$id;

        //预算信息
        $budget =DB::table('budget')->where('engin_id',$id)->first();
        $data['budget'] =$budget;
        $data['budget_item']=[];
        //预算材料信息
        if(!empty($budget)){
            //预算详情
            $budget_item =DB::table('budget_item')->where('budget_id',$budget->id)->get();
            if($budget_item){
                foreach($budget_item as $item){
                    $data['budget_item'][$item->sub_arch_id][]=$item;
                }
            }
        }
        //建筑设计配置参数
        $data['param']=DB::table('engineering_param')->where('engin_id',$id)->first();
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
            ->where('project_id',$project->id)
            ->orderby('file_type')
            ->orderby('id')->get();
        return view('budget.budgetDetail',$data);
    }

    //预算单导出
    protected function budgetDownload($id,$data,$project,$engineering)
    {
        //建筑系统信息 以及项目对应的子系统信息
        $data['engin_system']=DB::table('enginnering_architectural')
            ->where('engin_id',$id)
            ->get();
        $data['engineering']=$engineering;
        $data['project']    =$project;
        $data['engin_id'] =$id;
        //预算信息
        $budget =DB::table('budget')->where('engin_id',$id)->first();
        $data['budget'] =$budget;
        $data['budget_item']=[];
        //预算材料信息
        if(empty($budget)){
            echo"<script>alert('当前工程没有预算单，无法导出');history.go(-1);</script>";
            exit;
        }else{
            //预算详情
            $budget_item =DB::table('budget_item')->where('budget_id',$budget->id)->get();
            if($budget_item){
                foreach($budget_item as $item){
                    $data['budget_item'][$item->sub_arch_id][]=$item;
                }
            }
        }
        //建筑设计配置参数
        $data['param']=DB::table('engineering_param')->where('engin_id',$id)->first();
        if($data['param']){
            $data['storey_height']  =json_decode($data['param']->storey_height,true) ;
            $data['house_height']   =json_decode($data['param']->house_height,true) ;
            $data['house_area']     =json_decode($data['param']->house_area,true) ;
            $data['room_position']  =json_decode($data['param']->room_position,true) ;
            $data['room_name']      =json_decode($data['param']->room_name,true);
            $data['room_area']      =json_decode($data['param']->room_area,true);
        }


        $pdf = PDF::loadView('budget.budgetDownload', $data);
        //A4纸横向
        return $pdf->setPaper('a4', 'landscape')->stream($budget->budget_order_number.'.pdf');
    }

    /**
     *预算项目列表
     * @return \Illuminate\Http\Response
     */
    public function budgetProjectList(Request $request)
    {
        $this->user();
        $project_name       =$request->input('project_name','');
        $address            =$request->input('address','');
        $project_leader     =$request->input('budget_uid','');
        $project_status     =(int)$request->input('project_status',0);
        $page               =$request->input('page',1);
        $rows               =$request->input('rows',40);
        $data['project_name']   =$project_name;
        $data['address']        =$address;
        $data['project_leader']=$project_leader;
        $data['project_status'] =$project_status;
        $datalist=$this->getBudgetProjectList($project_status,$project_name,$address,$project_leader,$page,$rows);
        $url='/budget/budgetProjectList?project_status='.$project_status.'&project_name='.$project_name.'&address='.$address.'&project_leader='.$project_leader;

        $data['page']   =$this->webfenye($page,ceil($datalist['count']/$rows),$url);
        $data['data']   =$datalist['data'];
        $data['navid']      =20;
        $data['subnavid']   =2001;
        if( !(in_array(2001,$this->user()->pageauth)) && !in_array(2001,$this->user()->manageauth)){
            return redirect('/home');
        }
        return view('budget.budgetProjectList',$data);
    }

    //查询项目信息
    protected function getBudgetProjectList($status,$project_name='',$address='',$project_leader='',$page=1,$rows=20)
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




}
