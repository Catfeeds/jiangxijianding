<?php
namespace app\examinee\controller;
use app\common\controller\Examineebase;
use think\Request;
use app\common\service\ExamEnrollTable;
use app\common\service\Userinfo;
use app\common\service\Certificate;
use app\common\service\UserLogin;
use app\common\service\Invoice;
use app\common\service\Grade;


class CertController extends Examineebase
{
    protected $SExamEnroll;
    protected $SUserinfo;
    protected $SCertificate;
    protected $SUserLogin;
    protected $SInvoice;
    protected $SGrade;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SExamEnroll = new ExamEnrollTable();
        $this->SUserinfo = new Userinfo();
        $this->SCertificate = new Certificate();
        $this->SUserLogin = new UserLogin();
        $this->SInvoice = new Invoice();
        $this->SGrade = new Grade();
        if (!$this->SUserinfo->findUserinfoData(['user_login_id' => session('user')['id']])) $this->redirect('/examinee/BaseInfo/indexbase');
    }

    //证书管理首页
    public function index()
    {
        $examJoinData = $this->pubCert();
        return view('', ['examData' => $examJoinData]);

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
       return  $examJoinData = $this->SExamEnroll->ExamEnroll(['exam_enroll.status' => config('EnrollStatus.paypass'), 'exam_enroll.status' => config('EnrollStatus.printticket')]);
    }

    public function detail(){
        $id = Request::instance()->param();
        $map = session('user')['id'];
        //获取当前用户的所有信息
        $userLoginId = $this->SUserLogin->getUserLoginCurrent($map);
        $arrExamEnroll = $this->SExamEnroll->selectExamEnroll(['exam_enroll.id' => $id['id']]);
        $certData = $this->SCertificate->findCertDataOne(['exam_enroll_id'=>$id['id']]);
        if(!empty($certData)){
            return view('',['certinfo'=>$certData,'logininfo'=>$userLoginId,'examenroll'=>$arrExamEnroll]);
        }else{
            return view('');
        }
    }
}