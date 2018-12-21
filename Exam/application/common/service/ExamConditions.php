<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/7
 * Time: 6:22 PM
 */

namespace app\common\service;

use app\common\model\ExamConditions as MExamConditions;

/**
 * Class ExamConditions
 * @package app\common\service
 */
class ExamConditions extends MExamConditions
{
    /**
     * 查找一条指定的数据
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/7~6:23 PM
     */
    public function showOne($map, $field = [])
    {
        return $this->BaseFind($map, $field);
    }
}