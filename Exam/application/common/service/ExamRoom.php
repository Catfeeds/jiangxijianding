<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 14:16
 */

namespace app\common\service;

use app\common\model\ExamRoom as MExamRoom;
use think\Db;

class ExamRoom extends MExamRoom
{
    /**use
     * 添加数据
     */
    public function addRoom($data)
    {
        return $this->insert($data);
    }

    /**
     * 获取work，level 对应的考场
     */
    public function getRoom($work,$level,$roomCount)
    {
        return $this
                ->where('work_id','=',$work)
                ->where('level','=',$level)
                ->where('seatCount','<',$roomCount)
                ->find();
    }

    /**
     * 获取科目工种级别对象的空余考场
     */
    public function getRoomInfo($map,$work_id='',$level='',$LiLun='',$ZongHe='',$ZhuanYe='')
    {
        return $this
                ->join('__EXAM_SEAT__','exam_seat.ExamRoomNum=exam_room.id')
                ->where($work_id)
                ->where($level)
                ->where('exam_seat.exam_plan_id','=',$map['exam_plan_id'])
                ->where('exam_room.seatCount','<',$map['count'])
                ->where($LiLun)
                ->where($ZongHe)
                ->where($ZhuanYe)
                ->find();
    }

    /**
     * 查询考场号
     */
    public function getRoomNum($plan,$plans)
    {
        $where['ExamSiteId'] = $plan[0]['id'];
        $where['exam_plan_id'] = $plans['id'];
        return $this->BaseFind($where,'','id desc');
    }

    /**
     * 获取没坐满考场
     */

    public function getLastRoom($plan,$plans,$subject,$roomCount)
    {
        $map['seatCount'] = array('<',$roomCount);
        $map['ExamSiteId'] = $plan[0]['id'];
        $map['exam_plan_id'] = $plans['id'];
        $result = $this->BaseSelect($map);
        $return_data = '';
        foreach($result as $k=>$v)
        {
            if($v['work_id'] == $subject['work_id'] && $v['level'] == $subject['level'])
            {
                $return_data = $v;
            }
        }
        if(!$return_data)
        {
            foreach($result as $k=>$v)
            {
                if($v['work_id'] == $subject['work_id'] )
                {
                    $return_data = $v;
                }
            }
        }
        if(!$return_data)
        {
            foreach($result as $k=>$v)
            {
                 return $v;

            }
        }
        return $return_data;
    }

    public function getEnd($plan,$plans)
    {
        $map['ExamSiteId'] = $plan[0]['id'];
        $map['exam_plan_id'] = $plans['id'];
        return $this->BaseFind($map,'','examRoomNum desc');
    }

    public function getRoomPlanOrganize($map)
    {
        return $this->BaseSelect($map);
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * 单个查询
     */
    public function getBaseInfo($where,$field='',$order='')
    {
        return $this->BaseFind($where,$field,$order);
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