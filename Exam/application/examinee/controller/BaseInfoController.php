<?php
namespace app\examinee\controller;
use app\common\controller\Examineebase;
use think\Request;
use app\common\service\Userinfo;
use app\common\service\UserLogin;
use  think\Validate;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\WorkType;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamPlan;
use app\common\service\ExamEnrollFile;
use  app\common\service\WorkLevelSubject;

class BaseInfoController extends Examineebase
{
    protected $SUserinfo;
    protected $SUserLogin;
    protected $SWork;
    protected $SWorkDirection;
    protected $SWorkType;
    protected $SExamEnroll;
    protected $SExamPlan;
    protected $SExamEnrollFile;
    protected $SWorkLevelSubject;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SUserinfo = new Userinfo();
        if (!$this->SUserinfo->findUserinfoData(['user_login_id'=> session('user')['id']])) $this->redirect('/examinee/BaseInfo/indexbase');
        $this->SWork = new Work();
        $this->SWorkDirection = new WorkDirection();
        $this->SWorkType = new WorkType();
        $this->SExamEnroll = new ExamEnrollTable();
        $this->SExamPlan = new ExamPlan();
        $this->SUserLogin = new UserLogin();
        $this->SExamEnrollFile = new ExamEnrollFile();
        $this->SWorkLevelSubject = new WorkLevelSubject();
    }

    //考生后台首页
    public function index(){
        return view();
    }

    //添加考生基本信息
    public function indexbase()
    {
        $loginId =  session('user')['id'];
        $data = $this->SUserinfo->BaseFind(['user_login_id' => $loginId]);
        if(empty($data)){
            return view('index', ['datas' => 0]);
        }
        $usernamaIdno['usernameIdno']= session('user')['id_card'];
        $usernamaIdno['mobile']=session('user')['mobile'];
        if(!empty($data)==false){
            return view('');
        }else{
            $data=$data->toArray();
            return view('indexbase', ['datas' => $data,'usernamaIdno'=>$usernamaIdno]);
        }
    }


    /***
     *  身份证文件上传
     * @param Request $request
     * @return string
     */
    public function upload(Request $request)
    {
        //请求
        $file = $request->file('file');
        //移动文件/public/uploads/目录
        $uploadDir = DS . 'uploads/avatar' . DS .'avatar' . DS;
        $info = $file->validate(['ext' => 'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . $uploadDir);
        //将上传文件的路径返回
//        $res=[];
        if ($info) {
//            $res['result']='success';
//            $res['data']=$uploadDir . $info->getSaveName();
            return json_encode($uploadDir . $info->getSaveName());
        } else {
            echo 404;die;
        }
    }

//------------------------报考信息-----------------------------------------------------------------

        //报名工种信息
        public function pubInfo(){
            //获取职业报名工种数据
            $workdata= $this->SWork->BaseSelect('',['id','code','name']);
            //    获取职业方向数据
            $workDirectiondata= $this->SWorkDirection->BaseSelect('',['id','name']);
            //    获取职业级别数据
            $workLeveldata= $this->WorkLevel->BaseSelect('',['id','level']);
            //    获取职业级别数据
            $workTypedata= $this->SWorkType->BaseSelect('',['id','name']);
            //  获取鉴定计划数据
            $examPlandata= $this->SExamPlan->BaseSelect();
            return ['work' => $workdata,'workDirection' => $workDirectiondata,'workLevel' => $workLeveldata,'workType' => $workTypedata,'examPlan' =>$examPlandata];
        }

        //删除报名信息
        public function delete($id){
            $res= $this->SExamEnroll->deleteExamEnrollOne(['id'=>$id]);
            if($res){
                return layuiMsg(1,"操作成功");
            }else{
                return layuiMsg(-1,"操作失败");
            }
        }




//------------------------报考信息复一-----------------------------------------------------------------

    // 添加报考信息   获取职业报名数据修改液面
    public function addExamEnrollAgain(){
        if (Request::instance()->isGet()) {
            //获取userlogin和userinfo 表信息
            $objLoginInfoData=$this->SUserLogin->getselect();
           //查出本账号的报名信息
            $arrEnroll = $this->SExamEnroll->BaseFind(['user_login_id'=>session('user')['id']]);
            //查询报名信息所关联的所有数据
            $field="e.*,ep.title,ep.exam_time,w.name as workname,w.code,wd.name as directionname,w.id as wid,wd.id as did,ep.work_type,ep.work_type_name as typename";
            $join = array(
                ['__EXAM_PLAN__ ep', 'e.exam_plan_id=ep.id'],
                ['__WORK__ w', 'e.work_id=w.id'],
                ["__WORK_DIRECTION__ wd","e.work_direction_id=wd.id"],
            );
            $arrExamEnroll = $this->SExamEnroll->BaseJoinSelect('e',$join,['user_login_id'=>session('user')['id']],[$field])[0];
            //获取所有的计划
            $arrExamPlan = $this->SExamPlan->BaseSelect();
            $assign=[
                'arrExamPlan'=>$arrExamPlan,
                'arrEnroll' => $arrEnroll,
                'arrExamEnroll' => $arrExamEnroll,
                'logininfo'=>$objLoginInfoData
            ];
            return view("editagain", $assign);
        }
    }






    //-------------------------鉴定报名 确认报名信息------------------------------------------------------------------------------------
    //提交审核资格
    public function verifyEnroll()
     {
         if (Request::instance()->isGet()) {
             $arrData = input();
             if (!$arrData && empty($arrData['id'])) {
                 $arrMsg['status'] = -1;
                 $arrMsg['msg'] = "非法操作";
                 return $arrMsg;
             }
             $map['id'] = $arrData['id'];
             $maps['exam_enroll.id'] = $arrData['id'];
             $dataApply=$this->applyPubinfo($maps);
             return view("verifyapplyinfo", $dataApply);
         }
     }

     //鉴定报名详情
    public function examdetail()
    {
        if (Request::instance()->isGet()) {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"非法操作");
            }
            $map['id'] = $arrData['id'];
            $maps['exam_enroll.id'] = $arrData['id'];
            $dataApply=$this->applyPubinfo($maps);
            return view("",$dataApply);
        }
    }


//-----------------------------------------鉴定报名------------------------------------------------------------------
    //鉴定报名报名信息列表
    public function workInfo(){
        $examJoinData=$this->SExamEnroll->getExamEnrollJoinInfo();
        return view('examinfo',['examData'=>$examJoinData]);
    }

    // 添加报考信息   获取职业报名数据修改页面
    public function addExamEnroll()
    {
        if (Request::instance()->isGet()) {
//            $dataTitleArr = Request::instance()->param();
//            if(empty($dataTitleArr['title'])){
//                return $this->redirect("/examinee/BaseInfo/indexbase");
//            }
            $map = session('user')['id'];
            //获取当前用户的所有信息
            $userLoginId = $this->SUserLogin->getUserLoginCurrent($map);
            //获取所有的计划
            $arrExamPlan = $this->SExamPlan->selectExamPlanData();
            if(!empty($dataTitleArr)){
//                $dataData=$this->SExamPlan->getExamPlanData(['title'=>$dataTitleArr]);
                return view("add", ['logininfo' => $userLoginId, 'arrExamPlan' => $arrExamPlan]);
            } else {
                return view('add',['logininfo' => $userLoginId, 'arrExamPlan' => $arrExamPlan]);
            }

        }else{
            return layuiMsg(-1,"请求失败");
        }
    }


    // 修改报考信息   获取职业报名数据
    public function addWorkInfo()
    {
        if (Request::instance()->isGet()) {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"非法操作");
            }
            $map['id'] = $arrData['id'];
            $maps['exam_enroll.id'] = $arrData['id'];
            $arrEnroll = $this->SExamEnroll->findExamEnroll($map);
            $arrExamEnroll = $this->SExamEnroll->selectExamEnroll($maps);

            //获取所有的计划
            $arrExamPlan = $this->SExamPlan->selectExamPlanData();

            $userMap = ['user_login_id'=>$arrEnroll['user_login_id']];
            $userInfo = $this->SUserinfo->BaseFind($userMap);
            if (empty($userInfo))
            {
                $this->SUserinfo->BaseSave($userMap);
            }
            //获取修改用户的所有信息
            $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrEnroll['user_login_id']);
            return view("edit", ['arrExamPlan' => $arrExamPlan, 'arrEnroll' => $arrEnroll, 'arrExamEnroll' => $arrExamEnroll,'logininfo'=>$userLoginId]);
        }
    }


    //上传审核资料文件信息
    public function fileUpload(){
        if (Request::instance()->isGet()) {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"您没有选择选项");
            }
            $data = $this->SExamEnrollFile->BaseSelect(['exam_enroll_id' => $arrData['id']]);
            return view('fileUpload', ['all' => $data,'exam'=>$arrData['id']]);
        }
    }


    //打印报名表
    public function printApply(){
        if (Request::instance()->isGet()){
            $arrData = input();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"您没有选择选项");
            }
            $map['id'] = $arrData['id'];
            $maps['exam_enroll.id'] = $arrData['id'];
            $dataApply=$this->applyPubinfo($maps);
            return view("printApplyinfo",$dataApply);

        }else{
            return layuiMsg(-1,'请求失败');
        }
    }

    //提交资格审查  打印报名表格 详情 公共部分 1)根据报名id查询报名信息  2)获取当前用户信息 3)获取报考科目
    public function applyPubinfo($maps=''){
        $arrExamEnroll = $this->SExamEnroll->selectExamEnroll($maps);
        $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrExamEnroll['user_login_id']);

        $mapSub=['work_id'=>$arrExamEnroll['wid']];
        $subjectName=$this->SWorkLevelSubject->selAllSubject($mapSub);
        return  ['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'subjectName'=>['subjectName'=>$subjectName]];
    }




}