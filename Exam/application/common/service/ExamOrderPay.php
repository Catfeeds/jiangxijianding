<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/18
 * Time: 6:20 PM
 */

namespace app\common\service;

use app\common\model\ExamOrderPay as MExamOrderPay;

/**
 * Class ExamOrderPay
 * @package app\common\service
 */
class ExamOrderPay extends MExamOrderPay
{

    /**
     * 添加订单支付记录
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/12/18~6:33 PM
     */
    public function addOrderPay($data)
    {
        return $this->BaseSave($data);
    }
}