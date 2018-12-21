<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午5:35
 */

namespace app\common\service;

use app\common\model\Role as MRole;

/**
 * Class Role
 * @package app\common\service
 */
class Role extends MRole
{

    /**
     * 查找角色是否存在
     * @param $name
     * @return bool
     * @user 李海江 2018/8/31~上午10:42
     */
    public function findRole($map)
    {
        $res = $this->BaseFind($map);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 创建角色 同时 赋予权限
     * @param $data
     * @return int
     * @user 李海江 2018/9/20~下午10:28
     */
    public function addRole($data)
    {

        $res = $this->findRole($data);
        //角色名已经存在
        if ($res) return -1;

        $role_id = $this->BaseSave($data);
        if ($role_id) {
            return 1;
        } else {
            return -2;
        }
    }

    /**
     * 修改角色 信息
     * @param $data
     * @param $id
     * @return false|int|string
     * @user 李海江 2018/9/20~下午10:00
     */
    public function editRole($data, $id)
    {

        $res = $this->BaseUpdate($data, ['id' => $id]);
        return $res;
    }

    /**
     * 获取所有角色(分页)
     * @param $param
     * @return mixed
     * @user 李海江 2018/10/22~11:59 AM
     */
    public function getAll($param, $field = [], $order = 'id desc')
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        //所有角色
        $list = $this->BaseSelectPage($paginate, $param, $field, $order)->each(function ($item) {
            $item->dataStatus = $item->getData('status');
        });
        return $list;
    }

    /**
     * 获取所有角色(不分页)
     * @return mixed
     * @user 李海江 2018/10/18~10:14 AM
     */
    public function getAllNoPage($param)
    {
        $list = $this->BaseSelect($param);
        return $list;
    }


    /**
     * 获取一个角色的信息
     * @param $param
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/10/22~12:11 PM
     */
    public function showOne($param)
    {
        return $this->BaseFind($param);
    }

}