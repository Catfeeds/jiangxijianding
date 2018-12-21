<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/8
 * Time: 13:28
 */
namespace app\common\service;


use app\common\model\ExamSeatShicao as MExamSeatShicao;
use think\Db;

class ExamSeatShicao extends MExamSeatShicao
{
    /**use
     * @param $data
     * @return mixed|string
     * 插入数据
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
