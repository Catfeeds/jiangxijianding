<?php
namespace app\common\model;

class LearningPaperHistory extends BaseModel
{
    public $level;
    public $start_time;
    public function __construct($data = [])
    {
        parent::__construct($data);
        if (count($data) > 0) {
            $level  = config('EnrollStatusText.work_level_subject_level');
            if(key_exists('level_name',$data)){
                $this->level = $level[$data['level_name']];
            }
            if (key_exists('start_time', $data)) {
                $this->start_time = date('Y-m-d H:i:s',$data['start_time']);
            }
        }

    }

    public function getStopTimeAttr($time)
    {
        return date('Y-m-d H:i:s',$time);
    }
}