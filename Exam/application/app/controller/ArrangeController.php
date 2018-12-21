<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/12/5
 * Time: 9:52
 */

namespace app\app\controller;

use think\Controller;
use think\Request;
use app\common\service\Jury;
use app\common\service\JuryLogin;
use app\common\service\ExamStaffLog;
use app\common\service\ExamRoom;
use app\common\service\ExamSeat;
use app\common\service\UserLogin;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\ExamEnroll;
use app\common\service\Grade;
use think\response;
use think\db;
/**
 * Class AboutController
 * @package app\app\controller
 */
class ArrangeController extends Controller
{
    private $jury;
    private $juryLogin;
    private $staffLog;
    private $examRoom;
    private $examSeat;
    private $userLogin;
    private $work;
    private $direction;
    private $examEnroll;
    private $grade;
    private $level_name = array('1'=>'高级技师','2'=>'技师','3'=>'高级工','4'=>'中级','5'=>'初级','00'=>'考评员','01'=>'高级考评员');

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->jury = new Jury();
        $this->juryLogin = new JuryLogin();
        $this->staffLog = new ExamStaffLog();
        $this->examRoom = new ExamRoom();
        $this->examSeat = new ExamSeat();
        $this->userLogin = new UserLogin();
        $this->direction = new WorkDirection();
        $this->examEnroll = new ExamEnroll();
        $this->work = new Work();
        $this->grade = new Grade();
        //判断是不是post
        if(!$request->isPost())
        {
            result('40001');
        }
    }

    /**
     * @param Request $request
     * 发送短信验证码
     */
    public function sendmessage(Request $request)
    {
        $str = $request->param('phone');
        if(preg_match("/^1[34578]\d{9}$/", $str))
        {
            if($this->verifyUser($str))
            {
                $res = sendMsg($str, 0);
                if ($res['flag']) {
                    $arr['success'] = true;
                    $arr['code'] = '20004';
                    $arr['message'] = '发送验证码成功';
                } else {
                    $arr['success'] = false;
                    $arr['code'] = '40010';
                    $arr['message'] = '发送验证码失败';
                }
            }else{
                $arr['success'] = false;
                $arr['code'] = '40017';
                $arr['message'] = '未找到此用户';
            }
        }else{
            $arr['success'] = false;
            $arr['code'] = '40013';
            $arr['message'] = '手机号不符合规则';
        }
        return json($arr);
    }

    /**
     * @param $phone
     * @return array|false|\PDOStatement|string|\think\Model
     * 检查用户是否存在
     */
    public function verifyUser($phone)
    {
        $user = $this->jury->getOne(array('phone'=>$phone));
        return $user;
    }

    /**
     * @param Request $request
     * @return string
     * 用户登录
     */
    public function userlogin(Request $request)
    {
        if(!$this->checkmessage($request))
        {
            $token = create_token();
            $user = $this->verifyUser($request->param('phone'));
            if($user)
            {
                $this->juryLogin->updateToken(array('jury_id'=>$user['id']));
                if($this->juryLogin->addInfo($request->param('phone'),$token,$user['id']))
                {
                    $arr['success'] = true;
                    $arr['token'] = $token;
                    $arr['code'] = '20001';
                    $arr['message'] = '登录成功';
                }else{
                    $arr['success'] = false;
                    $arr['code'] = '40018';
                    $arr['message'] = '登录失败';
                }
                exit(json_encode($arr));
            }else{
                result('40017');
            }
        }
    }

    /**
     * @param $request
     * 验证验证码
     */
    public function checkmessage($request)
    {
        $code = $request->param('code');
        $phone = $request->param('phone');
        //如果短信验证码与Cache里的不一致返回验证码错误 , 如果验证码错误直接return错误代码
        $res = check_code($code, $phone);
        //如果验证码不通过
        if ($res==false) {
            $arr['success'] = false;
            $arr['code'] = '40014';
            $arr['message'] = '验证验证码失败';
            exit(json_encode($arr));
        }
    }

    /**
     * @param Request $request
     * 检查token
     */
    public function verifyToken($request)
    {
        $userInfo = $this->juryLogin->getOne(array('token'=>$request->header('token')));
        if($userInfo == false)
        {
            $arr['success'] = false;
            $arr['code'] = '40019';
            $arr['message'] = '未登录';
            header('isTokenValid: false');
            exit(json_encode($arr));
        }elseif($userInfo['status'] == 2){
            $arr['success'] = false;
            $arr['code'] = '40027';
            $arr['message'] = '请重新登录';
            header('isTokenValid: false');
            exit(json_encode($arr));
        }
        else{
            header('isTokenValid: true');
        }
    }

    /**
     * @param Request $request
     * 获取考场
     */
    public function obtainExamRoom(Request $request)
    {
        if(!$this->verifyToken($request))
        {
            $sites = array();
            $plans = array();
            $res = $this->juryLogin->getOne(array('token'=>$request->header('token')));
            $id = $res['jury_id'];
            $site = $this->staffLog->selExamPlan(array('exam_staff_log.be_assigned_id'=>$id,'exam_staff_log.type'=>3));
            foreach ($site as $k=>$v)
            {
                array_push($sites,$v['site_id']);
                array_push($plans,$v['exam_plan_id']);
            }
            $roomList = $this->examSeat->getPlanSite(array('exam_seat.exam_plan_id'=>array('in',$plans),'exam_seat.ZongHe'=>array('>',0),'exam_time.subject_id'=>3,'exam_seat.ExamSiteId'=>array('in',$sites)),'','','exam_seat.ExamRoomNum,exam_seat.ExamSiteId');
            foreach ($roomList as $k=>$v)
            {
                $roomList[$k]['start_time_date'] = date('Y-m-d',$v['start_time']);
                $roomList[$k]['end_time_date'] = date('Y-m-d',$v['end_time']);
                $roomList[$k]['start_time_time'] = date('H:i:s',$v['start_time']);
                $roomList[$k]['end_time_time'] = date('H:i:s',$v['end_time']);
            }
            if($roomList)
            {
                $arr['success'] = true;
                $arr['code'] = '20001';
                $arr['message'] = '获取考场成功';
                $arr['data'] = $roomList;
                exit(json_encode($arr));
            }else{
                $arr['success'] = false;
                $arr['code'] = '40023';
                $arr['message'] = '已加载全部数据';
                $arr['data'] = array();
                exit(json_encode($arr));
            }
        }
    }

    /**
     * @param Request $request
     * 获取考生列表
     */
    public function obtainExamEnroll(Request $request)
    {

        if(!$this->verifyToken($request))
        {
            if(!$request->param('ExamSiteId') || !$request->param('exam_plan_id') || !$request->param('ExamRoomNum'))
            {
                $arr['success'] = false;
                $arr['code'] = '40024';
                $arr['message'] = '缺少参数';
                $arr['data'] = array();
                exit(json_encode($arr));
            }
            $this->returnData(array('page'=>$request->param('page'),'ExamSiteId'=>$request->param('ExamSiteId'),'exam_plan_id'=>$request->param('exam_plan_id'),'ExamRoomNum'=>$request->param('ExamRoomNum'),'count'=>$request->param('count')),$request->domain());
        }
    }

    /**
     * @param $array
     * @param $url
     * 返回数据
     */
    public function returnData($array,$url)
    {
        //分页
        $page = isset($array['page']) ? $array['page'] : 1 ;
        $count = isset($array['count']) ? $array['count'] : 10;
        $field = array('exam_seat.ZongHe as id','exam_seat.exam_plan_id','exam_seat.ExamSiteId','exam_card.card','exam_enroll.user_login_id','exam_enroll.work_id','exam_enroll.work_direction_id','exam_enroll.work_level_subject_level','grade.watch_score','userinfo.username','user_login.id_card','userinfo.avatar');
        //查询条件
        $where['exam_seat.ExamSiteId'] = $array['ExamSiteId'];
        $where['exam_seat.exam_plan_id'] = $array['exam_plan_id'];
        $where['exam_seat.ExamRoomNum'] = $array['ExamRoomNum'];
        if(isset($array['name']))
        {
            if(is_numeric($array['name']))
            {
                $where['exam_card.card'] = array('like','%'.$array['name'].'%');
            }else{
                $where['userinfo.username'] = array('like','%'.$array['name'].'%');
            }
        }
        $where['exam_seat.ZongHe'] = array('>','0');
        //查询数据
        $seatList = $this->examSeat->getEnrollInfo($where,$field,$page,$count);
        $work_name = $this->formatWork();
        $direction = $this->formatDirection();
        foreach ($seatList as $k=>$v)
        {
            $seatList[$k]['level'] = $v['work_level_subject_level'] > 0 ? $this->level_name[$v['work_level_subject_level']] : '';
            $seatList[$k]['work_name'] = $v['work_id']>0 ? $work_name[$v['work_id']] : '';
            $seatList[$k]['direction_name'] = $v['work_direction_id'] >0 ? $direction[$v['work_direction_id']] : '';
            $seatList[$k]['avatar'] = $url.$v['avatar'];
            $seatList[$k]['watch_score'] = $v['watch_score'] == null ? 0 : $v['watch_score'];
            unset($seatList[$k]['work_level_subject_level']);
            unset($seatList[$k]['work_id']);
            unset($seatList[$k]['work_direction_id']);
        }
        if($seatList)
        {
            $arr['success'] = true;
            $arr['code'] = '20001';
            $arr['message'] = '查询成功';
            $arr['data'] = $seatList;
        }else{
            $arr['success'] = false;
            $arr['code'] = '40020';
            $arr['message'] = '已加载全部数据';
            $arr['data'] = array();
        }
        exit(json_encode($arr));
    }

    /**
     * @return array
     * 格式化work name
     */
    public function formatWork()
    {
        $works = $this->work->getAlls();
        $data = array();
        foreach($works as $k=>$v)
        {
            $data[$v['id']] = $v['name'];
        }
        return $data;
    }

    /**
     * @return array
     * 格式化方向
     */
    public function formatDirection()
    {
        $direction = $this->direction->getAllInfo();
        $data = array();
        foreach($direction as $k=>$v)
        {
            $data[$v['id']] = $v['name'];
        }
        return $data;
    }

    /**
     * @param Request $request
     * 查询考生
     */
    public function searchExamEnroll(Request $request)
    {
        if(!$this->verifyToken($request))
        {
            $data['ExamSiteId'] = $request->param('ExamSiteId');
            $data['exam_plan_id'] = $request->param('exam_plan_id');
            $data['ExamRoomNum'] = $request->param('ExamRoomNum');
            $data['page'] = $request->param('page');
            $data['count'] = $request->param('count');
            if(!$request->param('name'))
            {
                $arr['success'] = false;
                $arr['code'] = '40025';
                $arr['message'] = '请输入查询条件';
                $arr['data'] = array();
                exit(json_encode($arr));
            }
            if(!$request->param('ExamSiteId') || !$request->param('exam_plan_id') || !$request->param('ExamRoomNum'))
            {
                $arr['success'] = false;
                $arr['code'] = '40024';
                $arr['message'] = '缺少参数';
                $arr['data'] = array();
                exit(json_encode($arr));
            }
            if($request->param('name'))
            {
                $data['name'] = $request->param('name');
            }
            if(isset($data['name']))
            {
                $this->returnData($data,$request->domain());
            }else{
                $arr['success'] = false;
                $arr['code'] = '40021';
                $arr['message'] = '查询条件为空';
                exit(json_encode($arr));
            }
        }
    }

    /**
     * @param Request $request
     * 登分页面
     */
    public function showUser(Request $request)
    {
        if(!$this->verifyToken($request))
        {
            $exam_enroll = $this->examEnroll->getUserCard(array('exam_enroll.id'=>$request->param('id')));
            if($exam_enroll)
            {
                $work = $this->formatWork();
                $direction = $this->formatDirection();
                $exam_enroll['work_name'] = $exam_enroll['work_id'] != null ? $work[$exam_enroll['work_id']] : '';
                $exam_enroll['direction_name'] = $exam_enroll['work_direction_id'] != null ? $direction[$exam_enroll['work_direction_id']] : '';
                $exam_enroll['level'] = $exam_enroll['work_level_subject_level'] !=null ? $this->level_name[$exam_enroll['work_level_subject_level']] : '';
                $exam_enroll['avatar'] = $request->domain().$exam_enroll['avatar'];
                $exam_enroll['watch_score'] = $exam_enroll['watch_score'] == null ? 0 : $exam_enroll['watch_score'];
                unset($exam_enroll['work_id']);
                unset($exam_enroll['work_direction_id']);
                unset($exam_enroll['work_level_subject_level']);
                unset($exam_enroll['exam_plan_id']);
                unset($exam_enroll['type']);
                $arr['success'] = true;
                $arr['code'] = '20001';
                $arr['message'] = '查询成功';
                $arr['data'] = $exam_enroll;
            }else{
                $arr['success'] = false;
                $arr['code'] = '40020';
                $arr['message'] = '以加载全部';
                $arr['data'] = array();
            }
            exit(json_encode($arr));
        }
    }

    /**
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 登分接口
     */
    public function recordScore(Request $request)
    {
        if(!$this->verifyToken($request))
        {
            //查询是否存在考生和是否已经登过分
            $info = $this->examEnroll->getUserCard(array('exam_enroll.id'=>$request->param('id')));
            if(!$info)
            {
                $arr['success'] = false;
                $arr['code'] = '40020';
                $arr['message'] = '未找到考生';
            }
//elseif ($info['watch_score'] != null)
//            {
//                $arr['success'] = false;
//                $arr['code'] = '40021';
//                $arr['message'] = '已登分';
//            }
            elseif(!is_numeric($request->param('score'))){
                $arr['success'] = false;
                $arr['code'] = '40022';
                $arr['message'] = '请输入正确分数';
            }elseif($request->param('score') > '100'){
                $arr['success'] = false;
                $arr['code'] = '40026';
                $arr['message'] = '分数错误';
            }elseif(!$info['grade_id']){
                $work = $this->formatWork();
                $userLogin = $this->userLogin->show(array('id'=>$info['user_login_id']));
                $data['id_card'] = $userLogin['id_card'];
                $data['user_login_id'] = $userLogin['id'];
                $data['id_type'] = $userLogin['id_type'];
                $data['username'] = $userLogin['name'];
                $data['TicketNum'] = $info['card'];
                $data['work_id'] = $info['work_id'];
                $data['exam_plan_id'] = $info['exam_plan_id'];
                $data['work_direction_id'] = $info['work_direction_id'];
                $data['level'] = $info['work_level_subject_level'];
                $data['exam_type'] = $info['type'];
                $data['watch_score'] = $request->param('score') < 100 ? sprintf("%.1f", $request->param('score')) : 100;
                $data['work_name'] = $work[$info['work_id']];
                $data['create_time'] = time();
                $data['update_time'] = time();
                if($this->grade->saveGradeData($data))
                {
                    $arr['success'] = true;
                    $arr['code'] = '20001';
                    $arr['message'] = '登分成功';
                }else{
                    $arr['success'] = false;
                    $arr['code'] = '40022';
                    $arr['message'] = '登分失败';
                }
            }elseif ($info['grade_id'])
            {
                $data['watch_score'] = $request->param('score') < 100 ? sprintf("%.1f", $request->param('score')) : 100;
                if($this->grade->updateGradeData($data,array('id'=>$info['grade_id'])))
                {
                    $arr['success'] = true;
                    $arr['code'] = '20001';
                    $arr['message'] = '登分成功';
                }else{
                    $arr['success'] = false;
                    $arr['code'] = '40022';
                    $arr['message'] = '登分失败';
                }
            }
            exit(json_encode($arr));
        }
    }

    /**
     * @param Request $request
     * 退出登录
     */
    public function logout(Request $request)
    {
        if(!$this->verifyToken($request))
        {
            $this->juryLogin->delToken(array('token'=>$request->param('token')));
        }
    }
}