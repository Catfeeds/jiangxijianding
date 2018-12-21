<?php

namespace app\common\service;

use app\common\model\LearningHour as MLearningTime;

/**
 * Class LearningHour
 * @package app\common\service
 */
class LearningHour extends MLearningTime
{
    /**
     * 获得学时
     * @param $param
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/12/3~6:04 PM
     */
    public function getHour($param)
    {
        return $this->BaseSelect($param, ['sum(hours) as hours'], '', '', 'enroll_id');
    }
}