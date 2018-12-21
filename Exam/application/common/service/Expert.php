<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/8
 * Time: 9:50 AM
 */

namespace app\common\service;

use app\common\model\Expert as MExpert;

/**
 * Class Expert
 * @package app\common\service
 */
class Expert extends MExpert
{

    /**
     * 获取所有专家 分页
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/8~10:16 AM
     */
    public function getAll($map, $field = [], $order = 'id desc')
    {
        $page = ['', '', ['query' => request()->param()]];
        return $this->BaseSelectPage($page, $map, $field, $order);
    }

    /**
     * 获取指定一条
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/8~11:43 AM
     */
    public function getOne($map, $field = [])
    {
        return $this->BaseFind($map, $field);
    }

    /**
     * 更新数据
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/11/8~3:03 PM
     */
    public function edit($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 添加
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/11/28~10:17 AM
     */
    public function add($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseSave($data);
    }

}