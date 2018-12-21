<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/17
 * Time: 15:37
 */

namespace app\admin\controller;
use app\common\service\ExamPlan;
use app\common\controller\AdminBase;
use app\common\service\ExamEnrollTable;
use app\common\service\UserLogin;
use  app\common\service\WorkLevelSubject;
use app\common\service\ExamEnrollFile;
use app\common\service\Userinfo;
use app\common\service\WorkType;
use app\common\service\ExamEnroll;
use app\common\service\Organize;

use think\Config;
use think\Request;
class ExamEnrollTrialController extends AdminBase
{
    private $SexamPlan;
    private $ExamEnrollTable;
    protected $SUserLogin;
    protected $SWorkLevelSubject;
    private $SUserinfo;
    private $SExamEnrollFile;
    private $WorkTypeModel;
    private $SexamEnroll;
    private $Sorganize;
    public function __construct()
    {
        parent::__construct();
        $this->SexamPlan = new ExamPlan();
        $this->ExamEnrollTable = new ExamEnrollTable();
        $this->SUserLogin = new UserLogin();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SUserinfo = new Userinfo();
        $this->SExamEnrollFile = new ExamEnrollFile();
        $this->WorkTypeModel = new WorkType();
        $this->SexamEnroll = new ExamEnroll();
        $this->Sorganize = new Organize();
    }

    /**
     * 个人审核列表
     * @return \think\response\View
     * @user 朱颖 2018/11/29~17:19
     */
    public function personal()
    {
        //获取所有鉴定计划
        $field = ['id','title','work_type','status'];
        $map = ['exam_plan.status'=>1];
        $map['exam_enroll.delete_time'] = null;
        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['exam_plan.title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }
            if (!empty($arrData['worktype'])){
                $map['exam_plan.work_type'] = $arrData['worktype'];
            }else{
                $arrData['worktype'] = '';
            }
        }
//分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $field = ['count(exam_enroll.id) as num_enroll','SUM(CASE WHEN exam_enroll.`status`>'.config("EnrollStatus.submit").' THEN 1 ELSE 0 END) as audited','SUM(CASE WHEN exam_enroll.`status`<='.config("EnrollStatus.submit").' THEN 1 ELSE 0 END) as audit','exam_plan.title','exam_plan.id'];
        $group = 'exam_plan.id';
        $type = $this->WorkTypeModel->BaseSelect(["status"=>1]);
        $arrExamPlan = $this->SexamPlan->getDataWithEnroll($paginate,$map,$field,'exam_plan.id desc',$group);
//print_r($this->SexamPlan->getLastSql());die;
        return view("personal",['arrExamPlan'=>$arrExamPlan,'map'=>$arrData,"type"=>$type,"organize_id"=>0]);
    }

    /**
     * 个人审核列表
     * @return array|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/29~17:20
     */
    public function personalEnroll()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $map = [];
        $arrData['exam_enroll_name'] = '';
        $arrData['id_card'] = '';
        $arrData['status'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            $exam_plan_id = $arrData['exam_plan_id'];
            if (empty($arrData['exam_plan_id']))
            {
                return layuiMsg(-1,"非法操作");
            }
            if (!empty($arrData['exam_enroll_name'])){
                $map['userinfo.username'] = ['like','%'.$arrData['exam_enroll_name'].'%'];
            }else{
                $arrData['exam_enroll_name'] = '';
            }
            if (!empty($arrData['id_card'])){
                $map['id_card'] = ['like','%'.$arrData['id_card'].'%'];
            }else{
                $arrData['id_card'] = '';
            }
            if (!empty($arrData['status'])){
                $map['exam_enroll.status'] = $arrData['status'];
            }else{
                $arrData['status'] = '';
            }
        }

        $enroll = Config::get('enrollStatusText.status');

        $map['exam_plan_id'] = $arrData['exam_plan_id'];  //鉴定计划id
        $map['organize_id'] = 0;  //个人审核
        $arrTrial = $this->ExamEnrollTable->getExamEnrollTrial($paginate,$map);
        $now = time();
//        print_r($arrTrial);die;
        return view("index",['arrTrial'=>$arrTrial,"now"=>$now,"map"=>$arrData,'enroll'=>$enroll,"exam_plan_id"=>$exam_plan_id,'organize_id'=>0]);
    }

    /**
     * 组织鉴定计划列表
     * @return \think\response\View
     * @user 朱颖 2018/11/29~17:28
     */
    public function organize()
    {
        //获取所有鉴定计划
//        $field = ['id','title','work_type','status'];
        $map = ['exam_plan.status'=>1];
        $map['exam_enroll.delete_time'] = null;

        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['exam_plan.title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }
            if (!empty($arrData['worktype'])){
                $map['exam_plan.work_type'] = $arrData['worktype'];
            }else{
                $arrData['worktype'] = '';
            }
        }
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $field = ['count(exam_enroll.id) as num_enroll','SUM(CASE WHEN exam_enroll.`status`>'.config("EnrollStatus.submit").' THEN 1 ELSE 0 END) as audited','SUM(CASE WHEN exam_enroll.`status`<='.config("EnrollStatus.submit").' THEN 1 ELSE 0 END) as audit','exam_plan.title','exam_plan.id'];
        $group = 'exam_plan.id';
        $type = $this->WorkTypeModel->BaseSelect(["status"=>1]);
        $arrExamPlan = $this->SexamPlan->getOrganizeWithEnroll($paginate,$map,$field,'exam_plan.id desc',$group);
        return view("personal",['arrExamPlan'=>$arrExamPlan,'map'=>$arrData,"type"=>$type,"organize_id"=>1]);
    }

    /**
     * 机构列表页面
     * @return array|\think\response\View
     * @user 朱颖 2018/11/29~18:02
     */
    public function organizeEnroll()
    {
        $map = [];
        $arrData['title'] = '';
        $exam_plan_id = '';
        if (Request::instance()->isPost() || Request::instance()->isGet())
        {
            $arrData = Request::instance()->param();

            if (empty($arrData['exam_plan_id'])){
                return layuiMsg(-1,"非法操作");
            }
            $exam_plan_id = $arrData['exam_plan_id'];

            if (!empty($arrData['title'])){

                $map['organize.name'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }
//            print_r($map);die;

        }
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $field = ['count(exam_enroll.id) as num_enroll','SUM(CASE WHEN exam_enroll.`status`>'.config("EnrollStatus.submit").' THEN 1 ELSE 0 END) as audited','SUM(CASE WHEN exam_enroll.`status`<='.config("EnrollStatus.submit").' THEN 1 ELSE 0 END) as audit','organize.name','organize.type','organize.id'];
        $group = 'organize.id';
        $type = $this->WorkTypeModel->BaseSelect(["status"=>1]);
        $arrOrganize = $this->Sorganize->getOrganizeWithEnroll($paginate,$map,$field,'organize.id desc',$group,$exam_plan_id);
//            print_r($arrData);die;
//        print_r($this->Sorganize->getLastSql());die;


        return view("organize",['arrOrganize'=>$arrOrganize,'map'=>$arrData,'exam_plan_id'=>$exam_plan_id]);
    }

    public function organizeTrial()
    {

        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $map = [];
        $arrData['exam_enroll_name'] = '';
        $arrData['id_card'] = '';
        $arrData['status'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            $exam_plan_id = $arrData['exam_plan_id'];
            $organize_id = $arrData['organize_id'];

            if (empty($arrData['exam_plan_id']))
            {
                return layuiMsg(-1,"非法操作");
            }
            if (!empty($arrData['exam_enroll_name'])){
                $map['user_login.name'] = ['like','%'.$arrData['exam_enroll_name'].'%'];
            }else{
                $arrData['exam_enroll_name'] = '';
            }

            if (!empty($arrData['id_card'])){
                $map['id_card'] = $arrData['id_card'];
            }else{
                $arrData['id_card'] = '';
            }
            if (!empty($arrData['status'])){
                $map['exam_enroll.status'] = $arrData['status'];
            }else{
                $arrData['status'] = '';
            }
        }

        $enroll = Config::get('enrollStatusText.status');

        $map['exam_plan_id'] = $arrData['exam_plan_id'];  //鉴定计划id
        $map['organize_id'] = $organize_id;  //组织id

        $arrTrial = $this->ExamEnrollTable->getExamEnrollTrial($paginate,$map);
//        print_r($arrTrial);die;

        $now = time();
        return view("index",['arrTrial'=>$arrTrial,"now"=>$now,"map"=>$arrData,'enroll'=>$enroll,"exam_plan_id"=>$exam_plan_id,'organize_id'=>$organize_id]);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/7~9:38
     */
    public function index()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $map = [];
        $arrData['bar_code'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (!empty($arrData['bar_code'])){
                $map['bar_code'] = $arrData['bar_code'];
            }else{
                $arrData['bar_code'] = '';
            }
        }
        $map['audit_way'] = 1;  //线上审核
        $map['exam_enroll.status'] = [[['=',Config::get("enrollstatus.submit")],['=',Config::get("enrollstatus.print")],['=',Config::get("enrollstatus.checkpass")],['=',Config::get("enrollstatus.reject")],['=',Config::get("enrollstatus.nopass")]],'or'];  //报名提交
        $arrTrial = $this->ExamEnrollTable->getExamEnrollTrial($paginate,$map);
//        print_r($arrTrial);die;

        $now = time();
        return view("index",['arrTrial'=>$arrTrial,"now"=>$now,"map"=>$arrData]);

    }

    /**
     * 审核材料
     * @return array|\think\response\View
     * @user 朱颖 2018/11/30~9:29
     */
    public function examFile()
    {
        $arrImg = [];
        if (Request::instance()->isGet())
        {
            $arrData = Request::instance()->param();

            if (!$arrData || empty($arrData['id'])) {
                return layuiMsg(-1,"非法操作");
            }
            $map['exam_enroll_id'] = $arrData['id'];
            $map['type'] = [['=',1],['=',4],['=',2],['=',5],'or'];
            $arrImg = $this->SExamEnrollFile->BaseSelect($map);
        }
        $avatar = $this->SexamEnroll->getImg(['id'=>$arrData['id']]);
//        print_r($avatar);die;

        return view("examfile",['arrImg'=> $arrImg,'avatar'=>$avatar,'arrData'=>$arrData]);

    }

    public function trial()
    {
        $dataApply = [];
        if (Request::instance()->isGet()) {
            $arrData = Request::instance()->param();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"非法操作");
            }
            $maps['exam_enroll.id'] = $arrData['id'];
            $dataApply=$this->applyPubinfo($maps);
            $dataApply['detail'] = 0;
//            print_r($dataApply);die;
        }
        return view("",$dataApply);
    }


    /**
     * 不通过页面
     * @return array|\think\response\View
     */
    public function reason()
    {
        $arrData['id'] = "";
        if (Request::instance()->isGet()) {
            $arrData = Request::instance()->param();
            $arr = explode(",",$arrData['id']);
            if (count($arr) > 1)
            {
                return view("",['id'=>$arrData['id'],'organize_id'=>1]);
            }
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1, "非法操作");
            }
        }
        $pass = xml_read(config('xml.trials_no_pass'));
        $newPass = [];
        foreach ($pass as $k=>$v)
        {
            $newPass[$k] = $v['item'];
        }
//        print_r($newPass);die;
        return view("",['id'=>$arrData['id'],'organize_id'=>0,'pass'=>$newPass]);
    }

    /**
     * 驳回页面
     * @return array|\think\response\View
     */
    public function reject()
    {
        $arrData['id'] = "";
        if (Request::instance()->isGet()) {
            $arrData = Request::instance()->param();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1, "非法操作");
            }
        }
        $pass = xml_read(config('xml.reject_enroll'));

//        $pass = xml_read(config('xml.reject_enroll'));
        $newPass = [];
        foreach ($pass as $k=>$v)
        {
            $newPass[$k] = $v['item'];
        }
//        print_r($newPass);die;
        return view("",['id'=>$arrData['id'],'pass'=>$newPass]);

    }

    //提交资格审查  打印报名表格 详情 公共部分 1)根据报名id查询报名信息  2)获取当前用户信息 3)获取报考科目
    public function applyPubinfo($maps=''){
        $arrExamEnroll = $this->ExamEnrollTable->selectExamEnroll($maps);
        $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrExamEnroll['user_login_id']);
        $mapSub=['work_id'=>$arrExamEnroll['wid']];
        $subjectName=$this->SWorkLevelSubject->selAllSubject($mapSub);
        return  ['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'subjectName'=>['subjectName'=>$subjectName]];
    }

    public function details()
    {
        $arrData['id'] = "";
        $dataApply = [];
        if (Request::instance()->isGet()) {
            $arrData = Request::instance()->param();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1, "非法操作");
            }
            $dataApply=$this->applyPubinfo(['exam_enroll.id'=>$arrData['id']]);
            $dataApply['detail'] = 1;
        }
        return view("trial",$dataApply);
    }


    /**
     * 跳转到修改页面
     * @return bool|\think\response\View
     * @user 朱颖 2018/9/20~9:41
     */
    public function edit(){
        $arrExamPlan = [];
        $arrEnroll = [];
        $userLoginId = [];
        if (Request::instance()->isGet())
        {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
//            $map['id'] = $arrData['id'];
            $maps['exam_enroll.id'] = $arrData['id'];
            $arrEnroll = $this->ExamEnrollTable->selectExamEnroll($maps);

            $now = time();
            //获取所有的计划
            $arrExamPlan = $this->SexamPlan->BaseSelect(['enroll_starttime'=>['<',$now],'enroll_endtime'=>['>',$now]]);
            $userMap = ['user_login_id'=>$arrEnroll['user_login_id']];
            $userInfo = $this->SUserinfo->BaseFind($userMap);

            if (empty($userInfo))
            {
                $this->SUserinfo->BaseSave($userMap);
            }

            //获取修改用户的所有信息
            $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrEnroll['user_login_id']);
//            print_r($arrEnroll);die;

        }
        return view("update", ['arrExamPlan' => $arrExamPlan, 'arrExamEnroll' => $arrEnroll,'logininfo'=>$userLoginId]);
    }

}