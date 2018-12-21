<?php

namespace app\common\model;

/**
 * Class Admin
 * @package app\common\model
 */
class Admin extends BaseModel
{

    /**
     * 用户 - 角色 远程关联
     * @return \think\model\relation\BelongsToMany
     * @user 李海江 2018/9/19~上午10:22
     */
    public function role()
    {
        return $this->belongsToMany('role', 'admin_role', 'role_id', 'admin_id');
    }

    /**
     * 用户 - 鉴定中心 关联
     * @return \think\model\relation\BelongsTo
     * @user 李海江 2018/10/19~9:25 AM
     */
    public function center()
    {
        return $this->belongsTo('exam_center','exam_center_id','id');
    }

    /**
     * 用户 - 用户角色关系表 关联
     * @user 李海江 2018/9/19~上午10:23
     */
    public function adminRole()
    {
        return $this->hasOne('adminRole', 'admin_id', 'id');
    }

    /**
     * 用户 - 科室 关联
     * @user 李海江 2018/9/19~上午10:23
     */
    public function office()
    {
        return $this->belongsTo('office', 'office_id', 'id');
    }

    /**
     * @param $status
     * @return string
     * @user 李海江 2018/9/19~上午10:23
     */
    public function getStatusAttr($status)
    {
        //1:正常；-1：删除(冻结、解冻)
        switch ($status) {
            case 1:
                return '<b style="color:green;">正常</b>';
                break;
            case 0:
                return '<b style="color:red;">冻结</b>';
                break;
        }
    }

}