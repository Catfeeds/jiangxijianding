<?php
namespace app\common\service;
use app\common\model\LearningTopicSimulation as MLearningTopicSimulation;

class LearningTopicSimulation extends MLearningTopicSimulation
{
    public function getList($field = '*',$where = '')
    {
        return $this
            ->join('__WORK__','`learning_topic_simulation`.work_id = `work`.id','left')
            ->join('__WORK_DIRECTION__','`work`.id = work_direction.work_id','left')
            ->field($field)
            ->where($where)
            ->select();
    }
}