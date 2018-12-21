<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\ExamRoom;
use app\common\service\ExamSeat;
use app\common\service\ExamEnroll;
use app\common\service\Work;
use app\common\service\Subject;
use app\common\service\Userinfo;
use app\common\service\UserLogin;
use app\common\service\ExamCard;
use app\common\service\WorkLevelSubject;
use app\common\service\ExamDiscipline;
use app\common\service\ExamTime;

use think\Request;
use think\Db;

class MissingController extends AdminBase
{
    private $SExamRoom;
    private $SExamSeat;
    private $SExamEnroll;
    private $SWork;
    private $SSubject;
    private $SUserLogin;
    private $SUserInfo;
    private $SExamCard;
    private $SWorkLevelSubject;
    private $SExamDicsipline;
    private $SExamTime;
    private $level_name = array('1'=>'高级技师','2'=>'技师','3'=>'高级工','4'=>'中级','5'=>'初级','00'=>'考评员','01'=>'高级考评员');
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->SExamRoom = new ExamRoom();
        $this->SExamSeat = new ExamSeat();
        $this->SExamEnroll = new ExamEnroll();
        $this->SWork = new work();
        $this->SSubject = new Subject();
        $this->SUserInfo = new Userinfo();
        $this->SUserLogin = new UserLogin();
        $this->SExamCard = new ExamCard();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SExamDicsipline = new ExamDiscipline();
        $this->SExamTime = new ExamTime();
    }

    public function index(Request $request)
    {
        $message['type'] = '缺考违纪管理';
        $message['code'] = '3';
        $list = action('CardController/getPlanMessage');
        foreach ($list as $k=>$v)
        {
            $list[$k]['code'] = 3;
        }

        return $this->thisView('card/index',['list'=>$list,'message'=>$message,'request'=>$request]);
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 考场统计列表
     */
    public function showRoom(Request $request)
    {
        $data = $this->showRoomList($request);
        return $this->thisView('card/room_list',['list'=>$data['list'],'plan'=>$data['map']['exam_plan_id'],'organize'=>$data['map']['organize_id'],'url'=>
                '/admin/missing/getRoomInfo']);
    }

    /**
     * @param $request
     * @return mixed
     * 展示考场列表
     */
    public function showRoomList($request)
    {
        $where['exam_enroll.exam_plan_id'] = $request->param('plan');
        $where['exam_enroll.site_id'] = $request->param('organize');
        $map['organize_id'] = $request->param('organize');
        $map['exam_plan_id'] = $request->param('plan');
        $order = 'exam_enroll.work_id,exam_enroll.work_level_subject_level,work_level_subject.subject_id';
        $list = $this->SExamEnroll->seatSubjec($where,$order);
        //查询有资格排考场的人
        foreach($list as $k=>$v)
        {
            $sub_name = $this->SSubject->getSub($v);
            $list[$k]['sub_name'] = $sub_name['name'];
            $work = $this->SWork->showOne($v['work_id']);
            if($v['subject_id'] ==1)
            {
                $type = 'LiLun';
            }elseif ($v['subject_id'] ==2)
            {
                $type = 'ZhuanYe';
            }elseif ($v['subject_id'] ==3)
            {
                $type = 'ZongHe';
            }
            $count = $this->SExamSeat->getSeat($v,$map['organize_id'],$map['exam_plan_id'],$type);
            $list[$k]['total_seat'] = count($count);
            $list[$k]['room_count'] = count(array_unique($count));
            $list[$k]['work_name'] = $work['name'];
            $list[$k]['level_name'] = $v['level']!= null ? $this->level_name[$v['level']] : '';
        }
        $data['list'] = $list;
        $data['map'] = $map;
        return $data;
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 考场列表
     */
    public function getRoomInfo(Request $request)
    {
        $batch = $this->SExamSeat->getBatch(array('ExamSiteId'=>$request->param('organize'),'exam_plan_id'=>$request->param('plan')),array('batch'),'','batch');
        $type = '';
        if($request->param('subject') ==1)
        {
            $type = 'LiLun';
        }
        if($request->param('subject') ==2)
        {
            $type = 'ZhuanYe';
        }
        if($request->param('subject') ==3)
        {
            $type = 'ZongHe';
        }
        $map['work_id'] = $request->param('work_id');
        $map['level'] = (int)$request->param('level');
        $count = $this->SExamSeat->getSeat($map,$request->param('organize'),$request->param('plan'),$type);
        $room_count = array_unique($count);
        return $this->thisView('card/room_info',['list'=>$room_count,'request'=>$request,'url'=>'/admin/missing/seatList']);
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 座位列表
     */
    public function seatList(Request $request)
    {
        $map = '';
        $enroll_miss = 0;
        $enroll_dis = 0;
        $type = '';
        $where = array();
        if($request->param('subject') ==1)
        {
            $type = 'LiLun';
        }
        if($request->param('subject') ==2)
        {
            $type = 'ZhuanYe';
        }
        if($request->param('subject') ==3)
        {
            $type = 'ZongHe';
        }
        $map['work_id'] = $request->param('work_id');
        $map['level'] = (int)$request->param('level');
        $map['ExamRoomNum'] = $request->param('num');
        $count = $this->SExamSeat->getSeat($map,$request->param('organize'),$request->param('plan'),$type,$request->param('batch'));
        $time = $this->SExamTime->getBase(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize'),
            'batch'=>$request->param('num'),'subject_id'=>$request->param('subject')));
        if($time)
        {
            $start_time = $time['start_time'];
            $end_time = $time['end_time'];
        }else{
            $start_time = '';
            $end_time = '';
        }
        foreach($count as $k=>$v)
        {
            $where['user_login_id'] = $v['user_login_id'];
            $userName = $this->SUserInfo->findUserinfoData($where);
            $count[$k]['username'] = $userName['username'];
            $where_m['exam_enroll_id'] = $v['id'];
            $where_m['subject_id'] = $request->param('subject');
            $miss = $this->SExamDicsipline->getInfo($where_m);
            $count[$k]['type'] = 0;
            if($miss)
            {
                foreach ($miss as $ks=>$vs)
                {
                    if($vs['type'] ==1 )
                    {
                        $enroll_miss++;
                        $count[$k]['type'] = '1';
                    }
                    if($vs['type'] ==2 )
                    {
                        $enroll_dis++;
                        $count[$k]['type'] = '2';
                    }
                }
            }
        }
        return $this->thisView('seat_list',['list'=>$count,'request'=>$request,'enroll_miss'=>$enroll_miss,'enroll_dis'=>$enroll_dis,'start_time'=>$start_time,'end_time'=>$end_time,'m'=>1]);
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 获取用户信息
     */
    public function userInfo(Request $request)
    {
        $name = '';
        $map = '';
        $where = '';
        $map['id'] = $request->param('enroll_id');

        $enroll = $this->SExamEnroll->findExamEnroll($map);
        $where['user_login_id'] = $enroll['user_login_id'];
        $login = $this->SUserLogin->show(array('id'=>$enroll['user_login_id']));
        $user = $this->SUserInfo->findUserinfoData($where);
        $where_login['id'] = $enroll['user_login_id'];
        $enroll['user_name'] = $user['username'];
        $enroll['img'] = $user['avatar'];
        $enroll['user_card'] = $login['id_card'];
        $where_card['enroll_id'] = $request->param('enroll_id');
        $where_card['exam_plan_id'] = $request->param('plan');
        $card = $this->SExamCard->getCardInfo($where_card);
        $enroll['level'] = $enroll['work_level_subject_level'] != null ? $this->level_name[$enroll['work_level_subject_level']] : '';

        $where_s['work_level_subject.work_id'] = $request->param('work_id');
        $where_s['level'] = $request->param('level');
        $subjects = $this->SWorkLevelSubject->selWorktype($where_s);
        $enroll['subject'] = $request->param('subject');
        $where_d['exam_enroll_id'] = $request->param('enroll_id');
        $where_d['subject_id'] = $request->param('subject');
        $dicsipline = $this->SExamDicsipline->getInfo($where_d);
        $enroll['miss'] = 0;
        $enroll['Ill'] = 0;

        foreach($dicsipline as $k=>$v)
        {
            if($v['type'] ==1 )
            {
                $enroll['miss'] = 1;
            }
            if($v['type'] ==2 )
            {
                $enroll['Ill'] = 1;
            }
        }
        if($request->param('subject') ==1)
        {
            $name = '理论';
        }elseif($request->param('subject') ==2){
            $name = '实操';
        }elseif($request->param('subject') ==3){
            $name = '综合';
        }else{
            foreach($subjects as $k=>$v)
            {
                $name .= $v['name'].' ';
            }
        }
        $enroll['sub_name'] = $name;
        $enroll['card'] = $card['card'];

        return view('card/user_info',['list'=>$enroll]);
    }

    /**
     * @param Request $request
     * @return array
     * 添加缺考和违纪
     */
    public function updateInfoMiss(Request $request)
    {
        $data = array();
        $map['exam_enroll_id'] = $request->param('list.id');
        $map['subject_id'] = $request->param('list.subject');
        if($request->param('type') == 1)
        {
            $map['type'] = 1;
        }elseif ($request->param('type') == 3)
        {
            $map['type'] = 2;
        }
        if(isset($map['type']))
        {
            if($this->SExamDicsipline->examInfoUpdate($map))
            {
                $data['code'] = 200;
            }
            return $data;
        }else{
            return $this->updateInfoNotMiss($request);
        }
    }

    /**
     * @param Request $request
     * @return array
     * 取消缺考和违纪
     */
    public function updateInfoNotMiss(Request $request)
    {
        if($request->param('type') == 2)
        {
            $where['type'] = 1;
        }elseif ($request->param('type') == 4)
        {
            $where['type'] = 2;
        }
        $data = array();
        $where['exam_enroll_id'] = $request->param('list.id');
        $where['subject_id'] = $request->param('list.subject');
        $map['delete_time'] = '';
        if($this->SExamDicsipline->examInfoReturn($map,$where))
        {
            $data['code'] = 200;
        }
        return $data;
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 设置考试时间页面
     */
    public function setTime(Request $request)
    {
        $data = array();
        $batch = $this->SExamSeat->getBatch(array('ExamSiteId'=>$request->param('organize'),'exam_plan_id'=>$request->param('plan')),array('batch'),'','batch');
        $time = $this->SExamTime->getAll(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize'),'batch'=>$request->param('num')));
        foreach($time as $k=>$v)
        {
            $v['start_time'] = date('Y年m月d日 H:i:s',$v['start_time']);
            $v['end_time'] = date('Y年m月d日 H:i:s',$v['end_time']);
            $data[$v['subject_id']] = $v;
        }
        return view('setTime',['request'=>$request,'batch'=>$batch,'time'=>$data]);
    }

    public function setTimeInfo(Request $request)
    {
        $result_1 = '';
        $result_2 = '';
        $result_3 = '';
        if(!$request->param('lilun_start_date') && !$request->param('lilun_end_date') &&
            !$request->param('zonghe_start_date') && !$request->param('zonghe_end_date') &&
            !$request->param('shicao_start_date') && !$request->param('shicao_end_date'))
        {
            $data['code'] = 104;
            return $data;
        }
        if(($request->param('lilun_start_date') && !$request->param('lilun_end_date')) || (!$request->param('lilun_start_date') && $request->param('lilun_end_date'))){
            $data['code'] = 101;
            return $data;
        }
        if(($request->param('zonghe_start_date') && !$request->param('zonghe_end_date')) || (!$request->param('zonghe_start_date') && $request->param('zonghe_end_date'))){
            $data['code'] = 102;
            return $data;
        }
        if(($request->param('shicao_start_date') && !$request->param('shicao_end_date')) || (!$request->param('shicao_start_date') && $request->param('shicao_end_date')))
        {
            $data['code'] = 103;
            return $data;
        }
        if(($request->param('lilun_start_date') <= !$request->param('lilun_end_date')) && $request->param('lilun_end_date') >0){
            $data['code'] = 111;
            return $data;
        }
        if(($request->param('zonghe_start_date') <= !$request->param('zonghe_end_date')) && $request->param('zonghe_end_date') >0){
            $data['code'] = 112;
            return $data;
        }
        if(($request->param('shicao_start_date') <= !$request->param('shicao_end_date')) && $request->param('shicao_end_date') >0){
            $data['code'] = 113;
            return $data;
        }
        switch ($request){
            case ($request->param('lilun_start_date') && $request->param('lilun_end_date')):
                $result_1 = $this->addExamTime($request,1);
                break;
            case ($request->param('zonghe_start_date') && $request->param('zonghe_end_date')):
                $result_2 = $this->addExamTime($request,2);
                break;
            case ($request->param('shicao_start_date') && $request->param('shicao_end_date')):
                $result_3 = $this->addExamTime($request,3);
                break;
        }
        if($result_1 === false || $result_2 === false && $result_3 === false)
        {
            $data['code'] = 100;
        }else{
            $data['code'] = 200;
        }
        return $data;
    }

    public function addExamTime($request,$type=0)
    {
        $data['exam_plan_id'] = $request->param('plan');
        $data['exam_center'] = $request->param('center');
        if($request->param('batch')>0)
        {
            $data['batch'] = $request->param('batch');
        }
        $data['subject_id'] = $type;
        $result = $this->SExamTime->getBase($data);
        if($result)
        {
            $this->SExamTime->deleteInfo(array('id'=>$result['id']));
        }
        if($type ==1)
        {
            $data['start_time'] = strtotime($request->param('lilun_start_date'));
            $data['end_time'] = strtotime($request->param('lilun_end_date'));
            return $this->SExamTime->addData($data);
        }elseif($type ==2)
        {
            $data['start_time'] = strtotime($request->param('zonghe_start_date'));
            $data['end_time'] = strtotime($request->param('zonghe_end_date'));
            return $this->SExamTime->addData($data);
        }elseif($type ==3)
        {
            $data['start_time'] = strtotime($request->param('shicao_start_date'));
            $data['end_time'] = strtotime($request->param('shicao_end_date'));
            return $this->SExamTime->addData($data);
        }
    }
}