<?php
namespace app\common\model;

class ExamOrderDetail extends BaseModel
{

    public $level;

    public function __construct($data=[])
    {
        parent::__construct($data);
        $level      = config('EnrollStatusText.work_level_subject_level');
        if(count($data)>0){
            if(key_exists('work_level_subject_level',$data)){
                $this->level = $level[$data['work_level_subject_level']];
            }
        }
    }
}