<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午6:13
 */

namespace app\common\service;

use app\common\model\ExamCenter as MExamCenter;

/**
 * Class ExamCenter
 * @package app\common\service
 */
class ExamCenter extends MExamCenter
{
    /**
     * 获取鉴定中心数据
     * @param $map
     * @return mixed
     */
    public function centerSiteAll($map, $field = ['*'])
    {
        $data = $this->BaseSelect($map, $field);
        return $data;
    }

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/22~9:59 AM
     */
    public function getOne($map)
    {
        return $this->BaseFind($map);
    }

    /**
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/11/22~9:59 AM
     */
    public function edit($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/11/22~9:59 AM
     */
    public function add($data)
    {
        return $this->BaseSave($data);
    }
}