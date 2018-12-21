<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/24
 * Time: 15:41
 */

namespace app\common\service;
use app\common\model\ExamStaffLog as MExamStaffLog;
use app\common\service\JuryReview;

class ExamStaffLog extends MExamStaffLog
{
    private $SExamStaffLog;
    private $SjuryReview;

    public function __construct()
    {
        parent::__construct();
        $this->SExamStaffLog = new MExamStaffLog();
        $this->SjuryReview = new JuryReview();
    }
    /**
     * @param $where
     * @param $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/24}~{16:51}
     */
    public function selExamPlan($where=[],$order="",$group = "")
    {
        return $this->SExamStaffLog
            ->join("__EXAM_PLAN__","exam_plan.id=exam_staff_log.exam_plan_id")
            ->where($where)
            ->order($order)
            ->group($group)
            ->select();
    }

    /**
     * [allotTrue 分配成功日志]
     * @return [type] [description]
     */
    public function allotTrue($where,$field='')
    {
        return $this->SExamStaffLog
            ->join('__JURY_CERTIFICATE__ jc','jc.id=exam_staff_log.be_assigned_id')
            ->join('__WORK__ w','w.id=jc.work_id')
            ->where($where)
            ->field($field)
            ->select();


    }

    public function getUser($where = [],$field='')
    {
        return $this->SExamStaffLog
            ->join('__TEMPORARY__','temporary.id=exam_staff_log.be_assigned_id')
            ->where($where)
            ->field($field)
            ->select();
    }

    public function getall($where)
    {
        return $this->SExamStaffLog->BaseSelect($where);
    }

    public function addJuryLog($webData = [],$addData = [])
    {
        // 启动事务
        $this->SExamStaffLog->startTrans();
        try {
            $return = $this->SExamStaffLog->BaseSaveAll($addData);
            if (!$return)
            {
                throw new \Exception('审核失败');
            }

            $juryMap['status'] = 2;
            if(isset($webData['reason']) || isset($webData['pass_reason'])){
                $juryMap['status'] = 1;
                $juryMap['reason'] = isset($webData['reason'])?trim($webData['reason']):'';
                $juryMap['pass_reason'] = isset($webData['pass_reason'])?implode(',',$webData['pass_reason']):'';
            }
//            print_r($juryMap);die;

            $juryReview = $this->SjuryReview->BaseUpdate($juryMap,['id'=>['in',$webData['id']]]);
            if (!$juryReview)
            {
                throw new \Exception('审核失败');
            }
            $this->SExamStaffLog->commit();
            return layuiMsg(1,'审核成功');
        } catch (\Exception $e) {
            // 回滚事务
            $this->SExamStaffLog->rollback();
            return layuiMsg(-1,$e->getMessage());
        }
    }
}