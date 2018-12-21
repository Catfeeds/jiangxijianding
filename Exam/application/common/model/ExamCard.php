<?php
namespace app\common\model;

class ExamCard extends BaseModel
{

    public $id_type;
    public $level;

    public function __construct($data=[])
    {
        parent::__construct($data);
        $id_type = config('EnrollStatusText.id_type');
        $level = config('EnrollStatusText.work_level_subject_level');
        if(count($data)>0){
            if(key_exists('id_type',$data)){
                $this->id_type = $id_type[$data['id_type']];
            }
            if (key_exists('level', $data)) {
                $this->level = $level[$data['level']];
            }
        }
    }


    /**
     * @param $time
     * @return false|string
     * @user xuweiqi
     */
    public function getPrintStarttimeAttr($time)
    {
        return date('Y-m-d H:i',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user xuweiqi
     */
    public function getPrintEndtimeAttr($time)
    {
        return date('Y-m-d H:i',$time);
    }

    /**
     * 模型关联 准考证 - 报名表
     * @return \think\model\relation\HasOne
     * @user xuweiqi
     */
    public function enrollinfo()
    {
            return $this->hasOne('exam_enroll', 'id', 'enroll_id');
    }


}