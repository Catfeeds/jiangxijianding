<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/6
 * Time: 5:00 PM
 */

namespace app\common\service;

use app\common\model\JuryCertificate as MJuryCertificate;

/**
 * Class JuryCertificate
 * @package app\common\service
 */
class JuryCertificate extends MJuryCertificate
{

    /**
     * 保存所有
     * @param $data
     * @return int|string
     * @user 李海江 2018/12/6~6:00 PM
     */
    public function add($data)
    {
        return $this->BaseSaveAll($data);
    }

    /**
     * 获得所有
     * @param $param
     * @param $field
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/12/6~9:39 PM
     */
    public function getAll($param, $field = [])
    {
        return $this->BaseSelect($param, $field);
    }
}