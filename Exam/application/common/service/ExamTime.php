<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/12
 * Time: 11:01
 */


namespace app\common\service;

use app\common\model\ExamTime as MExamTime;

class ExamTime extends MExamTime
{
    /**
     * @param $data
     * 添加数据
     */
    public function addData($data)
    {
        return $this->BaseSave($data);
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * 查询数据
     */
    public function getBase($where)
    {
        return $this->BaseFind($where);
    }

    public function getAll($where)
    {
        return $this->BaseSelect($where);
    }

    /**
     * @param $where
     * @return int
     * 删除数据
     */
    public function deleteInfo($where)
    {

        return $this->where($where)->delete();
    }

}