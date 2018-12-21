<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 15:33
 */

namespace app\common\service;
use app\common\model\ReviewLog as MReviewLog;

use app\common\model\ExamPlan;
class ReviewLogJoin extends MReviewLog
{

    private $MReviewLog;
    private $MExamPlan;

    public function __construct()
    {
        parent::__construct();
        $this->MReviewLog = new MReviewLog();
        $this->MExamPlan = new ExamPlan();
    }

    /**
     * 报名审核日志
     * @param array $paginate
     * @param array $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getSReviewLog($paginate = [],$where=[])
    {
        list($listRows, $simple, $config) = $paginate;
        $field = "review_log.review_id,review_log.review_type,review_log.review_time,review_log.reviewed_type,review_log.reviewed_id,review_log.status as log_status,admin.name,exam_plan.title,exam_enroll.status,exam_enroll.id as exam_enroll_id,user_login.id_card as username,user_login.id_type";
        return $ReviewLog = $this->MReviewLog
            ->join("__ADMIN__","admin.id=review_log.review_id")
            ->join("__EXAM_ENROLL__","review_log.reviewed_id=exam_enroll.id")
            ->join("__EXAM_PLAN__","exam_enroll.exam_plan_id=exam_plan.id")
            ->join("__USER_LOGIN__","exam_enroll.user_login_id=user_login.id")
            ->where($where)
            ->field($field)
            ->order("review_log.id desc")
            ->paginate($listRows, $simple, $config);
//        print_r($ReviewLog);die;
    }

    public function getLogBySlowPay($paginate = [],$where=[],$field=[])
    {
        list($listRows, $simple, $config) = $paginate;
        return $ReviewLog = $this->MReviewLog
            ->join("__ADMIN__","admin.id=review_log.review_id")
            ->join("__EXAM_ORDER__","exam_order.id=review_log.reviewed_id")
            ->join("__EXAM_PLAN__","exam_order.exam_plan_id=exam_plan.id")
            ->join("__ORGANIZE__","exam_order.type_id=organize.id AND exam_order.type=organize.type")
            ->where($where)
            ->field($field)
            ->order("review_log.id desc")
            ->paginate($listRows, $simple, $config);
    }

}