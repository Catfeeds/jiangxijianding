<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/19
 * Time: 10:05
 */

namespace app\common\service;

use app\common\model\ExamSchedule as MExamSchedule;
use think\Db;

class ExamSchedule extends MExamSchedule
{
    /**
     * @param $where
     * @param $field
     * @param $order
     * @return array|false|\PDOStatement|string|\think\Model
     * 基础数据查询
     */
    public function getBaseInf($where,$field='',$order='')
    {
        return $this->BaseFind($where,$field,$order);
    }

    /**
     * @param $data
     * @return mixed|string
     * 添加数据
     */
    public function addInfo($data)
    {
        return $this->BaseSave($data);
    }

    /**
     * @param $where
     * @return int
     * 删除数据
     */
    public function deInfo($where)
    {
        return $this->where($where)->delete();
    }
}