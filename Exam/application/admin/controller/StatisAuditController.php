<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/23
 * Time: 15:56
 */

namespace app\admin\controller;

use app\common\service\ReviewLog;
use app\common\service\ReviewLogJoin;
use app\common\controller\AdminBase;
use app\common\service\ExamEnrollTable;
use app\common\service\UserLogin;
use  app\common\service\WorkLevelSubject;
use think\Request;
class StatisAuditController extends AdminBase
{
    private $SReviewLog;
    private $SReviewLogJoin;
    private $ExamEnrollTable;
    protected $SUserLogin;
    protected $SWorkLevelSubject;

    public function __construct()
    {
        parent::__construct();
        $this->SReviewLog = new ReviewLog();
        $this->SReviewLogJoin = new ReviewLogJoin();
        $this->ExamEnrollTable = new ExamEnrollTable();
        $this->SUserLogin = new UserLogin();
        $this->SWorkLevelSubject = new WorkLevelSubject();
    }

    /**
     * 报名审核统计
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/7~9:59
     */
    public function index()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrReviewLog = $this->SReviewLogJoin->getSReviewLog($paginate,['review_log.reviewed_type'=>2]);
        return view("index",['arrReviewLog'=>$arrReviewLog]);
    }

    /**
     * 报名审核详情
     * @return array|\think\response\View
     * @user 朱颖 2018/11/30~12:20
     */
    public function detail()
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
//        print_r($dataApply);die;
        return view("detail",$dataApply);
    }
    //提交资格审查  打印报名表格 详情 公共部分 1)根据报名id查询报名信息  2)获取当前用户信息 3)获取报考科目
    public function applyPubinfo($maps=''){
        $arrExamEnroll = $this->ExamEnrollTable->selectExamEnroll($maps);
        $userLoginId= $this->SUserLogin->getUserLoginCurrent($arrExamEnroll['user_login_id']);
        $mapSub=['work_id'=>$arrExamEnroll['wid']];
        $subjectName=$this->SWorkLevelSubject->selAllSubject($mapSub);
        return  ['examenroll' => $arrExamEnroll,'logininfo'=>$userLoginId,'subjectName'=>['subjectName'=>$subjectName]];
    }

    public function slowPay()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $field = "review_log.id,review_log.reason,exam_order.order_num,exam_order.total_money,exam_order.pay_state,review_log.status as log_status,exam_plan.title,exam_order.id as order_id,organize.name,organize.type,admin.name as admin_name,review_log.review_time";
        $arrReviewLog = $this->SReviewLogJoin->getLogBySlowPay($paginate,['review_log.reviewed_type'=>5],$field);
//        print_r($this->SReviewLogJoin->getLastSql());die;
        return view("slowPay",['arrReviewLog'=>$arrReviewLog]);
    }
}