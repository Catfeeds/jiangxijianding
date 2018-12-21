<?php

namespace app\common\model;

/**
 * Class Jury
 * @package app\common\model
 */
class Jury extends BaseModel
{

    /**
     * 模型关联 考评人员 - 机构
     * @return \think\model\relation\BelongsTo
     * @user 李海江 2018/12/4~11:14 AM
     */
    public function organize()
    {
        return $this->belongsTo('organize', 'organize_id', 'id');
    }

    public function getCreateTimeAttr($time)
    {
        return date('Y年m月d日 H:i:s', $time);
    }
}