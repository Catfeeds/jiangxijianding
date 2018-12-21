<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/1
 * Time: 10:29
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\ExamPlan;
use app\common\service\Organize;
use app\common\service\ExamEnroll;
use app\common\service\WorkLevelSubject;
use app\common\service\ExamSeat;
use app\common\service\ExamCard;
use app\common\service\Subject;
use app\common\service\Userinfo;
use app\common\service\UserLogin;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\ExamSchedule;
use app\common\service\ExamTime;

use think\Exception;
use think\Request;
use think\Db;

class CardController extends AdminBase
{
    /**数据初始化
     * CardController constructor.
     */
    private $SExamPlan;
    private $SOrganize;
    private $SEnroll;
    private $SWrokLevelSubject;
    private $SExamSeat;
    private $SExamCard;
    private $SSubject;
    private $SUserInfo;
    private $SUserLogin;
    private $SWork;
    private $SWorkDirection;
    private $SExamSchedule;
    private $SExamTime;
    private $level_name = array('1'=>'高级技师','2'=>'技师','3'=>'高级工','4'=>'中级','5'=>'初级','00'=>'考评员','01'=>'高级考评员');

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->SExamPlan = new ExamPlan();
        $this->SOrganize = new Organize();
        $this->SEnroll = new ExamEnroll();
        $this->SWrokLevelSubject = new WorkLevelSubject();
        $this->SExamSeat = new ExamSeat();
        $this->SExamCard = new ExamCard();
        $this->SSubject = new Subject();
        $this->SUserInfo = new Userinfo();
        $this->SUserLogin = new UserLogin();
        $this->SWork = new Work();
        $this->SWorkDirection = new WorkDirection();
        $this->SExamSchedule = new ExamSchedule();
        $this->SExamTime = new ExamTime();
    }
    //展示基本信息
    public function index()
    {
        $message['type'] = '准考证管理';
        $message['code'] = '2';
        $message['down'] = '';
        $plans = $this->getPlanMessage();
        foreach ($plans as $k=>$v)
        {
            $plans[$k]['code'] = 2;
            $plans[$k]['message'] = $message;
        }
        return $this->thisView('',['list'=>$plans,'message'=>$message]);
    }

    /**
     * @return string
     * 获取鉴定计划
     */
    public function getPlanMessage()
    {
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        return $this->SExamPlan->BaseSelectPage($paginate,'','',"id desc");
    }

    /**
     * @param string $data
     * 展示考点
     */
    public function room(Request $request)
    {
        $data = $this->room_List($request);
        $Examplan = $this->SExamPlan->getExamPlanData(array('id'=>$request->param('plan')));
        return $this->thisView('room',['list'=>$data['list'],'message'=>$data['message'],'plan'=>$request->param('plan'),'request'=>$request,'ExamPlan'=>$Examplan]);
    }

    public function getMessage($where,$request)
    {
        $message = '';
        $result = $this->SExamSchedule->getBaseInf(array('exam_plan_id'=>$request->param('plan'),'exam_orangize'=>$where['id']),'','id desc');
        if($request->param('code') == 1)
        {
            $message['message'] = '考场编排';
            $message['url'] = '/admin/arrange/arrange';
            $message['down'] = 2;
            $message['type'] = '考场编排';
            if($result)
            {
                $message['message'] = '查看考场';
                $message['url'] = '/admin/missing/showRoom';
                $message['down'] = 3;
                $message['type'] = '准考证管理';
            }
        }elseif($request->param('code') == 2)
        {
            if(!$result)
            {
                $message['message'] = '考场编排';
                $message['url'] = '/admin/arrange/arrange';
                $message['down'] = 2;
                $message['type'] = '准考证管理';
            }else{
                if($result['type'] == 2)
                {
                    $message['type'] = '准考证管理';
                    $message['url'] = '/admin/card/getCard';
                    $message['down'] = 2;
                    $message['message'] = '批量生成准考证';
                }elseif($result['type'] == 1){
                    $message['type'] = '准考证管理';
                    $message['url'] = '/admin/card/generate';
                    $message['down'] = 2;
                    $message['message'] = '批量生成准考证号';
                }elseif($result['type']>2){
                    $message['type'] = '准考证管理';
                    $message['url'] = '/admin/card/downCard';
                    $message['down'] = 1;
                    $message['message'] = '批量下载准考证';
                }
            }
        }elseif($request->param('code') == 3)
        {
            if(!$result)
            {
                $message['type'] = '查看考场';
                $message['url'] = '/admin/arrange/arrange';
                $message['down'] = 2;
                $message['message'] = '考场编排';
            }else{
                if($result['type']<2)
                {
                    $message['type'] = '准考证管理';
                    $message['url'] = '/admin/card/generate';
                    $message['down'] = 2;
                    $message['message'] = '批量生成准考证号';
                }else{
                    $message['type'] = '准考证管理';
                    $message['url'] = '/admin/missing/showRoom';
                    $message['down'] = 2;
                    $message['message'] = '查看考场';
                }
            }
        }elseif($request->param('code') ==4)
        {
            $message['type'] = '组织册管理';
            $message['url'] = '/admin/arrange/generate';
            $message['down'] = 2;
            $message['message'] = '生成组织册';
        }elseif($request->param('code') ==5)
        {
            $message['down'] = 5;
            if(is_dir('download/clear/'.$request->param('plan').'/'.$where['id']))
            {
                $message['clear'] = 1;
            }else{
                $message['clear'] = 2;
            }
            if(is_dir('download/deskpaste/'.$request->param('plan').'/'.$where['id']))
            {
                $message['deskpaste'] = 1;
            }else{
                $message['deskpaste'] = 2;
            }
            if(is_dir('download/seat/'.$request->param('plan').'/'.$where['id']))
            {
                $message['seatproduct'] = 1;
            }else{
                $message['seatproduct'] = 2;
            }
        }
        return $message;
    }

    /**
     * @param $request
     * @return mixed
     * 考场列表
     */
    public function room_List($request)
    {
        $data = $request->param('plan');
        $list = $this->getOrganize($data);
        $message['type'] = '准考证管理';
        foreach($list as $k=>$v)
        {
            $list[$k]['message'] = $this->getMessage($v,$request);
        }
        $array['list'] = $list;
        $array['data'] = $data;
        $array['message'] = $message;
        return $array;
    }
    /**
     * 获取鉴定计划对应的考点
     */
    public function getOrganize($data)
    {
        return $this->SOrganize->getOrganize($data);
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 生成准考证号
     */
    public function generate(Request $request)
    {
        $exam_schedule = $this->SExamSchedule->getBaseInf(array('exam_plan_id'=>$request->param('plan'),'exam_orangize'=>$request->param('organize')));
        if(!$exam_schedule)
        {
            $data_res['code'] = 103;
            $data_res['message'] = '批量生成准考证号失败';
            return $data_res;
        }
        $info['exam_plan_id'] = $request->param('plan');
        $info['exam_orangize'] = $request->param('organize');
        $info['type'] = 2;
        if($this->SExamSchedule->getBaseInf($info))
        {
            $data_res['code'] = 101;
            $data_res['message'] = '批量生成准考证号失败';
            return $data_res;
        }
        $data = $request->param('plan');
        $list = $this->getOrganize($data);

        $map['exam_enroll.exam_plan_id'] = $request->param('plan');
        $map['exam_enroll.site_id'] = $request->param('organize');
        //查询有资格排考场的人
        $users = $this->SEnroll->getQualifications($map);
        $plan = $this->SExamPlan->getExamPlanData(array('id'=>$request->param('plan')));
        $organize = $this->SOrganize->getBaseFind(array('id'=>$request->param('organize')));
        foreach($users as $k=>$v)
        {
            $lilun = 0;
            $zonghe = 0;
            $shicao = 0;
            if($v['theory'])
            {
                $lilun = 1;
            }
            if($v['comprehen'])
            {
                $zonghe = 10;
            }
            if($v['operation'])
            {
                $shicao = 100;
            }
            $subject = $shicao+$zonghe+$lilun;
            $where['user_login_id'] = $v['id'];
            $where['exam_plan_id'] = $v['exam_plan_id'];
            $seatInfo = $this->SExamSeat->getSeatInfo($where);
            $card['card'] = substr($plan['year'],2,2).substr($organize['address_code'],2,2).$organize['code'].
                    sprintf('%03s',$plan['batch_num']).sprintf('%03s',$subject).sprintf('%03s',$seatInfo['ExamRoomNum']).
                    sprintf('%03s',$seatInfo['ExamSeatNum']);
            $card['enroll_id'] = $v['id'];
            $card['exam_plan_id'] = $v['exam_plan_id'];
            $result = $this->SExamCard->getCardInfo($card);
            if(!$result)
            {
                $this->SExamCard->insert($card);
            }
        }
        foreach($list as $k=>$v)
        {
            $list[$k]['message'] = $this->getMessage($v,$request);
        }
        $this->SExamSchedule->addInfo($info);
        $data_res['code'] = 200;
        $data_res['message'] = '批量生成准考证号成功';
        return $data_res;
    }

    /**
     * @param Request $request
     * @return mixed
     * 批量生成准考证
     */
    public function getCard(Request $request)
    {
        try{
            $seatLiLun = '';
            $seatZhuanYe = '';
            $seatZongHe = '';
            $liluntime = '';
            $zhuanyetime = '';
            $zonghetime = '';
            $direction_name = '';
            $clan = $this->SExamPlan->getExamPlanData(array('id'=>$request->param('plan')));
            $organize = $this->SOrganize->getBaseFind(array('id'=>$request->param('organize')));
            $list = $this->SEnroll->getQualifications(array('exam_plan_id'=>$request->param('plan'),'site_id'=>$request->param('organize')));
            $work = $this->SWork->getAlls();
            $direction = $this->SWorkDirection->getAll();
            foreach($list as $k=>$v)
            {
                if ($v['theory']) {
                    $seatLiLun = $this->SExamSeat->getBaseInfo(array('exam_plan_id' => $request->param('plan'), 'ExamSiteId' => $request->param('organize'), 'LiLun' => $v['id']));
                    $liluntime = $this->SExamTime->getBase(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize'),'batch'=>$seatLiLun['batch'],'subject_id'=>'1'));
                    $type[1] = 'LiLun';
                }
                if ($v['comprehen']) {
                    $seatZhuanYe = $this->SExamSeat->getBaseInfo(array('exam_plan_id' => $request->param('plan'), 'ExamSiteId' => $request->param('organize'), 'ZhuanYe' => $v['id']));
                    $zhuanyetime = $this->SExamTime->getBase(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize'),'batch'=>$seatZhuanYe['batch'],'subject_id'=>'2'));
                    $type[2] = 'ZhuanYe';
                }
                if ($v['operation']) {
                    $seatZongHe = $this->SExamSeat->getBaseInfo(array('exam_plan_id' => $request->param('plan'), 'ExamSiteId' => $request->param('organize'), 'ZongHe' => $v['id']));
                    $zonghetime = $this->SExamTime->getBase(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize'),'batch'=>$seatZongHe['batch'],'subject_id'=>'3'));
                    $type[3] = 'ZongHe';
                }
                foreach($work as $kwo=>$vwo)
                {
                    if($v['work_id'] == $vwo['id'])
                    {
                        $work_name = $vwo['name'];
                    }
                }
                foreach($direction as $kd=>$vd)
                {
                    if($v['work_direction_id'] == $vd['id'])
                    {
                        $direction_name = $vd['name'];
                    }
                }

                $user = $this->SUserInfo->findUserinfoData(array('user_login_id'=>$v['user_login_id']));
                $userInfo = $this->SUserLogin->show(array('id'=>$v['user_login_id']));
                $card = $this->SExamCard->getCardInfo(array('enroll_id'=>$v['id'],'exam_plan_id'=>$request->param('plan')));
                $this->cardDown($request,$clan,$organize,$user,$type,$work_name,$direction_name,$v,$userInfo,$card,$seatLiLun,$seatZhuanYe,$seatZongHe,$liluntime,$zhuanyetime,$zonghetime);
            }
            $data['code'] = 200;
            $data['message'] = '成功准考证成功';
            $this->SExamSchedule->addInfo(array('exam_plan_id'=>$clan['id'],'exam_orangize'=>$request->param('organize'),'type'=>3));
        }catch (Exception $e)
        {
            $data['code'] = 100;
            $data['message'] = '成功准考证失败';
        }
        return $data;
    }

    public function cardDown($request,$clan,$organize,$user,$type,$work,$direction,$list,$userInfo,$card,$seatLiLun='',$seatZhuanYe='',$seatZongHe='',$liluntime='',$zhuanyetime='',$zonghetime='')
    {
        try{
            $pdf = new \fpdf\chinese('L');
            $pdf->AddGBFont('simhei', 'Microsoft YaHei UI');
            $pdf->AddGBFont('heiti','黑体');
            $pdf->AddGBFont('songti','宋体');
            $pdf->AddPage('','A4');
            //输出表格
            $pdf->setxy(5,5);
            $pdf->SetFont('simhei', 'B', 12);
            $pdf->Cell(140,6,iconv("utf-8","gbk",$organize['name']),'LTR','','C');
            $pdf->Ln();
            $pdf->setxy(5,11);
            $pdf->Cell(140,6,iconv("utf-8","gbk","准考证"),'LBR','','C');
            $pdf->Ln();
            $pdf->setxy(5,17);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,17);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(10,6,iconv("utf-8","gbk","姓名："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(20,6,iconv("utf-8","gbk",$user['username']),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,23);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,23);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk","证件号码："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(32,6,iconv("utf-8","gbk",$userInfo['id_card']),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,29);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,29);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk","准考证号："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(32,6,iconv("utf-8","gbk",$card['card']),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,35);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,35);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(32,6,iconv("utf-8","gbk","报考（职业）工种："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(10,6,iconv("utf-8","gbk",$work),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,41);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,41);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk","报考方向："),'0','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(32,6,iconv("utf-8","gbk",$direction),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,47);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,47);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk","报考等级："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk",$list['work_level_subject_level'] != null ? $this->level_name[$list['work_level_subject_level']] : ''),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,53);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,53);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk","考点名称："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk",$organize['name']),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,59);
            $pdf->Cell(1,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->setxy(10,59);
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk","考试地点："),'','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(18,6,iconv("utf-8","gbk",$organize['address']),'','','L');
            $pdf->Ln();
            $pdf->setxy(5,65);
            $pdf->Cell(2,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(20,6,iconv("utf-8","gbk","考试科目"),'1','','C');
            $pdf->Cell(40,6,iconv("utf-8","gbk","考试时间"),'1','','C');
            $pdf->Cell(40,6,iconv("utf-8","gbk","考场"),'1','','C');
            $pdf->Cell(30,6,iconv("utf-8","gbk","座位号"),'1','','C');
            $pdf->Ln();
            $pdf->setxy(5,71);
            $pdf->Cell(2,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(20,6,iconv("utf-8","gbk","理论"),'1','','C');
            $pdf->SetFont('simhei', '', 8);
            $pdf->Cell(40,6,iconv("utf-8","gbk",$liluntime ? date('Y年m月d日H:i:s',$liluntime['start_time']).'-'.date('H:i:s',$liluntime['end_time']) : ''),'1','','C');
            $pdf->Cell(40,6,iconv("utf-8","gbk",$seatLiLun ? "第".$seatLiLun['ExamRoomNum']."考场" : ''),'1','','C');
            $pdf->Cell(30,6,iconv("utf-8","gbk",$seatLiLun ? $seatLiLun['ExamSeatNum']."号" : ''),'1','','C');
            $pdf->Ln();
            $pdf->setxy(5,77);
            $pdf->Cell(2,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(20,6,iconv("utf-8","gbk","实操"),'1','','C');
            $pdf->SetFont('simhei', '', 8);
            $pdf->Cell(40,6,iconv("utf-8","gbk",$zhuanyetime ? date('Y年m月d日H:i:s',$zhuanyetime['start_time']).'-'.date('H:i:s',$zhuanyetime['end_time']) : ''),'1','','C');
            $pdf->Cell(40,6,iconv("utf-8","gbk",$seatZhuanYe ? "第".$seatZhuanYe['ExamRoomNum']."考场" : ''),'1','','C');
            $pdf->Cell(30,6,iconv("utf-8","gbk",$seatZhuanYe ? $seatZhuanYe['ExamSeatNum']."号" : ''),'1','','C');
            $pdf->Ln();
            $pdf->setxy(5,83);
            $pdf->Cell(2,6,iconv("utf-8","gbk",""),'L','','L');
            $pdf->SetFont('simhei', '', 10);
            $pdf->Cell(20,6,iconv("utf-8","gbk","综合"),'1','','C');
            $pdf->SetFont('simhei', '', 8);
            $pdf->Cell(40,6,iconv("utf-8","gbk",$zonghetime ? date('Y年m月d日H:i:s',$zonghetime['start_time']).'-'.date('H:i:s',$zonghetime['end_time']) : ''),'1','','C');
            $pdf->Cell(40,6,iconv("utf-8","gbk",$seatZongHe ? "第".$seatZongHe['ExamRoomNum']."考场" : ''),'1','','C');
            $pdf->Cell(30,6,iconv("utf-8","gbk",$seatZongHe ? $seatZongHe['ExamSeatNum']."号" : ''),'1','','C');
            $pdf->Ln();
            $pdf->setxy(5,89);
            $pdf->SetFont('simhei', 'B', 8);
            $pdf->Cell(20,6,iconv("utf-8","gbk","成绩公布"),'L','','C');
            $pdf->SetFont('simhei', '', 8);
            $pdf->Cell(40,6,iconv("utf-8","gbk","\t请登录www.jxjd.gov查询成绩"),'','','C');
            $pdf->Ln();
            $pdf->setxy(5,95);
            $pdf->Cell(124,3,iconv("utf-8","gbk","- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -"),'L','','L');
            $pdf->SetFont('simhei', 'B', 18);
            $pdf->Ln();
            $pdf->setxy(5,98);
            $pdf->Cell(140,8,iconv("utf-8","gbk","考场规则（要点）"),'L','','C');
            $pdf->Ln();
            $pdf->setxy(5,106);
            $pdf->SetFont('songti', '', 12);
            $pdf->MultiCell(130,6,iconv("UTF-8","gbk","\t\t一、考生必须在开考前20分钟凭准考证和身份证进场，对号入座"),'L','L');
            $pdf->setxy(5,112);
            $pdf->MultiCell(130,6,iconv("UTF-8","gbk","\t\t二、考生迟到30分钟以上者不得入场，考试结束考试方可交卷（注：机考开考30分钟后可交卷离开考场）。"),'L','L');
            $pdf->setxy(5,124);
            $pdf->MultiCell(140,6,iconv("UTF-8","gbk","\t\t三、理论知识考试除带必要的文具（如钢笔、圆珠笔、2B铅笔、橡\r\n皮、墨水、三角板）外其它任何电子产品、通信设备、计算器、存储器、书籍、资料等，一律不准带入考场。"),'L','L');
            $pdf->setxy(5,142);
            $pdf->MultiCell(130,6,iconv("UTF-8","gbk","\t\t四、考生要在试卷规定的位置上准确的填写本人姓名和准考证号，\r\n采用答题卡填涂答案时，还需用2B铅笔填涂准开证号码、科目代码等信息。"),'L','L');
            $pdf->setxy(5,160);
            $pdf->MultiCell(130,6,iconv("UTF-8","gbk","\t\t五、考试鉴定结束铃响，考生应立即停止答卷，不准将试卷、考试\r\n题、答题卡、草稿纸带出考场。"),'L','L');
            $pdf->setxy(5,162);
            $pdf->Cell(130,7,iconv("utf-8","gbk",""),'L','','C');
            $pdf->Ln();
            $pdf->setxy(5,167);
            $pdf->Cell(1,10,iconv("utf-8","gbk",""),'L','','C');
            $pdf->setxy(5,177);
            $pdf->SetFont('songti', 'B', 10);
            $pdf->Cell(287,6,iconv("utf-8","gbk","温馨提示：1、考生打印准考证后，仔细核对报考信息，如有误请在考前报省中心修改，否则后果自负。2、请考生认真阅读《考试要点》和《考试人员违纪，违规处理方法》，带"),'LTR','','C');
            $pdf->Ln();
            $pdf->setxy(5,183);
            $pdf->Cell(142,6,iconv("utf-8","gbk","齐身份证等相关证件方可参加考试，3、考试自行带好水笔、2B铅笔、橡皮等考试工具。"),'LB','','C');
            $pdf->SetFont('simhei', 'B', 10);
            $pdf->Cell(145,6,iconv("utf-8","gbk","诚信报名，诚信考试。"),'BR','','L');
            $pdf->SetFont('simhei', 'B', 16);
            $pdf->setxy(145,5);
            $pdf->Cell(147,10,iconv("utf-8","gbk","违纪违规行为处理办法（要点）"),'LTR','','C');
            $pdf->Ln();
            $pdf->SetFont('songti', '', 12);
            $pdf->setxy(145,14);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t一、考生有下列行为之一的，给予该科目考试成绩无效处理："),'LR');
            $pdf->setxy(145,19);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（一）将规定以外的物品带入考场且未按要求放在指定位置的；"),'LR');
            $pdf->setxy(145,24);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（二）未在规定座位参加考试，或未经工作人员允许擅自离开座位或者考\r\n场的；"),'LR');
            $pdf->setxy(145,29);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（三）经提醒仍不按照规定填写（填涂）本人信息的；"),'LR');
            $pdf->setxy(145,34);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（四）未用规定的纸笔作答的；"),'LR');
            $pdf->setxy(145,39);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（五）故意损毁试卷、答题纸、答题卡、或者将试卷、答题纸、答题卡、\r\n带出考场的；"),'LR');
            $pdf->setxy(145,49);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（六）在考卷（答题卡）上做特殊标记的；"),'LR');
            $pdf->setxy(145,54);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（七）未在答题卡（纸）规定位置上答题的，或者未用现代汉语作答的（\r\n试卷中特别指明的除外）；"),'LR');
            $pdf->setxy(145,64);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（八）以旁窥、交头接耳、打手势等方式传递信息的；"),'LR');
            $pdf->setxy(145,69);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（九）其他一般违纪违规行为。"),'LR');
            $pdf->setxy(145,74);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t二、考生有下列行为之一的，给予取消考试资格，且当次全部科目成绩无效\r\n的处理，并将违纪违规情况予以通报，触犯国家法律的应移交司法、公安部门处理："),'LR');
            $pdf->setxy(145,88);
            $pdf->MultiCell(147,4,iconv("utf-8","gbk","\t\t（一）伪造、涂改证件、证明或以不正当手段获取考试资格的；"),'LR');
            $pdf->setxy(145,92);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（二）违反规定翻阅资料，或使用手机等规定以外工具的；"),'LR');
            $pdf->setxy(145,97);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（三）互相交换试卷、答题纸、答题卡、草稿纸等；"),'LR');
            $pdf->setxy(145,102);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（四）抄袭、协助他人抄袭试题答案或与考试内容相关资料的；"),'LR');
            $pdf->setxy(145,107);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（五）让他人冒名顶替参加考试的；"),'LR');
            $pdf->setxy(145,112);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（六）在考试未结束前，出卖试卷答案的；"),'LR');
            $pdf->setxy(145,117);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（七）与考试工作人员串通作弊或参与有组织作弊的；"),'LR');
            $pdf->setxy(145,122);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（八）将无线耳机、无线接收器等高科技作弊设备带入座位并使用的；"),'LR');
            $pdf->setxy(145,127);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（九）不服从监考人员管理，无理取闹；扰乱考场秩序，辱骂监考人\r\n员，威胁他人安全的；"),'LR');
            $pdf->setxy(145,137);
            $pdf->MultiCell(147,5,iconv("utf-8","gbk","\t\t（十）其他严重违纪违规行为的。"),'LR');
            $pdf->setxy(145,121);
            $pdf->Cell(2,56,iconv("utf-8","gbk",""),'L','','C');
            $pdf->setxy(292,121);
            $pdf->Cell(2,56,iconv("utf-8","gbk",""),'L','','C');
            $pdf->Image('uploads/avatar/avatar/20181212/f597c71cf7916ec30cd74fb5de8c2ea7.jpg',114,20,24,32,'','');
            $pdf->Image("code.jpg",162,145,18,18,'','');
            $pdf->Image("code.jpg",210,145,18,18,'','');
            $pdf->Image("code.jpg",258,145,18,18,'','');
            $pdf->setxy(160,164);
            $pdf->SetFont('songti', '', 8);
            $pdf->Cell(24,3,iconv("utf-8","gbk","鉴定在线学习APP"),'','','L');
            $pdf->setxy(152,168);
            $pdf->MultiCell(42,3,iconv("utf-8","gbk","扫一扫下载“在线学习”公众号获取免费模式书籍"),'','C');
            $pdf->setxy(208,164);
            $pdf->Cell(24,3,iconv("utf-8","gbk","江西鉴定公众号"),'','');
            $pdf->Cell(24,3,iconv("utf-8","gbk","江西鉴定公众号"),'','');
            $pdf->setxy(200,168);
            $pdf->MultiCell(42,3,iconv("utf-8","gbk","扫一扫关注“江西鉴定”公众号了解最新考试通知"),'','C');
            $pdf->setxy(259,164);
            $pdf->Cell(24,3,iconv("utf-8","gbk","人社公众号"),'','');
            $pdf->setxy(248,168);
            $pdf->MultiCell(38,3,iconv("utf-8","gbk","扫一扫关注“人社”公众号了解最新考试通知"),'','C');

            //直接输出，即在浏览器显示
            $dir = 'download/card/'.$request->param('plan').'/'.$request->param('organize').'/';
            if (!file_exists($dir)){
                mkdir ($dir,0777,true);
            }
            $pdf->Output($dir."/".$request->param('plan')."_".$request->param('organize')."_".$list['id']."_card.pdf", 'F');
            $data['code'] = 200;
            $data['message'] = '生成准考证成功';
        }catch (Exception $e)
        {
            $data['code'] = 100;
            $data['message'] = '成功准考证失败';
        }
        return $data;
    }

    public function create_zip($path,$zip)
    {
        $zipObj = new \ZipArchive();
        $zip_path = './public/test.zip';

        if (file_exists($zip_path)) {
            // 如果压缩文件已经存在，就覆盖
            $res = $zipObj->open($zip_path, \ZipArchive::OVERWRITE);
        } else {
            // 如果不存在，就创建
            $res = $zipObj->open($zip_path, \ZipArchive::CREATE);
        }

        if($res===true) {
            // 添加一个文件到压缩文件，第二个参数可对该文件重命名（可省略）
            $zipObj->addFile('./a.txt', 'newName.txt');
            $zipObj->addFile('./b.txt');

            // 添加一个文件到压缩文件，第二个参数为该文件的内容
            $zipObj->addFromString('README.txt', "this is a instruction file");

            $zipObj->close();
            echo 'ok';
        } else {
            echo 'failed';
        }
    }

    public function getclear(Request $request)
    {
        $str = '';
//        try{
            $count = 0;
            $pdf = new \fpdf\chinese();
            $pdf->AddGBFont('simhei', '楷体');
            $pdf->AddPage();
            $pdf->SetFont('simhei', '', 9);
            $subject = $this->SSubject->getAll();
            $where['ExamSiteId'] = $request->param('organize');
            $where['exam_plan_id'] = $request->param('plan');
            $organize = $this->SOrganize->getBaseFind(array('id'=>$where['ExamSiteId']));
            foreach($subject as $ks=>$vs)
            {
                $work = 0;
                $level = 0;
                $room = 0;
                $i = 0;
                unset($where[$str]);
                $str = $vs['id'] == 1 ? 'LiLun' : ($vs['id'] == 2 ? 'ZhuanYe' : 'ZongHe');
                $str_name = $vs['id'] == 1 ? '理论' : ($vs['id'] == 2 ? '综合' : '实操');
                $field = array("$str,ExamRoomNum,ExamSeatNum,exam_plan_id");
                $where[$str] = array('>','0');
                $order = array('ExamRoomNum asc');
                $enroll = $this->SExamSeat->getAllInfo($where,$field,$order);
                if($enroll)
                {
                    if($count>0)
                    {
                        $pdf->AddPage();
                    }
                    $count ++;
                    $m = 0;
                    foreach ($enroll as $k => $v) {
                        $y = 16;
                        $x = 10;
                        if($i>=5)
                        {
                            $pdf->AddPage();
                            $x = 10;
                            $y = 16;
                            $i=0;
                        }
                        $enrollInfo = $this->getEnrollInfo($v);
                        if($enrollInfo)
                        {
                            if($work != $enrollInfo['work_id'] || $level != $enrollInfo['work_level_subject_level'] || $room !=$v['ExamRoomNum'])
                            {
                                $work = $enrollInfo['work_id'];
                                $level = $enrollInfo['work_level_subject_level'];
                                $room =$v['ExamRoomNum'];
                                $pdf->setxy($x,($y + $i*48 +($m>0 ? 6 : 0)));
                                $m ++;
                                $pdf->Cell('',6,iconv("utf-8","gbk",$organize['name']."考点\t\t\t\t\t\t工种：".$enrollInfo['workname']."\t\t\t级别：".$level != 0 ? $this->level_name[$level] : ''."\t\t科目：".$str_name."\t\t考场：".sprintf('%02d',$room)),1,0,'L');
                                $pdf->Ln();
                            }
                            $y = $y + $i*48 +$m*6;
                            $i++;
                            $user = $this->SUserInfo->findUserinfoData(array('user_login_id'=>$enrollInfo['user_login_id']));
                            $pdf->Image('uploads/avatar/avatar/20181212/f597c71cf7916ec30cd74fb5de8c2ea7.jpg',$x,$y,36,44,'','');
                            $pdf->Ln();
                            $pdf->SetFont('simhei', '', 9);
                            $pdf->setxy($x+36,$y);
                            $pdf->Cell('36',8,iconv("utf-8","gbk","姓名"),'1','','C');
                            $pdf->Cell('36',8,iconv("utf-8","gbk",$enrollInfo['username']),'1','','C');
                            $pdf->Cell('20',8,iconv("utf-8","gbk","性别"),'1','','C');
                            $pdf->Cell('10',8,iconv("utf-8","gbk","男"),'1','','C');
                            $pdf->Cell('20',8,iconv("utf-8","gbk","报名号"),'1','','C');
                            $pdf->Cell('32',8,iconv("utf-8","gbk","9527"),'1','','C');
                            $pdf->Ln();
                            $pdf->setxy($x+36,$y+8);
                            $pdf->Cell('36',8,iconv("utf-8","gbk","身份证号"),'1','','C');
                            $pdf->Cell('118',8,iconv("utf-8","gbk",$enrollInfo['id_card']),'1','','L');
                            $pdf->Ln();
                            $pdf->setxy($x+36,$y+8+8);
                            $pdf->Cell('36',8,iconv("utf-8","gbk","准考证号"),'1','','C');
                            $pdf->Cell('118',8,iconv("utf-8","gbk",$enrollInfo['card']),'1','','L');
                            $pdf->Ln();
                            $pdf->setxy($x+36,$y+8+8+8);
                            $pdf->MultiCell('154',8,iconv("utf-8","gbk","我已阅读《鉴定注意事项》、《考场规则》，承诺是本人参加鉴定，保证遵守考场规则，如有违纪，自愿接受处罚。"),'TR','L');
                            $pdf->setxy($x+36,$y+8+8+8+10);
                            $pdf->SetFont('simhei', 'B', 10);
                            $pdf->Cell('',10,iconv("utf-8","gbk","考生签字："),'RB','','C');
                            $pdf->Ln(1);
                            $pdf->SetFont('simhei', '', 9);
                        }
                    }
                }
            }
            $dir = 'download/clear/'.$request->param('plan').'/'.$request->param('organize').'/';
            if (!file_exists($dir)){
                mkdir ($dir,0777,true);
            }
            $pdf->Output($dir."/".$request->param('plan')."_".$request->param('organize')."_seat.pdf", 'F');
            $data['code'] = 200;
            return $data;
//        }catch(Exception $e)
//        {
//            $data['code'] = 101;
//            return $data;
//        }
    }


    public function getEnrollInfo($where)
    {
        $param['ee.id'] = isset($where['LiLun']) ? $where['LiLun'] : (isset($where['ZhuanYe']) ? $where['ZhuanYe'] : $where['ZongHe']);
        $enroll = $this->SEnroll->ExamEnroll($param)[0];
        $map['user_login_id'] = $enroll['user_login_id'];
        $user = $this->SUserInfo->findUserinfoData($map);
        $enroll['username'] = $user['username'];
        $enroll['avatar'] = $user['avatar'];
        $card = $this->SExamCard->getCardInfo(array('enroll_id'=>$enroll['id'],'exam_plan_id'=>$enroll['exam_plan_id']));
        $enroll['card'] = $card['card'];
        $login = $this->SUserLogin->show(array('id'=>$enroll['user_login_id']));
        $enroll['id_card'] = $login['id_card'];
        return $enroll;
    }

    public function downCard(Request $request)
    {
        $file = getFileName($request->param('plan'),$request->param('organize'),'',$request->param('type'));
        downFile($file);
    }
}