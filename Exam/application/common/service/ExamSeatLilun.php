<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/8
 * Time: 13:28
 */
namespace app\common\service;


use app\common\model\ExamSeatLilun as MExamSeatLilun;
use think\Db;

class ExamSeatLilun extends MExamSeatLilun
{
    /**use
     * @param $data
     * @return mixed|string
     * 插入信息
     */
    public function addInfo($data)
    {
        return $this->BaseSave($data);
    }

    /**use
     * @param $where
     * 获取考三科考生
     */
    public function getAllEnroll($where)
    {
        return $this->field('exam_seat_lilun.id,exam_seat_lilun.user_id,exam_seat_lilun.work_id,exam_seat_lilun.level_id,exam_seat_lilun.direction_id,exam_seat_lilun.organize_id,exam_seat_lilun.exam_plan_id')
                     ->join('exam_seat_zonghe','exam_seat_lilun.work_id=exam_seat_zonghe.work_id and exam_seat_lilun.level_id=exam_seat_zonghe.level_id and exam_seat_lilun.direction_id=exam_seat_zonghe.direction_id and exam_seat_lilun.user_id=exam_seat_zonghe.user_id','left')
                     ->join('exam_seat_shicao','exam_seat_lilun.work_id=exam_seat_shicao.work_id and exam_seat_lilun.level_id=exam_seat_shicao.level_id and exam_seat_lilun.direction_id=exam_seat_shicao.direction_id and exam_seat_lilun.user_id=exam_seat_shicao.user_id','left')
                     ->where($where)
                     ->order('exam_seat_lilun.direction_id,exam_seat_lilun.user_id')
                     ->select();
    }

    /**
     * @param $where
     * @return int
     * 删除数据
     */
    public function deleteEnroll($where)
    {
        return $this->BaseDelete($where);
    }
}
