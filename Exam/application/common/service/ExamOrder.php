<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午6:13
 */

namespace app\common\service;

use app\common\model\ExamOrder as MExamOrder;

class ExamOrder extends MExamOrder
{
    protected $updateTime = false;

    private $SExamOrderPay;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->SExamOrderPay = new ExamOrderPay();
    }

    /**
     * 添加订单
     * @param $orderData
     * @param $oderDetailData
     * @param $enrollData
     * @param $map
     * @return bool|string
     * @throws \think\exception\PDOException
     * @user xuweiqi 2018/10/22
     */
    public function addOrder($orderData, $oderDetailData, $enrollData, $map)
    {
        // 启动事务
        $this->startTrans();
        try {
            $orderid = $this->BaseSave($orderData, true);
            if (!$orderid) {
                throw new \Exception('添加订单失败');
            }
            $oderDetailData['order_id'] = $orderid;
            $res                        = model('ExamOrderDetail')->BaseSave($oderDetailData);
            if (!$res) {
                throw new \Exception('添加订单详情失败');
            }

            $enrollres = model('ExamEnroll')->BaseUpdate($enrollData, $map);
            if (!$enrollres) {
                throw new \Exception('报名状态修改失败');
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }

    }

    /**
     * 缓缴费
     * @param array $where
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/30}~{11:16}
     */
    public function getDetailById($where = [], $field = "", $group = "user_login.id_card", $order = "")
    {
        return $this
            ->join("__EXAM_ORDER_DETAIL__", "exam_order.id=exam_order_detail.order_id", 'left')
            ->join("__EXAM_ENROLL__", "exam_order_detail.enroll_id=exam_enroll.id", 'left')
            ->join("__ORGANIZE__", "organize.id=exam_enroll.organize_id", 'left')
            ->join("__WORK__", "exam_enroll.work_id=work.id", 'left')
            ->join("__USER_LOGIN__", "exam_enroll.user_login_id=user_login.id", 'left')
            ->join("__REVIEW_LOG__", "exam_order.id=review_log.reviewed_id and review_log.reviewed_type=5", "left")
            ->join('__ADMIN__', 'review_log.review_id=admin.id and review_log.review_type=1', 'left')
            ->field($field)
            ->where($where)
            ->where("`exam_order_detail`.`delete_time` IS NULL AND exam_enroll.`delete_time` IS NULL AND organize.`delete_time` IS NULL AND work.`delete_time` IS NULL AND user_login.`delete_time` IS NULL")
            ->group($group)
            ->order($order)
            ->select();
    }

    /**
     * @param $paginate
     * @param array $param
     * @param array $field
     * @param string $order
     * @param string $group
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/20~14:40
     */
    public function getDataJoinLog($paginate, $param = [], $field = [], $order = '', $group = '')
    {
        !empty($paginate[0]) ?: $paginate[0] = config('paginate.list_rows');
        !empty($paginate[1]) ?: $paginate[1] = false;
        if (!empty($paginate[2])) {
            list($listRows, $simple, $config) = $paginate;
        } else {
            list($listRows, $simple) = $paginate;
        }
        return $this
            ->join("__REVIEW_LOG__", "exam_order.id=review_log.reviewed_id and review_log.reviewed_type=5", "left")
            ->join("__EXAM_PLAN__", "exam_order.exam_plan_id=exam_plan.id", "left")
            ->where($param)
            ->field($field)
            ->group($group)
            ->order($order)
            ->paginate($listRows, $simple, $config);
    }

    /**
     * [机构缓缴费订单]
     */
    public function addOrders($orderData, $enrollWhere, $orderDetail, $data)
    {
        $this->startTrans();
        try {
            $orderid = $this->BaseSave($orderData, true);
            if (!$orderid) {
                throw new \Exception('添加订单失败');
            }
            $res = $this->addOrderDetail($orderDetail, $orderid);
            if ($res == false) {
                throw new \Exception('添加订单详情失败');
            }

            $enrollres = model('ExamEnroll')->BaseUpdate($data, $enrollWhere);
            if (!$enrollres) {
                throw new \Exception('报名状态修改失败');
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    public function addOrderDetail($orderDetail, $order_id)
    {
        foreach ($orderDetail as $key => $val) {
            $val['create_time'] = time();
            $val['order_id']    = $order_id;
            $val['total_money'] = $val['lilun_price'] + $val['shicao_price'] + $val['zonghe_price'];
            $val['status']      = 1;
            $res                = model('ExamOrderDetail')->BaseSave($val);
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    public function invoiceOrder($map, $field)
    {

        $join         = array(
            ['__INVOICE__ i', "eo.id=i.order_id", 'left'],
        );
        $paginate     = array(
            config('paginate.list_rows'),
            false,
        );
        $examJoinData = $this->BaseJoinSelectPage($paginate, 'eo', $join, $map, [$field], 'eo.id desc');
        return $examJoinData;
    }

    public function updateOrder($enrollWhere, $enrollData, $orderData)
    {
        $this->startTrans();
        try {
            $orderid = $this->BaseUpdate($orderData, ['exam_plan_id' => $enrollWhere['exam_plan_id']]);
            if (!$orderid) {
                throw new \Exception('修改订单失败');
            }

            $enrollres = model('ExamEnroll')->BaseUpdate($enrollData, $enrollWhere);
            if (!$enrollres) {
                throw new \Exception('报名状态修改失败');
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    /**
     * [record 查询缓缴费的审核记录]
     * @param  [array] $arrWhere [description]
     * @return [type]           [description]
     */
    public function record($arrWhere, $field = [])
    {
        $join = [
            ['__REVIEW_LOG__ rl', 'rl.reviewed_id=eo.id'],
            ['__ADMIN__ a', 'rl.review_id=a.id'],
            ['__ORGANIZE__ o', 'o.id=eo.type_id'],
        ];

        $data = $this->BaseJoinSelect('eo', $join, $arrWhere, [$field], 'rl.create_time');

        return $data;
    }

    /**
     * [addOrder 缴费订单二次修改]
     * @param [type] $enrollData      [报名表修改数据]
     * @param [type] $orderdata       [订单表修改数据]
     * @param [type] $map            [修改条件]
     */
    public function orderUpdate($enrollData, $orderData, $map)
    {
        // 启动事务
        $this->startTrans();
        try {
            $orderid = $this->BaseUpdate($orderData, ['id' => $map['id']]);
            if (!$orderid) {
                throw new \Exception('修改订单失败');
            }

            $enrollres = model('ExamEnroll')->BaseUpdate($enrollData, $map['enroll']);
            if (!$enrollres) {
                throw new \Exception('报名状态修改失败');
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }

    }


    /**
     * 修改订单状态 并且 添加 一个订单支付记录
     * @param $data
     * @param $time
     * @return bool|string
     * @throws \think\exception\PDOException
     * @user 李海江 2018/12/18~8:27 PM
     */
    public function modifyOrderStatus($data, $time)
    {
        $this->startTrans();
        try {
            $res = $this->BaseUpdate(['pay_state' => 4], ['order_num' => $data['r6_Order']]);
            if ($res < 1) if (!$res) throw new \Exception('订单提交失败');
            //添加订单支付记录
            $array['water_no']  = $data['r2_TrxId'];
            $array['order_num'] = $data['r6_Order'];
            $array['pay_money'] = $data['r3_Amt'];
            $array['pay_time']  = $time;
            //添加订单支付记录
            $ordRes = $this->SExamOrderPay->addOrderPay($array);
            if ($ordRes < 1) throw new \Exception('创建订单是失败');
            //提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }

    }

    /**
     * 获取指定的一条订单
     * @param $data
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/12/18~6:03 PM
     */
    public function getOne($data)
    {
        return $this->BaseFind($data);
    }

}