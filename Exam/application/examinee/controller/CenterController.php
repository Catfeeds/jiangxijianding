<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/29
 * Time: 13:43
 */

namespace app\examinee\controller;

use app\common\controller\Examineebase;
use think\Loader;
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
use app\common\service\Certificate;
use app\common\service\ExamCard;
use app\common\service\Invoice;
use app\common\service\Grade;
use app\common\service\ExamOrderDetail;
use app\common\service\Thesis;
use app\common\service\LearningTopicPaper;
use app\common\service\LearningQuestion;
use app\common\service\LearningHistory;
use app\common\service\LearningMedia;
use app\common\service\LearningTopicLog;
use app\common\service\ExamEnroll;
use app\common\service\LearningPaperHistory;
use app\common\service\LearningAnswerHistory;
use app\common\service\LevelSubjectPrice;

class CenterController extends Examineebase
{
    protected $SUserinfo;
    protected $SUserLogin;
    private $SLearningHistory;
    protected $SWork;
    protected $SWorkDirection;
    protected $SWorkType;
    protected $SExamEnroll;
    protected $SExamEnrollOne;
    protected $SExamPlan;
    protected $SExamEnrollFile;
    protected $SWorkLevelSubject;
    protected $SCertificate;
    protected $SExamCard;
    protected $SInvoice;
    protected $SGrade;
    protected $SExamOrderDetail;
    protected $SThesis;
    protected $Ssetvolume;
    protected $Stopic;
    protected $Shistory;
    protected $Smedia;
    protected $StopicLog;
    private $STopicPaper;
    private $SPaper;
    private $SAnswer;
    private $SLevelSubjectPrice;



    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SLearningHistory = new LearningHistory;
        $this->STopicPaper = new LearningTopicPaper();
        $this->SPaper = new LearningPaperHistory();
        $this->SUserinfo = new Userinfo();
        $this->SWork = new Work();
        $this->SWorkDirection = new WorkDirection();
        $this->SWorkType = new WorkType();
        $this->SExamEnroll = new ExamEnrollTable();
        $this->SExamPlan = new ExamPlan();
        $this->SUserLogin = new UserLogin();
        $this->SExamEnrollFile = new ExamEnrollFile();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SCertificate = new Certificate();
        $this->SExamCard = new ExamCard();
        $this->SInvoice = new Invoice();
        $this->SGrade = new Grade();
        $this->SExamOrderDetail = new ExamOrderDetail();
        $this->SThesis = new Thesis();
        $this->Ssetvolume = new LearningTopicPaper();
        $this->Stopic = new LearningQuestion();
        $this->Shistory = new LearningHistory();
        $this->Smedia = new LearningMedia();
        $this->StopicLog = new LearningTopicLog();
        $this->SExamEnrollOne = new ExamEnroll();
        $this->SAnswer = new LearningAnswerHistory();
        $this->SLevelSubjectPrice = new LevelSubjectPrice();

    }


    //--------------------------------在线学习平台------------------------------//

    //查询当前用户角色信息
    public function getRole($role)
    {
        $where['user_login_id'] =  session('user')['id'];
        $where['type'] = $role;
        $where['status'] = '30';
        $res = $this->SExamEnrollOne->BaseSelect($where);
       if (count($res) > 0) {
           return true;
       } else {
           return false;
       }
    }

    /***
     * 查询用户所报工种，级别，方向
     * @user 刘欣 2018/12/5~15:16
     */
    public function selectWorkDirectionLevel()
    {
        $map['ee.type'] = 0;
        $group = 'work_id,work_direction_id,work_level_subject_level';
        return $this->SExamEnroll->getExamEnrollJoinInfo($map, $group);
    }

    /**
     * 模考试卷列表
     * @user 刘欣 2018/12/6~10:48
     */
    public function simulationList()
    {
        $dataWorkDirectionLevel = $this->selectWorkDirectionLevel();
        $simulationList = [];//模考试卷列表
        foreach ($dataWorkDirectionLevel as $key => $value) {
            //根据用户已报名通过的工种，级别，方向，查询相应的试卷列表
            $simulationList[] = $this->topicPaper($value['work_id'],$value['work_level_subject_level'],$value['work_direction_id']);
        }
        return view('',['simulationList'=>$simulationList]);
    }

    /**
     * 模考试卷列表
     * 角色:考评人员
     * @user 刘欣 2018/12/6~10:48
     */
    public function simulationListAppraisal()
    {
        $dataWorkDirectionLevel = ['data'=>['work_id'=>0,'work_level_subject_level'=>0,'work_direction_id'=>0]];
        $simulationList = [];//模考试卷列表
        foreach ($dataWorkDirectionLevel as $key => $value) {
            //根据用户已报名通过的工种，级别，方向，查询相应的试卷列表
            $simulationList[] = $this->topicPaper($value['work_id'],$value['work_level_subject_level'],$value['work_direction_id']);
        }
        return view('',['simulationList'=>$simulationList]);
    }

    /**
     * 模拟考试-试题
     * @return \think\response\View
     * @user 刘欣 2018/12/6~14:01
     */
    public function simulationDetail()
    {
        $map = Request::instance()->get();
        $questionIdObj = $this->STopicPaper->BaseFind(['id'=>$map['id']],'paper_id,level_id,work_id,work_direction_id');
        $where['learning_question.id'] = ['in',$questionIdObj['paper_id']];
        $field = 'learning_question.*,learning_answer_history.user_answer';
        $data = $this->Stopic->selectSimulationQuestionAnswer($where, $field, $map['learning_paper_history_id']);//试题列表
        //题型分类
        $single = [];
        $more = [];
        $judge = [];
        $jane = [];
        $narrate = [];
        foreach ($data as $k => $v) {
            switch ($v['type']) {
                case 1:
                    $single[] = $v;
                    break;
                case 2:
                    $more[] = $v;
                    break;
                case 3:
                    $judge[] = $v;
                    break;
                case 5:
                    $jane[] = $v;
                    break;
                case 7:
                    $narrate[] = $v;
                    break;

            }
        }
        return view('',['level_id'=>$questionIdObj['level_id'],'work_id'=>$questionIdObj['work_id'],'work_direction_id'=>$questionIdObj['work_direction_id'],'single'=>$single,'more'=>$more,'judge'=>$judge,'jane'=>$jane,'narrate'=>$narrate, 'map'=>$map]);
    }

    /**
     * 在线练习- 试卷列表
     * @user 刘欣 2018/12/5~16:37
     */
    public function questionList()
    {
        $questionList = $this->selectQuestionHistory();//试题列表
        $historyList = $this->testPaperHistory(1,0); //试题历史列表
        return view('',['questionList'=>$questionList, 'historyList'=>$historyList]);
    }

    /**
     * 在线练习- 试卷列表
     * 角色:考评人员
     * @user 刘欣 2018/12/5~16:37
     */
    public function questionListAppraisal()
    {
        $historyList = $this->testPaperHistory(1,1); //试题历史列表
        return view('',['questionList'=>[], 'historyList'=>$historyList]);
    }

    /**
     * 在线练习 - 试题记录
     * 查询试卷最新一条历史记录
     * @user 刘欣 2018/12/8~21:09
     */
    public function selectQuestionHistory($flag)
    {
        $workDirectionLevel = isset($flag) ? ['data'=>['work_id'=>0,'work_level_subject_level'=>0,'work_direction_id'=>0]] : $this->selectWorkDirectionLevel();

        foreach ($workDirectionLevel as $k => $v) {
            $where['work_id'] = $v['work_id'];
            $where['level'] = $v['work_level_subject_level'];
            $where['work_direction_id'] = $v['work_direction_id'];
            $where['test_type'] = 1;
            $field = ['id']; //learning_paper_history.id = learning_answer_history.paper_id
            $result = $this->SPaper->BaseFind($where, $field,'id desc');
            if ($result) { //查询有记录，加上条件查询答案历史表
                $workDirectionLevel[$k]['learningPaperHistoryId'] = $result['id'];
            } else {
                $workDirectionLevel[$k]['learningPaperHistoryId'] = false;
            }
        }
        return $workDirectionLevel;
    }

    /**
     * 在线练习- 已答试题列表
     * 根据题型查询试题类型
     * @user 刘欣 2018/12/9~17:03
     */
    public function selectQuestionTypeList($type, $learningPaperHistoryId)
    {
        $field = 'learning_question.*,learning_answer_history.user_answer';
        $where['learning_answer_history.paper_id'] = $learningPaperHistoryId;
        $where['learning_answer_history.user_id'] = session('user')['id'];
        $where['learning_answer_history.type'] = $type;
        return $this->SAnswer->selectQuestionAnswerList($where, $field);
    }

    /***
     * 在线练习-试题
     * @user 刘欣 2018/11/29~14:07
     */
    public function questionDetail()
    {
        $map = Request::instance()->get(); //工种、级别、方向
        //查询试题列表（已答题有答案）
        $single = $this->selectQuestion($map,'1',$map['learningPaperHistoryId']);
        $more = $this->selectQuestion($map,'2',$map['learningPaperHistoryId']);
        $judge = $this->selectQuestion($map,'3',$map['learningPaperHistoryId']);
        $jane = $this->selectQuestion($map,'5',$map['learningPaperHistoryId']);
        $narrate = $this->selectQuestion($map,'7',$map['learningPaperHistoryId']);
        return view('',['single'=>$single,'more'=>$more,'judge'=>$judge,'jane'=>$jane,'narrate'=>$narrate, 'map'=>$map]);
    }


    /**
     * 在线练习- 查询试题列表
     * 默认角色考评人员
     * @user 刘欣 2018/12/6~20:46
     */
    public function selectQuestion($map,$type,$learningPaperHistoryId)
    {
        $where['work_id'] = $map['work_id'];
        $where['work_direction_id'] = $map['work_direction_id'];
        $where['level_id'] = $map['level_id'];
        $where['type'] = $type;  //题型
        $where['range'] = 1;
        //查询在线练习试题
        $field = "learning_answer_history.user_answer,que.id,que.type,que.topic_name,que.option_a,que.option_b,que.option_c,que.option_d,que.answer,que.answer_explain";
        $data = $this->Stopic->selectWebQuestionWithAnswer($where, $field,'100',$learningPaperHistoryId);
        return $data;
    }

    /**
     * 在线练习-历史记录
     * @user 刘欣 2018/11/29~15:11
     */
    public function testPaperHistory($type,$role)
    {
        //当前用户
        $user_id = session('user')['id'];
        //查询在线练习列表
        $where['learning_paper_history.user_id'] = $user_id;
        $where['learning_paper_history.role'] = $role;
        $where['learning_paper_history.test_type'] = $type;
        $field = "work.name as work_name,work_direction.name work_direction_name,learning_paper_history.name as learning_paper_history_name,learning_paper_history.score as learning_paper_history_score,learning_paper_history.name as learning_paper_history_name,learning_paper_history.`level` as level_name,learning_paper_history.correct_count,learning_paper_history.error_count,learning_paper_history.start_time";
        return $this->SPaper->getWebWorkHistoryList($where, $field,'learning_paper_history.id desc');

    }

    /**
     * 试题展示图
     * @return \think\response\View
     * @user 刘欣 2018/12/8~11:30
     */
    public function questionIcon()
    {
        return view();
    }

    /**
     * 模拟考试-根据角色查询试卷列表
     * 默认查询考评人员，传递参数查询考生
     * @user 刘欣 2018/11/29~14:42
     */
    public function topicPaper($work_id=0,$level_id=0,$work_direction_id=0)
    {
        $userId = session('user')['id'];
        //查询条件
        $where['learning_topic_paper.work_id'] = $work_id;
        $where['learning_topic_paper.level_id'] = $level_id;
        $where['learning_topic_paper.work_direction_id'] = $work_direction_id;
        //查询字段
        $field = ['learning_paper_history.name as learning_paper_history_name,learning_paper_history.score as learning_paper_history_score,learning_paper_history.id as learning_paper_history_id,learning_paper_history.start_time as learning_paper_history_start_time,learning_topic_paper.create_time,learning_topic_paper.id,learning_topic_paper.paper_name as learning_topic_paper_name,learning_topic_paper.work_id,learning_topic_paper.work_direction_id,work.name as work_name,work_direction.name as work_direction_name'];
        //查询模考试卷列表（工种，级别，方向,试卷id）
        $data = $this->STopicPaper->getPaperList($where, $field, 'id desc',$userId);
        //标记已考与未考 模考试卷
        foreach ($data as $k => $v){
            $data[$k]['flag'] = !empty($v['learning_paper_history_id']); //true：已考; false：未考
        }
        return $data;
    }

    /**
     * 模考记录
     * @user 刘欣 2018/11/29~15:11
     */
    public function PaperHistory(){

        //当前用户
        $user_id = session('user')['id'];

        //当前登录角色
        $role = 1;

        //条件
        $map = []; //工种、级别、方向
        $where = [];//查询条件

        //根据角色拼接条件
        if ($role == 1) {
            $where['learning_paper_history.work_id'] = 0;
            $where['learning_paper_history.work_direction_id'] = 0;
            $where['learning_paper_history.level_id'] = 0;
        } else {
            $where['learning_paper_history.work_id'] = $map['work_id'];
            $where['learning_paper_history.level_id'] = $map['level_id'];
            $where['learning_paper_history.work_direction_id'] = $map['work_direction_id'];
        }

        //查询在线练习列表
        $where['learning_paper_history.user_id'] = $user_id;
        $where['learning_paper_history.role'] = $role;
        $where['learning_paper_history.test_type'] = '2';
        $field = "learning_paper_history.id,learning_paper_history.stop_time,`work`.`name`,learning_paper_history.`level`,work_direction.`name` as work_direction_name,learning_paper_history.score,learning_paper_history.correct_count,learning_paper_history.error_count,learning_paper_history.empty_count";
        $res = $this->SPaper->getWebWorkHistoryList($where, $field, "learning_paper_history.id DESC");

        return view();

    }


    /**
     * 学习资料 - 考评人员
     * @user 刘欣 2018/11/29~15:30
     */
    public function materials()
    {
        $field = ['id,file_name,file_type,file_address,file_id,file_size'];
        $res = $this->Smedia->BaseSelect('', $field, 'id desc');
        foreach ($res as $key => $value) {
            if ($value['file_type'] == 'PDF文件格式' || $value['file_type'] == 'Flash动画文件') {
                $res[$key]['file_address'] = $value['file_address'];
                $res[$key]['file_address'] = '/app/learning/file?file_url=' .$value['file_address'];
            } else {
                $res[$key]['file_address'] = 'http://view.officeapps.live.com/op/view.aspx?src=http://' .$_SERVER['SERVER_NAME'] . $value['file_address'];
            }
        }
        return view('',['data'=>$res]);
    }

    //-------------------------------------------------------------



    public function indexknow()
    {
        $title = "报名须知";
        return view('',['title'=>$title]);
    }


    //我要报名
    public function examplandata(){
        return view();
    }

//------------------------------基本信息--------------------------
    //添加考生基本信息
    public function indexbase()
    {
        $title = "个人信息";
        $data=[];
        $loginId =  session('user')['id'];
//        $data = $this->SUserLogin->BaseWithFind('info',['id' => $loginId]);
        $data = $this->SUserLogin->BaseFind(['id' => $loginId]);
        return view('', ['datas' => $data,'title'=>$title]);
    }

    //判断是否有补考的资格
    public function make(){
        $examid = Request::instance()->post();
        $dr = $this -> pubmakeinfo($examid['plan_id']);
        $countmake = count($dr);
        return layuiMsg($countmake);
    }

    //新考补考 列表
    public function makeupexam($examid){

        $dr = $this -> pubmakeinfo($examid);
        return view('',['makeexam' => $dr,'exam_plan_id'=>$examid]);
    }


    //公共补考的信息
    public function pubmakeinfo($examid){
        $map=[
            'id_card'=>session('user')['id_card'],
            'id_type'=>session('user')['id_type'],
            'exam_type' => 0,
        ];
//        鉴定计划的所有工种
        $webData['exam_plan_id'] = $examid;
        $webData['type'] = 5;
        //查询计划的所有工种
        $workdata=$this->SExamPlan->selectExamPlanSan($webData);
        //补考的信息
        $data = $this->SGrade->selectGradeData($map);
        //满足条件至少一门通过
        foreach ($data as $it=>$ie){
            if($ie['theory_score_result'] == 1 || $ie['watch_score_result'] == 1  || $ie['synthesize_score_result'] == 1){
                $workNOpass[] = $ie;
            }
        }
        //符合补考的数据
        $dr = [];
        foreach ($data as $vv){
            foreach ($workdata as $k=>$v){
                //两年内有效
                $timediff = time()-$vv['exam_time'];
                $days = intval($timediff / 86400);
                if($vv['work_id'] ==$v['work_id'] && $vv['level'] == $v['work_level'] && $days<730 ){
                    $dr[$k] = $vv;
                }
            }
        }

        return $dr;
    }



    //-------------------鉴定报名---------------------------------

    //选择计划
    public function selectplan()
    {
        if (Request::instance()->isGet()) {
            $dataTitleArr = request()->param();
            $dataTitle = ['id' => ''];
            if (!empty($dataTitleArr['id'])) {
                $transmit = $dataTitleArr['id'];
                $dataTitle = $this->SExamPlan->BaseFind(['id' => $transmit]);
            }
            $arrExamPlan = $this->selectExamPlan();
            return view('selectplan', ['arrExamPlan' => $arrExamPlan, 'dataTitle' => $dataTitle,]);
        } else {
            return layuiMsg(-1, "请求失败");
        }
    }

        //判断此次鉴定计划报考的次数
    public function plancount(){
        $planid = request()->param();
        $mapenroll = [
            'exam_plan_id' => $planid['plan_id'],
            'user_login_id'=>session('user')['id'],
            'type'=>0,
            'organize_id'=>0,
            'source'=>7,
        ];
        //查询用户已报考数据
        $countRepet = $this->SExamEnrollOne->BaseSelect($mapenroll);
        $countEnroll = count($countRepet);
        if($countEnroll>2){
            return layuiMsg(-1,"您此次报考的鉴定计划已经超过3次,不能再报哦!");
        }else{
            return layuiMsg(1);
        }
    }



    //鉴定报名报名信息列表
    public function workInfo()
    {
            $now = time();
            $map['ep.exam_time'] = ['>', $now];
            $map['ee.type'] = 0;
            $where['ep.exam_time'] = ['<', $now];
            $examJoinData = $this->SExamEnroll->getExamEnrollJoinInfo($map);
            $examJoinDataPast = $this->SExamEnroll->getExamEnrollJoinInfo($where);
            $now = time();
            $arrExamPlan = $this->selectExamPlan();
            $title = '我的报名';
            //分页条数
            return view('examinfo', ['examData' => $examJoinData, 'now' => $now, 'arrExamPlan' => $arrExamPlan, 'examJoinDataPast' => $examJoinDataPast,'title'=>$title]);
    }


    // 添加报考信息   获取职业报名数据修改页面
    public function addExamEnroll()
    {
        if (Request::instance()->isGet()) {
            $dataTitleArr = request()->param();
            $dataTitle = [];
            if(!empty($dataTitleArr['id'])){
                $transmit = $dataTitleArr['id'];
                $dataTitle = $this->SExamPlan->BaseFind(['id'=>$transmit]);
            }
            $map = session('user')['id'];
            //获取当前用户的所有信息
            $userLoginId = $this->SUserLogin->getUserLoginCurrent($map);
            $arrExamPlan = $this->selectExamPlan(['id'=>$dataTitle['id']])[0];
            return view("add", ['logininfo' => $userLoginId, 'arrExamPlan' => $arrExamPlan,'dataTitle'=>$dataTitle]);

        }else{
            return layuiMsg(-1,"请求失败");
        }
    }

    //获取所有有效的计划
    public function selectExamPlan($where=[]){
        $now = time();
        $where['enroll_starttime'] = ['<',$now];
        $where['enroll_endtime'] = ['>',$now];
        $where['status'] = 1;
        $arrExamPlan = $this->SExamPlan->selectExamPlanData($where);
        return $arrExamPlan;
    }

    //诚信声明
    public function cooper(){
        $title = '诚信声明';
        return view('',['title'=>$title]);
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
            $arrExamPlan = $this->selectExamPlan();
//            $arrExamPlan = $this->SExamPlan->selectExamPlanData();

            $userMap = ['user_login_id'=>$arrEnroll['user_login_id']];
            $userInfo = $this->SUserinfo->BaseFind($userMap);
            if (empty($userInfo))
            {
                $this->SUserinfo->BaseSave($userMap);
            }
            //获取修改用户的所有信息
            $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrEnroll['user_login_id']);
            $fileData = [];
            $fileData = $this->SExamEnrollFile ->BaseSelect(['exam_enroll_id' =>$arrData['id']]);
            $i=1;
            $fileDataArray = [];
            foreach ($fileData as $v){
                $fileDataArray[$i]['type'] = $v['type'];
                $fileDataArray[$i]['path'] = $v['path'];
                $i++;
            }
            return view("edit", ['arrExamPlan' => $arrExamPlan, 'arrEnroll' => $arrEnroll, 'arrExamEnroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'fileData'=>$fileDataArray]);
        }
    }


    //上传审核资料文件信息
    public function fileupload(){
        if (Request::instance()->isGet()) {
            $arrData = input();
            $data = $this->SExamEnrollFile->BaseSelect(['exam_enroll_id' => $arrData['id']]);
            return view('fileupload',['all'=>$data]);
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

            $title = '<h1>江西省职业技能鉴定报名表</h1>';
            $dataApply=$this->applyPubinfo($maps,$title,$map);
            return view("printApplyinfo",$dataApply);

        }else{
            return layuiMsg(-1,'请求失败');
        }
    }

    //提交审核资格
    public function verifyEnroll()
    {
        if (Request::instance()->isGet()) {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"您没有选择选项");
            }
            $map['id'] = $arrData['id'];
            $maps['exam_enroll.id'] = $arrData['id'];
            $dataApply=$this->applyPubinfo($maps,'',$map);
//            dump($dataApply);die;
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
            $dataApply=$this->applyPubinfo($maps,'',$map);
            return view("",$dataApply);
        }else{
            return layuiMsg(-1,'请求失败');
        }
    }

    //提交资格审查  打印报名表格 详情 公共部分 1)根据报名id查询报名信息  2)获取当前用户信息 3)获取报考科目
    public function applyPubinfo($maps='',$title='',$map=''){
        $arrExamEnroll = $this->SExamEnroll->selectExamEnroll($maps);
        $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrExamEnroll['user_login_id']);

        $mapSub=['work_id'=>$arrExamEnroll['wid']];
        $subjectName=$this->SWorkLevelSubject->selAllSubject($mapSub);
        //上传论文个数
        $mapthesis['ee.work_level_subject_level'] = [['=',1],['=',2],'or'];
        $mapthesis['ep.work_type'] = ['=',1];
        $mapthesis['ee.id'] = ['=',$map['id']];
        $now = time();
        $examJoinData=$this->SExamEnroll->getExamEnrollPlusJoinInfo($mapthesis);
        if(!empty($examJoinData)){
            return  ['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'subjectName'=>['subjectName'=>$subjectName],'now'=>$now,'title'=>$arrExamEnroll['title'], 'examJoinDataThesisCount'=>$examJoinData[0]];
        }
        return  ['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'subjectName'=>['subjectName'=>$subjectName],'now'=>$now,'title'=>$arrExamEnroll['title']];
    }


    //----------------------------------发票----------------------------------------
    //发票管理首页
    public function indexBill()
    {
        //已缴费和打印准考证
        $map['ee.status'] = [['=',config('ExamEnrollStatus.paypass')],['=',config('ExamEnrollStatus.printticket')],'or'];
        $examJoinData=$this->SExamEnroll->getExamEnrollInvoicePlusJoinInfo($map);
        $title = '发票管理';
        return view('indexbill',['examData'=>$examJoinData,'title'=>$title]);
    }

    //填写发票信息
    public function applyinfo(){
        $request = Request::instance();
        if ($request->isGet()) {
            $dataId = $request->get();

            if (!empty($dataId)) {
                $detailObjOne=$this->SExamOrderDetail->findOrderDetail(['enroll_id'=>$dataId['id']]);
                $findObj = [];
                if($detailObjOne == true){
                    $where['order_id'] = $detailObjOne['order_id'];
                    $findObj = $this->SInvoice->selInvoiceData($where);
                    return view('',['id'=>$dataId['id'],'detaildata'=>$detailObjOne,'invoiceData'=>$findObj]);
                }else{
                    return view('',['id'=>$dataId['id'],'detaildata'=>$detailObjOne]);
                }

            }else{
                return layuiMsg(-1,'未知错误');
            }
        }else{
            return layuiMsg(-1,'请求失败');
        }
    }

    //打印发票信息
    public function printbill(){
        $request = Request::instance();
        if ($request->isGet()) {
            $dataId = $request->get();
            $findObj = [];
            if (!empty($dataId)) {
                $findObj = $this->SInvoice->selInvoiceData(['order_id'=>$dataId['id']]);
                return view('',['id'=>$dataId['id'],'invoiceData'=>$findObj]);
            }else{
                return layuiMsg(-1,'未知错误');
            }
        }else{
            return layuiMsg(-1,'请求失败');
        }

    }

    //邮寄地址确认
    public function mail($id){
        $userLoginId = $this->SUserLogin->getUserLoginCurrent(session('user')['id']);
        return view('',['userinfo'=>$userLoginId,'id'=>$id]);
    }


    //--------------------------我的成绩------------------------------

    //我的成绩
    public function gradeindex(){
        $map=[
            'id_card'=>session('user')['id_card'],
            'id_type'=>session('user')['id_type'],
        ];

        $gradeData= $this->SGrade->selectGradeData($map);
        $title = '我的成绩';
        return view('gradeindex',['gradedata'=>$gradeData,'title'=>$title]);
    }

    //----------------------------证书管理-------------------------
    //证书管理首页
    public function indexcert()
    {
//        $examJoinData = $this->pubCert();
        $examJoinData = [];
        $map = array(
            'type' => session('user')['id_type'],
            'id_no' => session('user')['id_card'],
        );
        $examJoinData = $this->SCertificate->selCertData($map);
        $title = '我的证书';
        return view('indexcert', ['examData' => $examJoinData,'title'=>$title]);

    }

    public function lingqu($id){
        $userLoginId = $this->SExamEnroll->findExamEnroll(['id'=>$id]);
        return view('',['userinfo'=>$userLoginId]);
    }

    public function youji($id){
        $userLoginId = $this->SCertificate->findCertDataOne(['exam_enroll_id'=>$id]);
        return view('',['userinfo'=>$userLoginId]);
    }

    public function pubCert(){
        $map['exam_enroll.status'] = [['=',Config("ExamEnrollStatus.paypass")],['=',Config("ExamEnrollStatus.printticket")],'or'];
        return  $examJoinData = $this->SExamEnroll->getExamEnrollJoinInfo($map);
    }

    public function detail(){
        $id = Request::instance()->param()['id']?Request::instance()->param()['id']:"";
        $map = session('user')['id'];
        //获取当前用户的所有信息
        $userLoginId = $this->SUserLogin->getUserLoginCurrent($map);
//        $arrExamEnroll = $this->SExamEnroll->selectExamEnroll(['exam_enroll.id' => $id['id']]);

        $certData = $this->SCertificate->findCertDataOne(['id'=>$id]);
        if(!empty($certData)){
            return view('',['certinfo'=>$certData,'logininfo'=>$userLoginId]);
        }else{
            return view('');
        }
    }

    //------------------------------线下缴费------------------------

    public function indexpay()
    {
        $examJoinData=$this->SExamEnroll->getExamEnrollJoinInfo();
        return view('',['examData'=>$examJoinData]);
    }


    /**
     * 线下缴费
     * @return \think\response\View
     * @user xuwieqi 2018/10/19
     */
    public function pay()
    {
        $request = Request::instance();
        if ($request->isGet()) {
            $id = $request->get();
            if (!empty($id)) {
                $arrExamEnroll = $this->SExamEnroll->selectExamEnroll(['exam_enroll.id'=>$id['id']]);
                $array=[
                    'wls.work_id'=>$arrExamEnroll['work_id'],
                    'wls.level'=>$arrExamEnroll['work_level_subject_level'],
                ];
                $subject=$this->SWorkLevelSubject->selSubject($array);
                $total_money=[];
                if(!empty($subject)){
                    foreach ($subject as $k=>$v){
                        $total_money[$k]=$v->price;
                    }
                }
                $total_money['total_money']= array_sum($total_money);
                return view('',['worksubject'=>$subject,'arrExamEnroll'=>$arrExamEnroll,'total_money'=>$total_money]);
            }
        }
    }

    /**
     * 打印准考证
     * @return \think\response\View
     * @user xuwieqi 2018/10/23
     */
    public function printExam()
    {

        if (Request::instance()->isGet()){
            $arrData = input();
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1,"您没有选择选项");
            }
            $maps['exam_enroll.id'] = $arrData['id'];
            $arrExamEnroll = $this->SExamEnroll->selectExamEnroll($maps);
            $userLoginId=$this->SUserLogin->getUserLoginCurrent($arrExamEnroll['user_login_id']);
            $mapSub=['work_id'=>$arrExamEnroll['wid']];
            $subjectName=$this->SWorkLevelSubject->selAllSubject($mapSub);
            $getAdminTicketObj= $this->SExamCard->BaseFind(['enroll_id'=>$arrData['id']]);
            return view("print",['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'ticket'=>$getAdminTicketObj,'subjectName'=>['subjectName'=>$subjectName]]);

        }else{
            return layuiMsg(-1,'请求失败');
        }
    }



    //------------------------论文上传------------------------
    public function indexthesis(){
        $map['ee.work_level_subject_level'] = [['=',1],['=',2],'or'];
        $map['ep.work_type'] = ['=',1];
        $now = time();
        $examJoinData=$this->SExamEnroll->getExamEnrollPlusJoinInfo($map);
        $title = '论文上传';
        return view('',['examData'=>$examJoinData,'now'=>$now,'title'=>$title]);
    }


    //生成条形码
    public function barcode_create($content='112345678'){
    // 引用barcode文件6949233177018
    //夹对应的类
    Loader::import('BCode.BCGFontFile',EXTEND_PATH);
//		Loader::import('BCode.BCGColor',EXTEND_PATH);
    Loader::import('BCode.BCGDrawing',EXTEND_PATH);
//		$content= isset($_GET['text']) ? $_GET['text'] : 'HELLO';
    // 条形码的编码格式
    Loader::import('BCode.BCGcode128',EXTEND_PATH,'.barcode.php');
    // $code = '';
    // 加载字体大小
//		$font = new BCGFontFile('./class/font/Arial.ttf', 18);
//		$font = new \BCGFontFile(Loader::import('BCode.Arial',EXTEND_PATH,'ttf'), 18);

    //颜色条形码
    $color_black = new \BCGColor(0, 0, 0);
    $color_white = new \BCGColor(255, 255, 255);

    $drawException = null;
    try
    {
        $code = new \BCGcode128();
        $code->setScale(1);
        $code->setThickness(30); // 条形码的厚度
        $code->setForegroundColor($color_black); // 条形码颜色
        $code->setBackgroundColor($color_white); // 空白间隙颜色
//			 $code->setFont($font); //
        $code->parse($content); // 条形码需要的数据内容
    }
    catch(\Exception $exception)
    {
        $drawException = $exception;
    }

    //根据以上条件绘制条形码
    $drawing = new \BCGDrawing('', $color_white);
    if($drawException) {
        $drawing->drawException($drawException);
    }else{
        $drawing->setBarcode($code);
        $drawing->draw();
    }
    ob_end_clean();
    // 生成PNG格式的图片
    header('Content-Type: image/png');
//		header('Content-Disposition:attachment; filename="barcode.png"'); //自动下载
    $drawing->finish(\BCGDrawing::IMG_FORMAT_PNG);
}

  }