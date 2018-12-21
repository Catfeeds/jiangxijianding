<?php
namespace app\common\service;
use app\common\model\LearningCollect as MlearningCollect;

class LearningCollect extends MlearningCollect
{
    public function select($where='')
    {
        return $this
            ->where($where)
            ->order('id', 'desc')
            ->select();
    }
}