<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 15:29
 */
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\WorkLevelSubject;
use app\common\service\ExamPlan;
use app\common\service\ExamEnroll;
use app\common\service\ExamRoom;
use app\common\service\ExamSeat;
use app\common\service\Organize;

use think\Db;

class ArrangeController extends AdminBase
{
    /**数据初始化
     * ArrangeController constructor.
     */
    private $SWorkLevelSubject;
    private $SExamEnroll;
    private $SExamPlan;
    private $SExamRoom;
    private $SExamSeat;
    private $SOrganize;

    public function __construct()
    {
        parent::__construct();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SExamPlan = new ExamPlan();
        $this->SExamEnroll = new ExamEnroll();
        $this->SExamRoom = new ExamRoom();
        $this->SExamSeat = new ExamSeat();
        $this->SOrganize = new Organize();
    }

    /**
     * 考场编排
     * @return \think\response\View
     */
    public function index($map='',$field='',$subjectData='')
    {
        $this->getPlan();
        return view();
    }

    public function getPlan()
    {
        /**
         * 获取全部鉴定计划
         */
        $objects = $this->SWorkLevelSubject->getSubject();
        $plans = $this->SExamPlan->selectExamPlanData();
        foreach($plans as $k=>$v)
        {
            if($v['status'] ==1)
            {
                //获取全部考点
                $plan = $this->SOrganize->getAllOrganize($v['id']);
                foreach($plan as $kn=>$vn)
                {
                    foreach($objects as $ko=>$vo)
                    {
                        $map['work_id'] = $vo['work_id'];
                        $map['work_level_subject_level'] = $vo['level'];
                        $map['exam_plan_id'] = $v['id'];
                        $map['organize_id'] = $vn['id'];
                        //获取当前工种级别的考生
                        $enroll = $this->SExamEnroll->getOrganizeAll($map);
                        //$this->range($enroll,$vo,$plan,$v);
                       if($enroll)
                       {
                            $this->text($enroll,$map,$vo,$plan,$v);
                       }
                    }
                }
            }
        }
    }

    public function text($enroll,$map,$subject,$plan,$plans)
    {
        $map['ExamSiteId'] = $plan[0]['id'];
        $map['exam_plan_id'] = $plans['id'];
        $map['count'] = $plan[0]['roomCount'];
        $sub = $this->SWorkLevelSubject->getAllForWorkLevel($subject['work_id'],$subject['level']);
        $work_id['exam_seat.work_id'] = $subject['work_id'];
        $level['exam_seat.level'] = $subject['level'];
        $this->range($enroll,$subject,$plan,$plans,'',$sub);
    }

    public function range($enroll,$subject,$plan,$plans,$result='',$sub)
    {
        if(!empty($enroll))
        {
            //不考虑补考
            if($subject['count_subject'] ==3 || $subject['count_subject'] ==2)
            {
                $this->subjectThree($enroll,$result,$plan,$plans,$subject,$sub);
            }elseif ($subject['count_subject'] ==1)
            {
                $this->subjectThree($enroll,$result,$plan,$plans,$subject,$sub,1);
            }
        }
    }

    public function seatData($plan,$plans,$v,$Num,$seatNum,$LiLun='',$ZongHe='',$ZhuanYe='')
    {
        $data['ExamSiteId'] = $plan[0]['id'];
        $data['exam_plan_id'] = $plans['id'];
        $LiLun != null ? $data['LiLun'] = $v['id'] : '';
        $ZongHe != null ? $data['ZongHe'] = $v['id'] : '';
        $ZhuanYe != null ? $data['ZhuanYe'] = $v['id'] : '';
        $data['ExamRoomNum'] = $Num;
        $data['ExamSeatNum'] = $seatNum;
        $data['work_id'] = $v['work_id'];
        $data['Level'] = $v['work_level_subject_level'];
        $data['work_direction_id'] = $v['work_direction_id'];
        return $data;
    }

    public function roomData($plan,$Num,$seatNum,$plans,$subject)
    {
        $data_room['ExamSiteId'] = $plan[0]['id'];
        $data_room['examRoomNum'] = $Num;
        $data_room['seatCount'] = $seatNum;
        $data_room['exam_plan_id'] = $plans['id'];
        $data_room['work_id'] = $subject['work_id'];
        $data_room['level'] = $subject['level'];
        return $data_room;
    }

    public function subjectThree($enroll,$result,$plan,$plans,$subject,$sub='',$type='')
    {
        $Num = 1;
        $seatNum = 1;
        $LiLun = '';
        $ZhuanYe = '';
        $ZongHe = '';
        foreach ($sub as $k=>$v)
        {
            if($v['subject_id'] ==1)
            {
                $LiLun = 1;
            }elseif ($v['subject_id'] ==2)
            {
                $ZongHe = 1;
            }elseif ($v['subject_id'] ==3)
            {
                $ZhuanYe =1;
            }
        }
        //查找空余考场
        $LastRoom = $this->SExamRoom->getLastRoom($plan,$plans,$subject,$plan[0]['roomCount']);
        if($LastRoom)
        {
            $Num = $LastRoom['examRoomNum'];
            $seatNum = $LastRoom['seatCount'];
        }else{
            $endRoom = $this->SExamRoom->getEnd($plan,$plans);
            if($endRoom)
            {
                if($endRoom['seatCount'] < $plan[0]['roomCount'])
                {
                    $Num = $endRoom['examRoomNum'];
                    $seatNum = $endRoom['seatCount'] + 1;
                }elseif ($endRoom['seatCount'] == $plan[0]['roomCount'])
                {
                    $Num = $endRoom['examRoomNum'] + 1;
                    $seatNum = 1;
                }
            }
        }
        foreach($enroll as $k=>$v)
        {
            //考一科查询是否有空座
            $lastSeat = '';
            if($type ==1)
            {
                $lastSeat = $this->SExamSeat->getLastSeat($v,$LiLun,$ZhuanYe,$ZongHe);

            }
            if($lastSeat)
            {
                $data = $this->seatData($plan,$plans,$v,$lastSeat['ExamRoomNum'],$lastSeat['ExamSeatNum'],$LiLun,$ZongHe,$ZhuanYe);
                $this->SExamSeat->where(['id'=>$lastSeat['id']])->update($data);
            }else{
                $data = $this->seatData($plan,$plans,$v,$Num,$seatNum,$LiLun,$ZongHe,$ZhuanYe);
                $data_room = $this->roomData($plan,$Num,$seatNum,$plans,$subject);
                //插入座位
                $insertSeat = $this->SExamSeat->insert($data);
            }

            //插入和更新考场
            if($insertSeat)
            {
                $seatNum ++;
                if($data['ExamSeatNum'] == $plan[0]['roomCount'])
                {
                    if(isset($LastRoom['examRoomNum']) && $LastRoom['examRoomNum'] == $Num)
                    {
                        $data_room['seatCount'] = $plan[0]['roomCount'];
                        $this->SExamRoom->where('id',$LastRoom['id'])->update($data_room);
                    }else{
                        $this->SExamRoom->insert($data_room);
                    }
                    $Num ++;
                    $seatNum = 1;
                }elseif($v['id'] == end($enroll)['id'])
                {
                    $data_room['seatCount'] = $data['ExamSeatNum'];
                    $this->SExamRoom->insert($data_room);
                }
            }
        }
    }
}