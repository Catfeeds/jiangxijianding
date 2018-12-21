<?php
namespace app\common\model;

class LearningTopicPaper extends BaseModel
{
    public $level_id;
    public $learning_paper_history_start_time;
    public function __construct($data = [])
    {
        parent::__construct($data);
        if(count($data)>0) {
            if (key_exists('level_id', $data)) {
                $level_name = config("EnrollStatusText.work_level_subject_level");
                $this->level_id = $level_name[$data['level_id']];
            }
            if (key_exists('learning_paper_history_start_time', $data)) {
                $this->learning_paper_history_start_time = date('Y-m-d H:i:s',$data['learning_paper_history_start_time']);
            }
        }
    }
}