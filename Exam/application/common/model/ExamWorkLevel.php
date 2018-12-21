<?php
namespace app\common\model;

class ExamWorkLevel extends BaseModel
{
    public $work_level;

    public function __construct($data=[])
    {
        parent::__construct($data);
        $level = config('EnrollStatusText.work_level_subject_level');
        if(count($data)>0){
            if (key_exists('work_level', $data)) {
                $this->work_level = $level[$data['work_level']];
            }

        }
    }

}