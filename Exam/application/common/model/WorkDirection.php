<?php
namespace app\common\model;

/**
 * Class WorkDirection
 * @package app\common\model
 */
class WorkDirection extends BaseModel
{

    /**
     * @param $status
     * @return string
     * @user 李海江 2018/10/9~11:05 AM
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
     * 模型关联 工种方向 属于 工种
     * @return \think\model\relation\BelongsTo
     * @user 李海江 2018/10/9~11:05 AM
     */
    public function work()
    {
        return $this->belongsTo('work','work_id','id');
    }
}