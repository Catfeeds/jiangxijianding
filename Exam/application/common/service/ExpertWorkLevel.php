<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/11
 * Time: 1:35 PM
 */

namespace app\common\service;

use app\common\model\ExpertWorkLevel as MExpertWorkLevel;

/**
 * Class ExpertWorkLevel
 * @package app\common\service
 */
class ExpertWorkLevel extends MExpertWorkLevel
{
    /**
     * 获取专家的功能权限列表
     * @param $map
     * @param array $field
     * @return string
     * @user 李海江 2018/11/28~3:07 PM
     */
    public function getAll($map, $field = [])
    {
        $join = array(
            ['__WORK__ work', 'expert.id=work.id']
        );
        return $this->BaseJoinSelect('expert', $join, $map, $field);
    }

    /**
     * 获取一个条
     * @param $map
     * @param array $field
     * @return string
     * @user 李海江 2018/11/28~3:19 PM
     */
    public function getOne($map, $field = [])
    {
        $join = array(
            ['__WORK__ work', 'expert.id=work.id']
        );
        return $this->BaseJoinFind('expert', $join, $map, $field);
    }

    /**
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/11/28~3:54 PM
     */
    public function edit($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

}