<?php

namespace app\common\model;

/**
 * Class Work
 * @package app\common\model
 */
class Work extends BaseModel
{
    public $work_level;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $work_level = config('EnrollStatusText.work_level_subject_level');
        if (count($data) > 0) {
            if (key_exists('work_level', $data)) {
                $this->work_level = $work_level[$data['work_level']];
            }
        }
    }

    /**
     * @param $status
     * @return string
     * @user 李海江 2018/9/27~5:07 PM
     */
    public function getStatusAttr($status)
    {
        switch ($status) {
            case 1:
                return '<b style="color: green;">启用</b>';
                break;
            case 0:
                return '<b style="color: red;">禁用</b>';
                break;
            default:
                return '未知';
                break;
        }
    }

    /**
     * 模型关联 工种 - 类型
     * @user 李海江 2018/9/28~1:41 PM
     */
    public function workType()
    {
        return $this->hasOne('workType', 'id', 'work_type_id');
    }


    /**
     * 模型关联 工种 - 等级 - 科目
     * @user 李海江 2018/9/28~6:13 PM
     */
    public function workLevelSubject(){
        return $this->belongsToMany('Subject', 'work_level_subject', 'subject_id', 'work_id');
    }

    /**
     * 模型关联 工种 - 方向
     * @user 李海江 2018/9/28~2:16 PM
     */
    public function workDirection()
    {
        return $this->hasMany('WorkDirection','work_id','id');
    }
}