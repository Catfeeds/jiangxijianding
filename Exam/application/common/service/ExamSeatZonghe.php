<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/8
 * Time: 13:28
 */
namespace app\common\service;


use app\common\model\ExamSeatZonghe as MExamSeatZonghe;
use think\Db;

class ExamSeatZonghe extends MExamSeatZonghe
{
    /**use
     * @param $data
     * @return mixed|string
     * 插入
     */
    public function addInfo($data)
    {
        return $this->BaseSave($data);
    }
    /**use
     * @param $where
     * @return int
     * 删除数据
     */
    public function deleteEnroll($where)
    {
        return $this->BaseDelete($where);
    }
}