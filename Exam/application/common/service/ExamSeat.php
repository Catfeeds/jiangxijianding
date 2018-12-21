<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/19
 * Time: 14:59
 */
namespace app\common\service;

use app\common\model\ExamSeat as MExamSeat;
use think\Db;

class ExamSeat extends MExamSeat
{
    public function getRoomEnroll($map)
    {
        return $this
                ->field('Level')
                ->where('work_id','=',$map['work_id'])
                ->where('Level','=',$map['Level'])
                ->where('exam_plan_id','=',$map['exam_plan_id'])
                ->group('Level')
                ->select();
    }

    public function getSeatInfo($map)
    {
        $where['exam_plan_id'] = $map['exam_plan_id'];
        $whereor['LiLun'] = $map['user_login_id'];
        $whereor['ZhuanYe'] = $map['user_login_id'];
        $whereor['ZongHe'] = $map['user_login_id'];
        return $this->where($where)->where(function ($query) use ($whereor){
            $query->whereor($whereor);
        })->find();
    }

    public function getSeat($map,$organize,$exam_plan,$type='',$batch='')
    {
        $where['exam_enroll.exam_plan_id'] = $exam_plan;
        $where['exam_enroll.site_id'] = $organize;
        $where['exam_enroll.work_id'] = $map['work_id'];
        if($map['level'] != null || $map['level']!=0)
        {
            $where['exam_enroll.work_level_subject_level'] = $map['level'];
        }
        if($batch)
        {
            $where['exam_seat.batch'] = $batch;
        }
        if(isset($map['ExamRoomNum']))
        {
            $where['exam_seat.ExamRoomNum'] = $map['ExamRoomNum'];
            $field = array('exam_seat.ExamRoomNum','exam_enroll.user_login_id','exam_seat.ExamSeatNum','exam_enroll.id','exam_seat.batch');
        }else{
            $field = array('exam_seat.ExamRoomNum','exam_seat.batch');
        }
        return $this->field($field)
                     ->join('__EXAM_ENROLL__',"exam_seat.$type=exam_enroll.id",'left')
                     ->where($where)
                     ->select();
    }

    /**use
     * @param $data
     * @return mixed|string
     * 保存数据
     */
    public function insertInfo($data)
    {
        return $this->BaseSave($data);
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * 查询
     */
    public function getBaseInfo($where,$field='',$order='')
    {
        return $this->BaseFind($where,'',$order);
    }

    /**
     * @param $data
     * @param $where
     * @return false|int|string
     * 更新
     */
    public function updateInfo($data,$where)
    {
        return $this->where($where)->update($data);
    }

    /**
     * @param $where
     * @param string $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllInfo($where,$field='',$order='')
    {
        return $this->BaseSelect($where,$field,$order);
    }

    public function getBatch($where,$field ='',$order = '',$group = '')
    {
        return $this->BaseSelect($where,$field,$order,'',$group);
    }

    public function getAllSeat($plan,$organize,$type)
    {
        return $this->field('exam_enroll.work_id,exam_enroll.work_level_subject_level')->join('__EXAM_ENROLL__','exam_enroll.id=exam_seat.'.$type,'left')->where(array('exam_enroll.exam_plan_id'=>$plan,'exam_enroll.organize_id'=>$organize))->select();
    }

    public function getRoomSeat($plan,$organize,$type,$examRoomNum)
    {
        return $this->field('exam_enroll.work_id,exam_enroll.work_level_subject_level')->join('__EXAM_ENROLL__','exam_enroll.id=exam_seat.'.$type,'left')->where(array('exam_enroll.exam_plan_id'=>$plan,'exam_enroll.organize_id'=>$organize,'ExamRoomNum'=>$examRoomNum))->group('exam_enroll.work_id,exam_enroll.work_level_subject_level')->select();
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

    /**
     * @param $where
     * @param string $field
     * @param $order
     * @param $group
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取考场信息
     */
    public function getPlanSite($where,$order,$group)
    {
        return $this->field('exam_seat.id,exam_seat.ExamSiteId,exam_seat.ExamRoomNum,exam_seat.exam_plan_id,exam_plan.title,organize.name,exam_time.start_time,exam_time.end_time')
                     ->join('__EXAM_PLAN__','exam_plan.id=exam_seat.exam_plan_id','left')
                     ->join('__ORGANIZE__','exam_seat.ExamSiteId=organize.id','left')
                     ->join('__EXAM_TIME__','exam_time.exam_plan_id=exam_seat.exam_plan_id and exam_time.exam_center=exam_seat.ExamSiteId and exam_seat.batch=exam_time.batch')
                     ->where($where)
                     ->order($order)
                     ->group($group)
                     ->select();
    }

    public function getEnrollInfo($where,$field='',$page=1,$pageCount)
    {
        $start = ($page -1) * $pageCount;
        return $this
                    ->join('__EXAM_ENROLL__','exam_enroll.id=exam_seat.ZongHe','left')
                    ->join('__EXAM_CARD__','exam_card.enroll_id=exam_seat.ZongHe and exam_seat.exam_plan_id=exam_card.exam_plan_id','left')
                    ->join('__GRADE__','exam_enroll.user_login_id=grade.user_login_id and exam_enroll.exam_plan_id=grade.exam_plan_id and exam_enroll.work_id=grade.work_id and exam_enroll.work_direction_id=grade.work_direction_id and exam_enroll.work_level_subject_level=grade.level','left')
                    ->join('__USER_LOGIN__','user_login.id=exam_enroll.user_login_id','left')
                    ->join('__USERINFO__','userinfo.user_login_id=exam_enroll.user_login_id','left')
                    ->field($field)
                    ->where($where)
                    ->limit($start,$pageCount)
                    ->select();
    }
}
