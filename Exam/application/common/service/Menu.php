<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/20
 * Time: 下午3:33
 */

namespace app\common\service;

use app\common\model\Menu as MMenu;

/**
 * Class Menu
 * @package app\common\service
 */
class Menu extends MMenu
{

    /**
     * 获取所有菜单 平铺
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/9/22~下午9:22
     */
    public function getAllMenu($param = [])
    {
        $allMenuData = $this->BaseSelect($param, '', 'sort desc');
        //获取所有权限
        return $allMenuData;
    }

    /**
     * 获取所有菜单的树形结构
     * @return array
     * @user 李海江 2018/9/20~下午3:36
     */
    public function getAllMenuTree($param = [])
    {
        $arrayAllMenuData = $this->getAllMenu($param);
        //创建树形结构
        return getTree($arrayAllMenuData);
    }

    /**
     * 获取所有当前用户所拥有的菜单的树形结构
     * @user 李海江 2018/9/20~下午3:46
     */
    public function getAllCheckMenuTree($rules, $param = [])
    {
        if (is_string($rules)) {
            $rules = explode(',', $rules);
        }
        $arrayAllMenuData = $this->getAllMenu($param);
        $myMenuData = array();
        foreach ($arrayAllMenuData as $k => $v) {
            foreach ($rules as $kk => $vv) {
                if ($v['id'] == $vv) {
                    $myMenuData[] = $v;
                }
            }
        }

        return getTree($myMenuData);
    }

    /**
     * 添加菜单
     * @param $array
     * @return mixed|string
     * @user 李海江 2018/9/25~上午9:44
     */
    public function add($array)
    {
        $intLast_id = $this->BaseSave($array);
        return $intLast_id;
    }

    /**
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/9/26~10:54 AM
     */
    public function findMenuData($id)
    {
        return $this->BaseFind(['parent_id' => $id]);

    }

    /**
     * 删除菜单
     * @param array $map 范围
     * @return int|string
     * @user 李海江 2018/9/25~上午9:53
     */
    public function deleteMenu($map)
    {
        return $this::destroy($map, false);
    }

    /**
     * 编辑菜单
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/9/27~7:09 PM
     */
    public function editMenu($data)
    {
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 获取指定菜单
     * @param int $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/9/26~10:54 AM
     */
    public function showMenu($id)
    {
        return $this->BaseFind(['id' => $id]);
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * 获取指定菜单
     */
    public function showMenuForName($where)
    {
        return $this->BaseFind($where);
    }
}