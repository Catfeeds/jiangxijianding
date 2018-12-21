<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午5:35
 */
namespace app\common\service;

use app\common\model\AdminRole as MAdminRole;

/**
 * Class AdminRole
 * @package app\common\service
 */
class AdminRole extends MAdminRole
{

    /**
     * 通过uid更改用户角色
     * @param $roleId
     * @param $data
     * @return bool
     * @user 李海江 2018/11/29~7:25 PM
     */
    public function editAdminRole($roleId, $data)
    {
        $insertData = array_string_megre($roleId, 'role_id', $data['id'], 'admin_id');

        $this->transaction(function() use ($data,$insertData){
            $this->deleteAdminRole($data['id']);
            $this->addAdminRole($insertData);
            $this->commit();
        });
    }

    /**
     * 查找用户角色表查看是否有当前关系
     * @param $uid
     * @param $adminRoleId
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/10/19~1:41 PM
     */
    public function findAdminRole($param)
    {
        return $this->BaseSelectCount($param);
    }

    /**
     * 删除 用户-角色 关系
     * @param $param
     * @return int
     * @user 李海江 2018/10/19~4:04 PM
     */
    public function deleteAdminRole($id)
    {
        $res = $this->BaseDelete(['admin_id'=>$id]);
        return $res;
    }

    /**
     * 创建用户、角色关系
     * @param $data
     * @return int|string
     * @user 李海江 2018/10/19~1:46 PM
     */
    public function addAdminRole($data)
    {
        return $this->BaseSaveAll($data);
    }

}