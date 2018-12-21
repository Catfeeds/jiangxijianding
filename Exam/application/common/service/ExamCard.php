<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/5
 * Time: 14:22
 */

namespace app\common\service;

use app\common\model\ExamCard as MExamCard;

class ExamCard extends MExamCard
{
    public function getCardInfo($map)
    {
        $where['enroll_id'] = $map['enroll_id'];
        $where['exam_plan_id'] = $map['exam_plan_id'];
        return $this->BaseFind($where);
    }
}