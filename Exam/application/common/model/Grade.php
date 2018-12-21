<?php
namespace app\common\model;

class Grade extends BaseModel
{

    public $result;
    public $level;
    public $grade;
    public $theory_score_result;
    public $watch_score_result;
    public $synthesize_score_result;

    public function __construct($data=[])
    {
        parent::__construct($data);
        $arrexamresult = config('EnrollStatusText.result');
        $level = config('EnrollStatusText.work_level_subject_level');
        $grade = config('EnrollStatusText.grade');
        if(count($data)>0){
            if(key_exists('result',$data)){
                $this->result = $arrexamresult[$data['result']];
            }
            if (key_exists('level', $data)) {
                $this->level = $level[$data['level']];
            }
            if (key_exists('theory_score_result', $data)) {
                $this->theory_score_result = $grade[$data['theory_score_result']];
            }
            if (key_exists('watch_score_result', $data)) {
                $this->watch_score_result = $grade[$data['watch_score_result']];
            }
            if (key_exists('synthesize_score_result', $data)) {
                $this->synthesize_score_result = $grade[$data['synthesize_score_result']];
            }
        }
    }


    /**
     * 模型关联 成绩表 - 方向表
     * @return \think\model\relation\HasOne
     * @user xuweiqi
     */
    public function directioninfo()
    {
        return $this->hasOne('work_direction', 'id', 'work_direction_id');
    }



}
