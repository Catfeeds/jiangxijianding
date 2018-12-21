<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 15:45
 */

namespace app\common\model;


class ReviewLog extends BaseModel
{
    public function getDataByNum($param = [], $field = [], $order = '',$group = '')
    {
        return $this
            ->join("__EXAM_ORDER__","exam_order.id=review_log.reviewed_id and review_log.reviewed_type=5","right")
            ->where($param)
            ->field($field)
            ->group($group)
            ->order($order)
            ->find();
    }


}