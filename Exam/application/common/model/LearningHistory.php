<?php
namespace app\common\model;

class LearningHistory extends BaseModel
{
    public function getStopTimeAttr($time)
    {
        return date('Y-m-d H:i:s',$time);
    }

}