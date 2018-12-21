<?php
namespace app\common\model;

class WorkLevelSubject extends BaseModel
{
    public $level;
    public function __construct($data = [])
    {
        parent::__construct($data);
        if(count($data)>0) {
            if (key_exists('level', $data)) {
                $level_name = config("EnrollStatusText.work_level_subject_level");
                $this->level = $level_name[$data['level']];
            }
        }
    }
}