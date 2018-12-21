<?php

namespace app\common\model;


/**
 * Class Role
 * @package app\common\model
 */
class Role extends BaseModel
{


    /**
     * 模型关联 角色 - 用户
     * @return \think\model\relation\BelongsToMany
     * @user 李海江 2018/9/20~下午9:19
     */
    public function admin()
    {
        return $this->belongsToMany('admin', 'admin_role', 'admin_id', 'role_id');
    }


    /**
     * status 获取器
     * @param $status
     * @return string
     * @user 李海江 2018/8/30~下午6:19
     */
    public function getStatusAttr($status)
    {
        //1:正常；-1：删除(冻结、解冻)
        switch ($status) {
            case 1:
                return '<b style="color:green;">启用</b>';
                break;
            case 0:
                return '<b style="color:red;">禁用</b>';
                break;
            default:
                return '未知';
                break;
        }
    }

}