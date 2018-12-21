<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/2
 * Time: 5:33 PM
 */

namespace app\common\service;

use app\common\model\CmsGuide as MCmsGuide;

/**
 * Class CmsGuide
 * @package app\common\service
 */
class CmsGuide extends MCmsGuide
{
    /**
     * 获取栏目信息
     * @param $param
     * @param bool $field
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/2~5:35 PM
     */
    public function getAll($param, $field = ['*'])
    {
        return $this->BaseSelect($param, $field);
    }
}