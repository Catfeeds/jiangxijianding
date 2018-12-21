<?php
namespace app\common\model;

class LearningTopicSimulation extends BaseModel
{
    public $level_id;
    public function __construct($data = [])
    {
        parent::__construct($data);
        if(count($data)>0) {

            if (key_exists('level_id', $data)) {
                $level_name = ['0'=> '无', '1'=>'高级技师','2'=>'技师','3'=>'高级','4'=>'中级','5'=>'初级'];
                $this->level_id = $level_name[$data['level_id']];
            }
        }
    }
}