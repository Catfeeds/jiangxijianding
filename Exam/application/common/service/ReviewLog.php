<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 15:33
 */

namespace app\common\service;
use app\common\model\ReviewLog as MReviewLog;
use think\Config;


class ReviewLog extends MReviewLog
{
    /**
     * 缓缴费审核
     * @param array $map
     * @param array $webData
     * @return bool|int|string
     * @throws \think\exception\PDOException
     * @user 朱颖 {2018/10/30}~{15:24}
     */
    public function slowPayment($map=[],$webData = [])
    {
        if (empty($map))
        {
            return -1;
        }
        // 启动事务
        $this->startTrans();
        try {
//            $objDetail = $this->SexamOrder->getDetailById($map,$field,"","review_log.update_time desc");
            $OrderLog = $this->getDataByNum($map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status"],"review_log.update_time desc");

            //待初审 通过状态
            switch ($OrderLog['log_status'])
            {
                //等于1 需要复审 成功
                case 1:
                    $data['pay_state'] = 5;
                    $examenrollStatus = config('ExamEnrollStatus.huan');
                    $logStatus = 3;
                break;
                //等于'' 需要初审 成功
                case '':
                    $data['pay_state'] = 5;
                    $examenrollStatus = config("ExamEnrollStatus.huan");
                    $logStatus = 1;
                    break;
                //等于3 需要终审 成功
                case 3:
                    $data['pay_state'] = 3;
                    $examenrollStatus = config("ExamEnrollStatus.paydelayed");
                    $logStatus = 20;
                    break;
                default:
                    $data['pay_state'] = 0;
                    $examenrollStatus = 0;
                    $logStatus = 0;
            }
            if (isset($webData['reason']) || isset($webData['pass_reason']))
            {
                switch ($OrderLog['log_status'])
                {
                    //等于1 需要复审 不成功
                    case 1:
                        $data['pay_state'] = 6;
                        $examenrollStatus = config("ExamEnrollStatus.huanfalse");
                        $logStatus = 4;
                        break;
                    //等于'' 需要初审 不成功
                    case '':
                        $data['pay_state'] = 6;
                        $examenrollStatus = config("ExamEnrollStatus.huanfalse");
                        $logStatus = 2;
                        break;
                    //等于3 需要终审 不成功
                    case 3:
                        $data['pay_state'] = 6;
                        $examenrollStatus = config("ExamEnrollStatus.huanfalse");
                        $logStatus = 52;
                        break;
                    default:
                        $data['pay_state'] = 0;
                        $examenrollStatus = 0;
                        $logStatus = 0;
                }
            }
            if ($data['pay_state'] == 0 ||$examenrollStatus == 0 || $logStatus == 0)
            {
                throw new \Exception('审核失败');
            }
            $order = model('ExamOrder')->BaseUpdate($data,$map);

            if ( !$order )
            {
                throw new \Exception('审核失败');
            }
            $orderId = model('ExamOrder')->BaseFind($map,['id']);

            $examEnroll = model('ExamOrderDetail')->BaseSelect(['order_id'=>$orderId->id],['enroll_id']);

            foreach ($examEnroll as $k=>$v)
            {
                $arrWhere[$k]['id'] = $v['enroll_id'];
                $arrWhere[$k]['status'] = $examenrollStatus;
                $arrWhere[$k]['update_time'] = time();
            }

            //修改报名表
            $arrenroll = model('ExamEnroll')->BaseSaveAll($arrWhere);

            if ( !$arrenroll )
            {
                throw new \Exception('审核失败');
            }
            $adminuser = session('adminuser');
            $addLog['review_id'] = $adminuser['id'];
            $addLog['review_type'] = 1; //鉴定中心
            $addLog['review_time'] = time();
            $addLog['reviewed_type'] = 5;   //缓缴费
            $addLog['reviewed_id'] = $orderId->id;
            $addLog['review_ip'] = getip();
            $addLog['create_time'] = time();
            $addLog['update_time'] = time();
            $addLog['status'] = $logStatus;

            if (isset($webData['reason']) || isset($webData['pass_reason']))
            {
                $addLog['reason'] = isset($webData['reason'])?trim($webData['reason']):'';
                $addLog['pass_reason'] = isset($webData['pass_reason'])?implode(',',$webData['pass_reason']):'';
            }
            $res = $this->BaseSave($addLog);

            if (!$res)
            {
                throw new \Exception('审核失败');
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    /**
     * @param array $map
     * @param array $webData
     * @return bool|int|string
     * @throws \think\exception\PDOException
     * @user 朱颖 {2018/10/31}~{10:07}
     */
    public function personalBelowPay($map=[],$webData = [])
    {
        if (empty($map) || empty($webData))
        {
            return -1;
        }
        // 启动事务
        $this->startTrans();
        try {
            $data["pay_state"] = 4;     //线下已支付
            $order = model('ExamOrder')->BaseUpdate($data,$map);
            if ( !$order )
            {
                throw new \Exception('审核失败');
            }
            $orderId = model('ExamOrder')->BaseFind($map);

            $examEnroll = model('ExamOrderDetail')->BaseFind(['order_id'=>$orderId->id]);

            $arrWhere['status'] = Config::get("enrollstatus.paypass");
            $arrWhere['update_time'] = time();
            //修改报名表
            $arrenroll = model('ExamEnroll')->BaseUpdate($arrWhere,['id'=>$examEnroll['enroll_id']]);
            if ( !$arrenroll )
            {
                throw new \Exception('审核失败');
            }
            $adminuser = session('adminuser');

            $addLog['review_id'] = $adminuser['id'];
            $addLog['review_type'] = 1; //鉴定中心
            $addLog['review_time'] = time();
            $addLog['reviewed_type'] = 6;   //缓缴费
            $addLog['reviewed_id'] = $orderId->id;
            $addLog['review_ip'] = getip();
            $addLog['create_time'] = time();
            $addLog['update_time'] = time();
            $addLog['status'] = 50;
            $res = $this->BaseSave($addLog);
            if (!$res)
            {
                throw new \Exception('审核失败');
            }

            if (!empty($webData))
            {
                $addPay['water_no'] = $webData;
                $addPay['order_num'] = $map['order_num'];
                $addPay['pay_money'] = $orderId['total_money'];
                $addPay['pay_time'] = time();
                $addPay['pay_type'] = 1;    //线下支付
                $addPay['status'] = 1;      //正常
                $addPay['create_time'] = time();
                $addPay['update_time'] = time();
            }
            $ExamOrderPay = model('ExamOrderPay')->BaseSave($addPay);

//            print_r($ExamOrderPay);die;
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    /**
     * @param array $map
     * @param array $webData
     * @return bool|int|string
     * @throws \think\exception\PDOException
     * @user 朱颖 {2018/10/31}~{10:22}
     */
    public function organizeBelowPay($map=[],$webData = [])
    {
        if (empty($map) || empty($webData))
        {
            return -1;
        }
        // 启动事务
        $this->startTrans();
        try {
            $data["pay_state"] = 4;     //线下已支付
            $order = model('ExamOrder')->BaseUpdate($data,$map);
            if ( !$order )
            {
                throw new \Exception('审核失败');
            }
            $orderId = model('ExamOrder')->BaseFind($map);

            $examEnroll = model('ExamOrderDetail')->BaseSelect(['order_id'=>$orderId->id]);
            $arrWhere = [];
            foreach ($examEnroll as $k=>$v)
            {
                $arrWhere[$k]['status'] = config("ExamEnrollStatus.paypass");
                $arrWhere[$k]['update_time'] = time();
                $arrWhere[$k]['id'] = $v['enroll_id'];
            }
            //修改报名表
            $arrenroll = model('ExamEnroll')->BaseSaveAll($arrWhere);
            if ( !$arrenroll )
            {
                throw new \Exception('审核失败');
            }
            $adminuser = session('adminuser');

            $addLog['review_id'] = $adminuser['id'];
            $addLog['review_type'] = 1; //鉴定中心
            $addLog['review_time'] = time();
            $addLog['reviewed_type'] = 6;   //线下缴费
            $addLog['reviewed_id'] = $orderId->id;
            $addLog['review_ip'] = getip();
            $addLog['create_time'] = time();
            $addLog['update_time'] = time();
            $addLog['status'] = 50;
            $res = $this->BaseSave($addLog);
            if (!$res)
            {
                throw new \Exception('审核失败');
            }
            $addPay = [];
            if (!empty($webData))
            {
                $addPay['water_no'] = $webData;
                $addPay['order_num'] = $map['order_num'];
                $addPay['pay_money'] = $orderId['total_money'];
                $addPay['pay_time'] = time();
                $addPay['pay_type'] = 1;    //线下支付
                $addPay['status'] = 1;      //正常
                $addPay['create_time'] = time();
                $addPay['update_time'] = time();
            }
            $ExamOrderPay = model('ExamOrderPay')->BaseSave($addPay);
            if (!$ExamOrderPay)
            {
                throw new \Exception('审核失败');
            }
//            print_r($ExamOrderPay);die;
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

}