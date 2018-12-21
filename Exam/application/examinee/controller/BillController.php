<?php
namespace app\examinee\controller;
use app\common\controller\Examineebase;
use think\Request;
use app\common\service\ExamEnrollTable;
use app\common\service\Invoice;
use app\common\service\Grade;
use app\common\service\Userinfo;
use app\common\service\UserLogin;

class BillController extends Examineebase
{
    protected $SExamEnroll;
    protected $SInvoice;
    protected $SGrade;
    protected $SUserinfo;
    protected $SUserLogin;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SExamEnroll = new ExamEnrollTable();
        $this->SInvoice = new Invoice();
        $this->SGrade = new Grade();
        $this->SUserinfo = new Userinfo();
        $this->SUserLogin = new UserLogin();
        if (!$this->SUserinfo->findUserinfoData(['user_login_id'=> session('user')['id']])) $this->redirect('/examinee/BaseInfo/indexbase');
    }

    //发票管理首页
    public function index()
    {
        $examJoinData=$this->SExamEnroll->ExamEnroll(['exam_enroll.status'=>config('EnrollStatus.paypass'),'exam_enroll.status'=>config('EnrollStatus.printticket')]);
        return view('',['examData'=>$examJoinData]);
    }

    //填写发票信息
    public function applyinfo(){
        $request = Request::instance();
        if ($request->isGet()) {
            $dataId = $request->get();
            if (!empty($dataId)) {
                $findObj = $this->SInvoice->selInvoiceData(['enroll_id'=>$dataId['id']]);
                if(!$findObj){
                    return view('',['id'=>$dataId['id']]);
                }else{
                    return view('',['id'=>$dataId['id'],'invoiceData'=>$findObj]);
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
            if (!empty($dataId)) {
                $findObj = $this->SInvoice->selInvoiceData(['enroll_id'=>$dataId['id']]);
                if(!$findObj){
                    return view('',['id'=>$dataId['id']]);
                }else{
                    return view('',['id'=>$dataId['id'],'invoiceData'=>$findObj]);
                }
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



    //我的成绩
    public function gradeindex(){
        $map=[
            'id_card'=>session('user')['id_card'],
            'id_type'=>session('user')['id_type'],
        ];
        $gradeData= $this->SGrade->selectGradeData(['id_card'=>$map['id_card'],'id_type'=>$map['id_type']]);
        return view('gradeindex',['gradedata'=>$gradeData]);
    }




}