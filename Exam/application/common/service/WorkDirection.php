<?php

namespace app\common\service;

use app\common\model\WorkDirection as MWorkDirection;

/**
 * Class WorkDirection
 * @package app\common\service
 */
class WorkDirection extends MWorkDirection
{
    /**
     * 获取所有方向
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/10/9~9:43 AM
     */
    public function getAll($param = '', $map = '', $order = 'id desc')
    {
        $data = $this->BaseSelectPage(['', '', ['query' => $param]], $map, '', $order)->each(function ($item) {
            $item->dataStatus = $item->getData('status');
        });

        return $data;
    }

    /**
     * 展示一个
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/10/9~12:06 PM
     */
    public function show($id)
    {
        $data = $this->BaseFind(['id' => $id]);
        return $data;
    }

    /**
     * 修改方向
     * @param $data
     * @param $param
     * @return array
     * @user 李海江 2018/10/9~5:31 PM
     */
    public function edit($data, $param)
    {
        $intRes = $this->BaseUpdate($data, $param);
        $msg = $intRes ? '操作成功' : '操作失败';
        return layuiMsg($intRes, $msg);
    }

    /**
     * 删除工种方向
     * @param $map
     * @return int
     * @user 李海江 2018/10/10~2:34 PM
     */
    public function deleteWorkDir($map)
    {
        return $this::destroy($map, false);
    }

    /**
     * 添加工种
     * @user 李海江 2018/10/10~2:34 PM
     */
    public function doadd($data)
    {
        return $this->BaseSaveAll($data);
    }

    public function getAllInfo()
    {
        return $this->select();
    }
}