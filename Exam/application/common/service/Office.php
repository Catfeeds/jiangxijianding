<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/30
 * Time: 9:22 AM
 */

namespace app\common\service;

use app\common\model\Office as MOffice;

/**
 * Class Office
 * @package app\common\service
 */
class Office extends MOffice
{
    /**
     * 获取所有科室 分页
     * @return mixed
     * @user 李海江 2018/10/18~10:14 AM
     */
    public function getAll($param, $field = [], $order = 'id desc')
    {
        $list = $this->BaseSelectPage(['', '', ['query' => request()->param()]], $param, $field, $order);
        return $list;
    }


    /**
     * 返回所有科室 不分页
     * @param $param
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/27~9:27 PM
     */
    public function getAlls($param)
    {
        $list = $this->BaseSelect($param);
        return $list;
    }

    /**
     * 获取一个科室
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/9~11:08 AM
     */
    public function getOne($map)
    {
        return $this->BaseFind($map);
    }

    /**
     * 添加科室
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/11/9~11:08 AM
     */
    public function add($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseSave($data);
    }

    /**
     * 科室修改
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/11/9~11:40 AM
     */
    public function edit($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 科室删除
     * @param $data
     * @return int
     * @user 李海江 2018/11/9~11:08 AM
     */
    public function del($data)
    {
        return $this::destroy($data);
    }
}