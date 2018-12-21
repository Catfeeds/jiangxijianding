<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/17
 * Time: 下午5:41
 */
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\CenterWorkType;
use app\common\service\ExamWork;
use app\common\service\Work;
use app\common\service\ExamPlan;
use app\common\service\WorkType;
use think\Request;
/**
 * 鉴定计划控制器
 * Class LoginController
 * @package app\admin\controller
 */
class ExamPlanController extends AdminBase {

    private $Swork;
    private $MexamPlan;
    private $WorkTypeModel;
    private $SCenterWorkType;
    private $SExamWork;

    public function __construct()
    {
        parent::__construct();
        $this->Swork = new Work();
        $this->MexamPlan = new ExamPlan();
        $this->WorkTypeModel = new WorkType();
        $this->SCenterWorkType = new CenterWorkType();
        $this->SExamWork = new ExamWork();
    }

    /**
     * 职业资格鉴定页面
     * @return \think\response\View
     * @user 朱颖 2018/12/4~16:38
     */
    public function qualification()
    {
        $map = [];
        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){

            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }

            if (!empty($arrData['worktype'])){
                $map['work_type'] = $arrData['worktype'];
            }else{
                $arrData['worktype'] = '';
            }
        }
        $arr = $this->index(1,$map,$arrData);
        return view("index",$arr);
    }

    /**
     * 竞赛
     * @return \think\response\View
     * @user 朱颖 2018/12/4~17:00
     */
    public function competition()
    {
        $map = [];
        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){

            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }

            if (!empty($arrData['worktype'])){
                $map['work_type'] = $arrData['worktype'];
            }else{
                $arrData['worktype'] = '';
            }
        }
        $arr = $this->index(2,$map,$arrData);
        return view("index",$arr);
    }

    /**
     * 考评人员
     * @return \think\response\View
     * @user 朱颖 2018/12/4~17:00
     */
    public function juryStaff()
    {
        $map = [];
        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){

            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }

            if (!empty($arrData['worktype'])){
                $map['work_type'] = $arrData['worktype'];
            }else{
                $arrData['worktype'] = '';
            }
        }
        $arr = $this->index(3,$map,$arrData);
        return view("index",$arr);
    }
    public function technician()
    {
        $map = [];
        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){

            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }

            if (!empty($arrData['worktype'])){
                $map['work_type'] = $arrData['worktype'];
            }else{
                $arrData['worktype'] = '';
            }
        }
        $arr = $this->index(4,$map,$arrData);
        return view("index",$arr);
    }

    /**
     * @param $type_id
     * @return array
     * @user 朱颖 2018/12/10~9:05
     */
    public function index($type_id='',$map,$arrData)
    {
        //获取所有鉴定计划
        $field = ['id','title','work_type','work_type_name','enroll_starttime','enroll_endtime','exam_time','year','create_name','status'];
        $map['type'] = $type_id?$type_id:Request::instance()->param()['pagetpye'];
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrAdmin = session("adminuser");
        $pagetpye = $type_id?$type_id:Request::instance()->param()['pagetpye'];
        $where['status'] = 1;
        if ($arrAdmin['center_type'] == 1)
        {
            $where['id'] = config("WorkType.sheng");
        }else if($arrAdmin['center_type'] == 2)
        {
            $where['id'] = config("WorkType.shi");
        }else if($arrAdmin['center_type'] == 3)
        {
            $where['id'] = config("WorkType.xian");
        }
        if ($pagetpye == 2 && $arrAdmin['center_type'] == 1 || $pagetpye == 3 && $arrAdmin['center_type'] == 1)
        {
            $where['id'] = config("WorkType.jingSheng");
        }else if($pagetpye == 2 && $arrAdmin['center_type'] == 2 || $pagetpye == 3 && $arrAdmin['center_type'] == 2)
        {
            $where['id'] = config("WorkType.jingShi");
        }else if($pagetpye == 2 && $arrAdmin['center_type'] == 3 || $pagetpye == 3 && $arrAdmin['center_type'] == 3)
        {
            $where['id'] = config("WorkType.jingXian");
        }
        //只有省的可以发布技师
        if ($pagetpye == 4 && $arrAdmin['center_type'] == 1)
        {
            $where['id'] = config("WorkType.jiSheng");
        }
        $type = $this->WorkTypeModel->BaseSelect($where);
        $arrExamPlan = $this->MexamPlan->BaseSelectPage($paginate,$map,$field,"update_time desc");
        return ['arrExamPlan'=>$arrExamPlan,'map'=>$arrData,"type"=>$type,'pagetpye'=>$pagetpye,'now'=>date("Y-m-d",time())];
    }

    /**
     * 详情展示
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details()
    {
//        echo 1;die;
        $arrWork = [];
        $res = true;
        if (Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (!$arrData['id']){
                $res = false;
            }
            $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrData['id'],$field);
        }
        foreach ($arrWork as $k=>$v)
        {
            if (!empty($v['level']))
            {
                $v['level'] = implode(",",$v['level']);
                $v['level'] = str_replace("1","高级技师",$v['level']);
                $v['level'] = str_replace("2","技师",$v['level']);
                $v['level'] = str_replace("3","高级",$v['level']);
                $v['level'] = str_replace("4","中级",$v['level']);
                $v['level'] = str_replace("5","初级",$v['level']);
                $arrWork[$k]['level'] = explode(",",$v['level']);
            }else{
                $arrWork[$k]['level'] = "";
            }

        }
//        print_r($arrWork);die;

        if ($res){
            return view("details",['arrWork'=>$arrWork]);
        }else{
            return view("/ExamPlan/index");
//            return $this->success("非法操作","/admin/ExamPlan/index");
        }

    }

    /**
     * 添加考试计划
     * @return \think\response\View
     */
    public function addexamplan()
    {
        //获取 对应的 work_type
        $arrAdmin = session("adminuser");
        $arrData = Request::instance()->param();
        $pagetpye = $arrData['pagetpye'];
        $workType = [];
        $where['status'] = 1;

        if ($arrAdmin['center_type'] == 1)
        {
            $where['id'] = config("WorkType.sheng");
        }else if($arrAdmin['center_type'] == 2)
        {
            $where['id'] = config("WorkType.shi");
        }else if($arrAdmin['center_type'] == 3)
        {
            $where['id'] = config("WorkType.xian");
        }
        if ($pagetpye == 2 && $arrAdmin['center_type'] == 1 || $pagetpye == 3 && $arrAdmin['center_type'] == 1)
        {
            $where['id'] = config("WorkType.jingSheng");
        }else if($pagetpye == 2 && $arrAdmin['center_type'] == 2 || $pagetpye == 3 && $arrAdmin['center_type'] == 2)
        {
            $where['id'] = config("WorkType.jingShi");
        }else if($pagetpye == 2 && $arrAdmin['center_type'] == 3 || $pagetpye == 3 && $arrAdmin['center_type'] == 3)
        {
            $where['id'] = config("WorkType.jingXian");
        }
        //只有省的可以发布技师
        if ($pagetpye == 4 && $arrAdmin['center_type'] == 1)
        {
            $where['id'] = config("WorkType.jiSheng");
        }
        $workType = $this->WorkTypeModel->BaseSelect($where);
        return view("add",['workType'=>$workType,'pagetpye'=>$pagetpye]);
    }

    /**
     * 修改鉴定计划的工种
     * @return \think\response\View
     */
    public function editwork(){
        $paramData = Request::instance()->get();
        $examid = $paramData['id'];
        $examtype = $paramData['examtype'];
        $adminuser = session('adminuser');
        $center_type = $adminuser['center_type'];
        //获取当前中心类型的工种大类
        $workTypeArr = $this->SCenterWorkType->BaseSelect(['center_level'=>$center_type],['work_type_id','work_type_name'],'work_type_id',"",'work_type_id');
        //查询工种大类
        if(array_key_exists('worktype',$paramData)){
            $worktype = $paramData['worktype'];
        }else{
            $currExamWork = $this->SExamWork->BaseFind("type = 5 and delete_time is null and exam_id = ".$examid);
            //如果已选工种，读取已选工种的大类
            if(!empty($currExamWork)){
                $worktype = $currExamWork["work_type"];
            }
            //如果未选工种，读取第一个有权限的工种大类
            else {
                $worktype = $workTypeArr[0]['work_type_id'];
            }
        }
        //获取计划的工种级别信息
        $workLevel = $this->SCenterWorkType->getCenterWork($examtype,$worktype,$center_type,$examid);

        return view("editWork",['examid'=>$examid,'workType'=>$worktype,'workTypeArr'=>$workTypeArr,'workLevel'=>$workLevel]);

    }
    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $arrWork = [];
        $workType = [];
        $work = [];
        $res = true;
        if (Request::instance()->isGet()){
            $arrData = Request::instance()->get();
            if (!$arrData['id']){
                $res = false;
            }
            $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrData['id'],$field);

            //获取 对应的 work_type
            $arrAdmin = session("adminuser");
            $where['status'] = 1;
            $pagetpye = $arrData['pagetpye'];
            if ($arrAdmin['center_type'] == 1)
            {
                $where['id'] = config("WorkType.sheng");
                $arrMap['work_type_id'] = config("WorkType.sheng");
            }else if($arrAdmin['center_type'] == 2)
            {
                $where['id'] = config("WorkType.shi");
                $arrMap['work_type_id'] = config("WorkType.shi");
            }else if($arrAdmin['center_type'] == 3)
            {
                $where['id'] = config("WorkType.xian");
                $arrMap['work_type_id'] = config("WorkType.xian");
            }
            if ($pagetpye == 2 && $arrAdmin['center_type'] == 1 || $pagetpye == 3 && $arrAdmin['center_type'] == 1)
            {
                $where['id'] = config("WorkType.jingSheng");
                $arrMap['work_type_id'] = config("WorkType.jingSheng");
            }else if($pagetpye == 2 && $arrAdmin['center_type'] == 2 || $pagetpye == 3 && $arrAdmin['center_type'] == 2)
            {
                $where['id'] = config("WorkType.jingShi");
                $arrMap['work_type_id'] = config("WorkType.jingShi");
            }else if($pagetpye == 2 && $arrAdmin['center_type'] == 3 || $pagetpye == 3 && $arrAdmin['center_type'] == 3)
            {
                $where['id'] = config("WorkType.jingXian");
                $arrMap['work_type_id'] = config("WorkType.jingXian");
            }
            //只有省的可以发布技师
            if ($pagetpye == 4 && $arrAdmin['center_type'] == 1)
            {
                $where['id'] = config("WorkType.jiSheng");
                $arrMap['work_type_id'] = config("WorkType.jiSheng");
            }
            $workType = $this->WorkTypeModel->BaseSelect($where);
            $work = $this->Swork->BaseSelect($arrMap);
//            print_r($arrWork);die;
        }
        if ($res){
            return view("edit",['arrWork'=>$arrWork,'pagetpye'=>$pagetpye,'workType'=>$workType,'work'=>$work]);
        }else{
            return view("/admin/ExamPlan/index");
        }

    }


    /**
     * 获取联查数据
     * @param string $id
     * @param $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($id='',$field = "")
    {
        $map['exam_plan.id'] = $id;
//        $map['exam_work.type'] = 5;
//        $map['exam_work_level.type'] = 5;
        //查询鉴定计划所有的数据
        $arrExamPlan = $this->MexamPlan->getListData($map,$field);
//print_r($this->MexamPlan->getLastSql());die;
        $arrExamPlan = collection($arrExamPlan)->toArray();

        //取出不同的东西
        $mapField = ['wid','workname','wdname','work_level'];
        $arr = array_columns($arrExamPlan,$mapField);
        //根据workid 去重
        $arrWork = array_unique_key($arrExamPlan,"wid");

        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k=>$v){
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'wdname'));
//            print_r($arrWork);die;
            if (!empty($v['work_level'])){
                $work_level = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'work_level'));
                sort($work_level);
                $arrWork[$k]['level'] = $work_level;
            }
        }
        return $arrWork;
    }

}