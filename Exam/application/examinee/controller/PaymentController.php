<?php
namespace app\examinee\controller;
use app\common\controller\Examineebase;
use think\Request;
use app\common\service\Userinfo;
use app\common\service\UserLogin;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\WorkType;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamPlan;
use app\common\service\ExamEnrollFile;
use  app\common\service\WorkLevelSubject;
use app\common\service\AdmissionTicket;
use app\common\service\ExamCard;
use think\session;

class PaymentController extends Examineebase
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
    protected $SExamCard;

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
        $this->SExamCard = new ExamCard();
    }

    public function index()
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
            $getAdminTicketObj= $this->SExamCard->BaseFind(['exam_enroll_id'=>$arrData['id']]);
            return view("print",['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'ticket'=>$getAdminTicketObj,'subjectName'=>['subjectName'=>$subjectName]]);

        }else{
            return layuiMsg(-1,'请求失败');
        }
    }




}