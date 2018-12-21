<?php
namespace app\common\service;
use app\common\model\LearningCollection as MlearningCollection;

class LearningCollection extends MlearningCollection
{
    public function select($where=[],$field =[],$order='id desc')
    {
        return $this->BaseSelect($where,$field,$order);
    }

    public function getList($where, $field='*')
    {



        return $this
            ->join('__LEARNING_MEDIA__','learning_collection.topic_id = learning_media.id')
            ->field($field)
            ->where($where)
            ->select();
    }
}