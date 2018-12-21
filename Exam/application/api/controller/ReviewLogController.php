<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 14:39
 */

namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\ExamEnroll;
use app\common\service\ReviewLog;
use app\common\service\ExamOrder;
use app\common\service\ExamPlan;
use app\common\service\Grade;
use think\Config;
use think\Request;


class ReviewLogController extends BaseApi{
    private $SExamEnroll;
    private $SReviewLog;
    private $SExamOrder;
    private $SExamPlan;
    private $SGrade;

    public function __construct()
    {
        parent::__construct();
        $this->SExamEnroll = new ExamEnroll();
        $this->SReviewLog  = new ReviewLog();
        $this->SExamOrder  = new ExamOrder();
        $this->SExamPlan   = new ExamPlan();
        $this->SGrade      = new Grade(); 
    }

    /**
     * @return array
     * @user 朱颖 {2018/10/20}~{16:47}
     */
    public function add()
    {
        if (Request::instance()->isPost())
        {
            $arrData = input('post.');
            $ip = getip();
            if (empty($arrData['id'])) {
                return layuiMsg(-1, "非法操作");
            }
        }
        $examEnroll = $this->SExamEnroll->BaseFind(['id'=>$arrData['id']],['id,status']);
        if ($examEnroll['status'] != config("ExamEnrollStatus.submit") && $examEnroll['status'] != config("ExamEnrollStatus.print")){
            return layuiMsg(-1, "您不能审核");
        }

        //审核通过状态
        if(isset($arrData['reason']) || isset($arrData['pass_reason'])){
            $map['status'] = config("ExamEnrollStatus.nopass");
        }elseif (isset($arrData['reject']) || isset($arrData['pass_reject']) ){
            $map['status'] = config("ExamEnrollStatus.reject");
        }else{
            $map['status'] = config("ExamEnrollStatus.checkpass");
        }

        // 启动事务
        $this->SExamEnroll->startTrans();
        try {
        $saveEnroll = $this->SExamEnroll->BaseUpdate($map,['id'=>$arrData['id']]);
        if (!$saveEnroll){
            throw new \Exception('审核失败');
        }
        //获取ID
        $arrAdmin = session("adminuser");
        $adminId = $arrAdmin['id'];
        $addReviewLog['review_id'] = $adminId;
        //鉴定中心审核
        $addReviewLog['review_type'] = 1;
        $addReviewLog['review_time'] = time();
        $addReviewLog['reviewed_type'] = 2;
        $addReviewLog['reviewed_id'] = $arrData['id'];
        $addReviewLog['review_ip'] = $ip;
        $addReviewLog['create_time'] = time();
        $addReviewLog['update_time'] = time();
        if(isset($arrData['reason']) || isset($arrData['pass_reason'])){
            $addReviewLog['reason'] = isset($arrData['reason'])?trim($arrData['reason']):'';
            $addReviewLog['pass_reason'] = isset($arrData['pass_reason'])?implode(',',$arrData['pass_reason']):'';
//            print_r($addReviewLog);die;
            $addReviewLog['status'] = config("ExamEnrollStatus.nopass");
        }elseif (isset($arrData['reject'])  || isset($arrData['pass_reject'])){
            $addReviewLog['reason'] = isset($arrData['reject'])?trim($arrData['reject']):'';
            $addReviewLog['pass_reason'] = isset($arrData['pass_reject'])?implode(',',$arrData['pass_reject']):'';
            $addReviewLog['status'] = config("ExamEnrollStatus.reject");
        }else{
            $addReviewLog['status'] = config("ExamEnrollStatus.checkpass");
        }
//        print_r($addReviewLog);die;
        $saveSReviewLog = $this->SReviewLog->BaseSave($addReviewLog);
        if (!$saveSReviewLog)
        {
            throw new \Exception('审核失败');
        }
        // 提交事务
        $this->SExamEnroll->commit();
        return layuiMsg(1, "审核成功");
        } catch (\Exception $e) {
            // 回滚事务
            $this->SExamEnroll->rollback();
            return layuiMsg(-1, $e->getMessage());
        }

    }
    //批量报名审核
    public function batchAdd()
    {
        if (Request::instance()->isPost())
        {
            $webData = Request::instance()->post();
            $ip = getip();
            $addReviewLog = [];
            if (empty($webData['id'])) {
                return layuiMsg(-1, "非法操作");
            }
        }
        $where['id'] = ['in',$webData['id']];
        $examEnroll = $this->SExamEnroll->BaseSelect($where,['id','status']);
        $oldArr = explode(",",$webData['id']);
        if (count($examEnroll) != count($oldArr))
        {
            return layuiMsg(-1, "您不能审核");
        }
        foreach ($examEnroll as $k=>$v)
        {
            if ($v['status'] != config("ExamEnrollStatus.submit") && $v['status'] != config("ExamEnrollStatus.print")){
                return layuiMsg(-1, "您不能审核");
            }
            //审核通过状态
            if(isset($arrData['reason'])){
                $map[$k]['status'] = config("ExamEnrollStatus.nopass");
                $map[$k]['id'] = $v['id'];
            }elseif (isset($arrData['reject'])){
                $map[$k]['status'] = config("ExamEnrollStatus.reject");
                $map[$k]['id'] = $v['id'];
            }else{
                $map[$k]['status'] = config("ExamEnrollStatus.checkpass");
                $map[$k]['id'] = $v['id'];
            }
        }
        // 启动事务
        $this->SExamEnroll->startTrans();
        try {
            $saveEnroll = $this->SExamEnroll->BaseSaveAll($map);
            if (!$saveEnroll){
                throw new \Exception('审核失败');
            }
            foreach ($examEnroll as $k=>$v)
            {
                //获取ID
                $arrAdmin = session("adminuser");
                $adminId = $arrAdmin['id'];
                $addReviewLog[$k]['review_id'] = $adminId;
                //鉴定中心审核
                $addReviewLog[$k]['review_type'] = 1;
                $addReviewLog[$k]['review_time'] = time();
                $addReviewLog[$k]['reviewed_type'] = 2;
                $addReviewLog[$k]['reviewed_id'] = $v['id'];
                $addReviewLog[$k]['review_ip'] = $ip;
                $addReviewLog[$k]['create_time'] = time();
                $addReviewLog[$k]['update_time'] = time();
                if(isset($webData['reason'])){
                    $addReviewLog[$k]['reason'] = trim($webData['reason']);
                    $addReviewLog[$k]['status'] = config("ExamEnrollStatus.nopass");
                }elseif (isset($webData['reject'])){
                    $addReviewLog[$k]['reason'] = trim($webData['reject']);
                    $addReviewLog[$k]['status'] = config("ExamEnrollStatus.reject");
                }else{
                    $addReviewLog[$k]['status'] = config("ExamEnrollStatus.checkpass");
                }
            }
            $saveSReviewLog = $this->SReviewLog->BaseSaveAll($addReviewLog);
            if (!$saveSReviewLog){
                throw new \Exception('审核失败');
            }
            // 提交事务
            $this->SExamEnroll->commit();
            return layuiMsg(1, "审核成功");
        } catch (\Exception $e) {
            // 回滚事务
            $this->SExamEnroll->rollback();
            return layuiMsg(-1, $e->getMessage());
        }
    }

    /**
     * @return array
     * @throws \think\exception\PDOException
     * @user 朱颖 {2018/10/30}~{16:47}
     */
    public function slowPayment()
    {
        if (Request::instance()->isPost())
        {
            $webData = Request::instance()->post();

            if (empty($webData['order_num']))
            {
                return layuiMsg(-1, "非法操作");
            }

            $saveSlowPayment = $this->SReviewLog->slowPayment(['order_num'=>$webData['order_num']],$webData);
            if ($saveSlowPayment)
            {
                return layuiMsg(1, "审核成功");
            }else{
                return layuiMsg(-1, "审核失败");
            }

        }else{
            return layuiMsg(-1, "非法操作");
        }
    }

    /**
     * @return array
     * @throws \think\exception\PDOException
     * @user 朱颖 {2018/10/31}~{10:08}
     */
    public function personalBelowPay()
    {
        if (Request::instance()->isPost())
        {
            $webData = Request::instance()->post();
            if (empty($webData['order_num']) || empty($webData['water_no']))
            {
                return layuiMsg(-1, "非法操作");
            }
            $savePersonalBelowPay = $this->SReviewLog->personalBelowPay(['order_num'=>$webData['order_num']],$webData['water_no']);

            if ($savePersonalBelowPay)
            {
                return layuiMsg(1, "审核成功");
            }else{
                return layuiMsg(-1, "审核失败");
            }
        }else{
            return layuiMsg(-1, "非法操作");
        }
    }

    /**
     * @return array
     * @throws \think\exception\PDOException
     * @user 朱颖 {2018/10/31}~{10:11}
     */
    public function organizeBelowPay()
    {
        if (Request::instance()->isPost())
        {
            $webData = Request::instance()->post();
            if (empty($webData['order_num']))
            {
                return layuiMsg(-1, "非法操作");
            }
            $water_no = $this->SExamOrder->BaseFind(['order_num'=>$webData['order_num']])['water_no'];
            $savePersonalBelowPay = $this->SReviewLog->organizeBelowPay(['order_num'=>$webData['order_num']],$water_no);

            if ($savePersonalBelowPay)
            {
                return layuiMsg(1, "审核成功");
            }else{
                return layuiMsg(-1, "审核失败");
            }
        }else{
            return layuiMsg(-1, "非法操作");
        }
    }

    public function delete()
    {

    }

    public function update()

    {

    }
}

