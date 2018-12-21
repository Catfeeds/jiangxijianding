<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 15:29
 */ 
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Work;
use app\common\service\ExamCard;
use app\common\service\ExamEnroll;
use app\common\service\ExamPlan;
use app\common\service\ExamRoom;
use app\common\service\ExamSchedule;
use app\common\service\ExamSeat;
use app\common\service\ExamSeatLilun;
use app\common\service\ExamSeatShicao;
use app\common\service\ExamSeatZonghe;
use app\common\service\ExamTime;
use app\common\service\Grade;
use app\common\service\Organize;
use app\common\service\Subject;
use app\common\service\Userinfo;
use app\common\service\WorkLevelSubject;
use think\Db;
use think\Exception;
use think\Request;

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
    private $SExamSeatLilun;
    private $SExamSeatZonghe;
    private $SExamSeatShicao;
    private $SUserinfo;
    private $SExamCard;
    private $SSubject;
    private $SExamTime;
    private $SGrade;
    private $SExamSchedule;
    private $SWork;
    private $level_name = array('1'=>'高级技师','2'=>'技师','3'=>'高级工','4'=>'中级','5'=>'初级','00'=>'考评员','01'=>'高级考评员');

    public function __construct()
    {
        parent::__construct();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SExamPlan = new ExamPlan();
        $this->SExamEnroll = new ExamEnroll();
        $this->SExamRoom = new ExamRoom();
        $this->SExamSeat = new ExamSeat();
        $this->SOrganize = new Organize();
        $this->SExamSeatLilun = new ExamSeatLilun();
        $this->SExamSeatZonghe = new ExamSeatZonghe();
        $this->SExamSeatShicao = new ExamSeatShicao();
        $this->SUserinfo = new Userinfo();
        $this->SExamCard = new ExamCard();
        $this->SSubject = new Subject();
        $this->SExamTime = new ExamTime();
        $this->SGrade = new Grade();
        $this->SExamSchedule = new ExamSchedule();
        $this->SWork = new Work();
    }

    /**
     * 考场编排
     * @return \think\response\View
     */

    public function index(Request $request)
    {
        $message['type'] = '考场编排';
        $message['code'] = '1';
        $list = action('CardController/getPlanMessage');
        foreach ($list as $k=>$v)
        {
            $list[$k]['code'] = 1;
        }
        return $this->thisView('card/index',['list'=>$list,'message'=>$message,'request'=>$request]);
    }

    /**
     * @param Request $request
     * @return bool|int
     * 考场排座位
     */
    public function arrange(Request $request)
    {
        $map['exam_enroll.exam_plan_id'] = $request->param('plan');
        $map['exam_enroll.site_id'] = $request->param('organize');
        //查询有资格排考场的人
        $users = $this->SExamEnroll->getQualifications($map);
        if(!$users)
        {
            $data_res['code'] = 102;
            $data_res['message'] = "考场编排失败；未找到符合要求报名";
            return $data_res;
        }

        //查出考点下的全部考生
        //删除当前已排的信息
        $this->SExamSchedule->deInfo(array('exam_plan_id'=>$request->param('plan'),'exam_orangize'=>$request->param('organize')));
        $this->SExamRoom->deInfo(array('exam_plan_id'=>$request->param('plan'),'ExamSiteId'=>$request->param('organize')));
        $this->SExamSeat->deInfo(array('exam_plan_id'=>$request->param('plan'),'ExamSiteId'=>$request->param('organize')));
        $recode = array();
        foreach($users as $k=>$v)
        {
            $data['work_id'] = $v['work_id'];
            $data['level_id'] = $v['work_level_subject_level'];
            $data['user_id'] = $v['id'];
            $data['direction_id'] = $v['work_direction_id'];
            $data['exam_plan_id'] = $v['exam_plan_id'];
            $data['organize_id'] = $v['site_id'];
            if($v['theory'])
            {
                $this->SExamSeatLilun->addInfo($data);
                $recode[$v['work_id']][$v['work_level_subject_level']]['lilun'] = 1;
            }
            if($v['comprehen'])
            {
                $this->SExamSeatZonghe->addInfo($data);
                $recode[$v['work_id']][$v['work_level_subject_level']]['zonghe'] = 1;
            }
            if ($v['operation'])
            {
                $this->SExamSeatShicao->addInfo($data);
                $recode[$v['work_id']][$v['work_level_subject_level']]['shicao'] = 1;
            }
        }
        $i = 0;
        $work_level_t = array();
        $work_level_s = array();
        $work_level_f = array();
        foreach ($recode as $k=>$v)
        {
            foreach($v as $kk=>$vv)
            {
                $m = $i++;
                if(count($vv)==3)
                {
                    $work_level_t[$m]['work_id'] = $k;
                    $work_level_t[$m]['level'] = $kk;
                    $work_level_t[$m]['cut'] = count($vv);
                }elseif(count($vv)==2)
                {
                    $work_level_s[$m]['work_id'] = $k;
                    $work_level_s[$m]['level'] = $kk;
                    $work_level_s[$m]['cut'] = count($vv);
                }elseif(count($vv)==1)
                {
                    $work_level_f[$m]['work_id'] = $k;
                    $work_level_f[$m]['level'] = $kk;
                    $work_level_f[$m]['cut'] = count($vv);
                }
            }
        }
        $recodes = array_merge($work_level_t,$work_level_s,$work_level_f);
        $this->SExamTime->deleteInfo(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize')));
        $this->seatDown($map,$recodes);
        $this->roomDown($map);
        $dataS['exam_plan_id'] = $request->param('plan');
        $dataS['exam_orangize'] = $request->param('organize');
        $dataS['type'] = 1;
        $this->SExamSchedule->addInfo($dataS);
        $data_res['code'] = 200;
        $data_res['message'] = "考场编排成功";
        return $data_res;
    }

    public function seatDown($map,$recode)
    {
        //循环考三科的工种级别方向然后在查相同工种级别方向考两科和一科的，再查相同工种考三科，两科，一科的。
        foreach($recode as $k=>$v)
        {
            $result_three_work_level = $this->threeSubjectAll('',$v['work_id'],$v['level']);
            if($result_three_work_level)
            {
                $this->addSeat($result_three_work_level);
            }
            $result_two_work_level = $this->twoSubject($v['work_id'],$v['level']);
            $result_one_work_level = $this->Subject($v['work_id'],$v['level']);
            $result_three_work = $this->threeSubjectAll('',$v['work_id']);

            if($result_three_work)
            {
                $this->addSeat($result_three_work_level);
            }
            $result_two_work = $this->twoSubject($v['work_id']);
            $result_one_work = $this->Subject($v['work_id']);
        }
    }

    public function threeSubject($where='')
    {
        $where['exam_seat_zonghe.work_id'] = array('>',0);
        $where['exam_seat_shicao.work_id'] = array('>',0);
        return Db::table('exam_seat_lilun')->field('exam_seat_lilun.id,exam_seat_lilun.user_id,exam_seat_lilun.work_id,exam_seat_lilun.level_id,exam_seat_lilun.direction_id,exam_seat_lilun.organize_id,exam_seat_lilun.exam_plan_id')->join('exam_seat_zonghe','exam_seat_lilun.work_id=exam_seat_zonghe.work_id and exam_seat_lilun.level_id=exam_seat_zonghe.level_id and exam_seat_lilun.direction_id=exam_seat_zonghe.direction_id and exam_seat_lilun.user_id=exam_seat_zonghe.user_id','left')->join('exam_seat_shicao','exam_seat_lilun.work_id=exam_seat_shicao.work_id and exam_seat_lilun.level_id=exam_seat_shicao.level_id and exam_seat_lilun.direction_id=exam_seat_shicao.direction_id and exam_seat_lilun.user_id=exam_seat_shicao.user_id','left')->where($where)->group('exam_seat_lilun.work_id,exam_seat_lilun.level_id')->order('exam_seat_lilun.direction_id,exam_seat_lilun.user_id')->select();
    }

    /**use
     * @param string $where
     * @param string $work
     * @param string $level
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取考三科的考生
     */
    public function threeSubjectAll($where='',$work='',$level='')
    {
        $where['exam_seat_zonghe.work_id'] = array('>',0);
        $where['exam_seat_shicao.work_id'] = array('>',0);
        $work != '' ? $where['exam_seat_lilun.work_id'] = $work :'';
        $level != '' ? $where['exam_seat_lilun.level_id'] = $level : '';
        return $this->SExamSeatLilun->getAllEnroll($where);

    }


    public function addSeat($result)
    {
        foreach($result as $k=>$v)
        {
            $this->insertSeat($v,1,1,1);
        }
    }

    public function insertSeat($map,$lilun='',$zonghe='',$shicao='')
    {
        //插入seat表，删除lilun、zonghe、shicao表
        $data['ExamSiteId'] = $map['organize_id'];
        $data['Level'] = $map['level_id'];
        $data['LiLun'] = $lilun ? $map['user_id'] : '';
        $data['ZhuanYe'] = $zonghe ? $map['user_id'] : '';
        $data['ZongHe'] = $shicao ? $map['user_id'] : '';
        $data['work_id'] = $map['work_id'];
        $data['exam_plan_id'] = $map['exam_plan_id'];
        $data['ExamRoomNum'] = 0;
        $data['work_direction_id'] = $map['direction_id'];
        $this->SExamSeat->insertInfo($data);
        $where['work_id'] = $map['work_id'];
        $where['level_id'] = $map['level_id'];
        $where['direction_id'] = $map['direction_id'];
        $where['user_id'] = $map['user_id'];
        //删除中间表
        $lilun ? $this->SExamSeatLilun->deleteEnroll($where) : '';
        $zonghe ? $this->SExamSeatZonghe->deleteEnroll($where) : '';
        $shicao ? $this->SExamSeatShicao->deleteEnroll($where) : '';
    }

    public function twoSubject($work='',$level='')
    {
        $where['exam_seat_lilun.work_id'] = $work;
        $level != '' ? $where['exam_seat_lilun.level_id'] = $level : '';
        $result_one = $this->getTwoSubject($where,'exam_seat_lilun','exam_seat_zonghe');
        if($result_one)
        {
            foreach($result_one as $k=>$v)
            {
                $this->insertSeat($v,1,1);
            }
        }
        $result_two = $this->getTwoSubject($where,'exam_seat_lilun','exam_seat_shicao');
        if($result_two)
        {
            foreach($result_two as $k=>$v)
            {
                $this->insertSeat($v,1,'',1);
            }
        }
        unset($where);
        $where['exam_seat_zonghe.work_id'] = $work;
        $level != '' ? $where['exam_seat_zonghe.level_id'] = $level : '';
        $result_three = $this->getTwoSubject($where,'exam_seat_zonghe','exam_seat_shicao');
        if($result_three)
        {
            foreach($result_three as $k=>$v)
            {
                $this->insertSeat($v, '', 1, 1);
            }
        }
    }

    public function Subject($work='',$level='')
    {
        $where['exam_seat_lilun.work_id'] = $work;
        $level != '' ? $where['exam_seat_lilun.level_id'] = $level : '';
        $lilun = $this->getOnesubject('exam_seat_lilun',$where);
        foreach($lilun as $k=>$v)
        {
            $this->selectUpdate($v,1);
        }
        unset($where);
        $where['exam_seat_zonghe.work_id'] = $work;
        $level != '' ? $where['exam_seat_zonghe.level_id'] = $level : '';
        $zonghe = $this->getOnesubject('exam_seat_zonghe',$where);

        foreach($zonghe as $k=>$v)
        {
            $this->selectUpdate($v,'',1);
        }
        unset($where);
        $where['exam_seat_shicao.work_id'] = $work;
        $level != '' ? $where['exam_seat_shicao.level_id'] = $level : '';
        $shicao = $this->getOnesubject('exam_seat_shicao',$where);
        foreach($shicao as $k=>$v)
        {
            $this->selectUpdate($v,'','',1);
        }
    }

    /**
     * @param string $where
     * @param $table_1
     * @param $table_2
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 任意量表join
     */
    public function getTwoSubject($where='',$table_1,$table_2)
    {
        return Db::table($table_1)->field("$table_1.id,$table_1.user_id,$table_1.work_id,$table_1.level_id,$table_1.direction_id,$table_1.organize_id,$table_1.exam_plan_id")->join($table_2,"$table_1.work_id=$table_2.work_id and $table_1.level_id=$table_2.level_id and $table_1.direction_id=$table_2.direction_id and $table_1.user_id=$table_2.user_id",'left')->where($where)->where("$table_2.work_id>0")->order("$table_1.direction_id,$table_1.user_id,level_id")->select();
    }

    /**
     * @param $table
     * @param string $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 查询任意考一科
     */
    public function getOnesubject($table,$where='')
    {
        return Db::table($table)->where($where)->order('level_id')->select();
    }

    /**
     * @param $map
     * @param string $lilun
     * @param string $zonghe
     * @param string $shicao
     * 更新或者插入seat表
     */
    public function selectUpdate($map,$lilun='',$zonghe='',$shicao='')
    {
        if($lilun) $where['LiLun'] = 0; $data['LiLun'] = $map['id'];
        if($zonghe) $where['ZhuanYe'] = 0; $data['ZhuanYe'] = $map['id'];
        if($shicao) $where['ZongHe'] = 0; $data['ZongHe'] = $map['id'];
        $where['work_id'] = $map['work_id'];
        $where['Level'] = $map['level_id'];
        $where['work_direction_id'] = $map['direction_id'];
        //查询工种级别方向是否有相同，没有减少条件，依次，如果有更新
        $result = $this->SExamSeat->getBaseInfo($where);
        if(!$result)
        {
            unset($where['work_direction_id']);
            $result = $this->SExamSeat->getBaseInfo($where);
            if($result)
            {
                $update_where['id'] = $result['id'];
                $this->SExamSeat->updateInfo($data,$update_where);
            }else{
                unset($where['Level']);
                $result = $this->SExamSeat->getBaseInfo($where);
                if($result)
                {
                    $update_where['id'] = $result['id'];
                    $this->SExamSeat->updateInfo($data,$update_where);
                }else{
                    unset($where['work_id']);
                    $result = $this->SExamSeat->getBaseInfo($where);
                    if($result)
                    {
                        $update_where['id'] = $result['id'];
                        $this->SExamSeat->updateInfo($data,$update_where);
                    }else{
                        $this->insertSeat($map, $lilun, $zonghe, $shicao);
                    }
                }
            }
        }else{
            $update_where['id'] = $result['id'];
            $this->SExamSeat->updateInfo($data,$update_where);
        }
    }

    public function roomDown($map,$batch=1,$roomNum=1)
    {
        $batch_next = '';
        $count = 1;
        $where['exam_plan_id'] = $map['exam_enroll.exam_plan_id'];
        $where['ExamSiteId'] = $map['exam_enroll.site_id'];
        $where['batch'] = $batch;
        $where_s['batch'] = 0;
        $order = 'work_id,Level,work_direction_id';
        $result_all = $this->SExamSeat->getAllInfo($where_s,'',$order);
        $where_o['id'] =4;
        $organize = $this->SOrganize->getBaseFind($where_o);
        $order_s = 'id desc';
        $room = $this->SExamRoom->getBaseInfo($where,'',$order_s);
        $seatNum = 1;
        if($room)
        {
            $roomNum = $room['examRoomNum'] + 1;
        }
        $end = end($result_all);
        foreach($result_all as $k=>$v)
        {
            $result = $this->existSeat($v,$batch);
            if($result)
            {
                $batch_next = $batch +1;
                continue;
            }else{
                $param['ExamRoomNum'] = $roomNum;
                $param['ExamSeatNum'] = $seatNum;
                $param['batch'] = $batch;
                $this->SExamSeat->updateInfo($param,array('id'=>$v['id']),false);

                if($seatNum == $organize['room_seat_count'])
                {
                    $data['ExamSiteId'] = $organize['id'];
                    $data['batch'] = $batch;
                    $data['seatCount'] = $organize['room_seat_count'];
                    $data['examRoomNum'] = $roomNum;
                    $data['batch'] = $batch;
                    $data['exam_plan_id'] = $map['exam_enroll.exam_plan_id'];
                    $data['ExamSiteId'] = $map['exam_enroll.site_id'];
                    $this->SExamRoom->addRoom($data);
                    $roomNum ++;
                    $seatNum = 1;
                }elseif($end==$v)
                {
                    $data['ExamSiteId'] = $organize['id'];
                    $data['batch'] = $batch;
                    $data['seatCount'] = $seatNum;
                    $data['examRoomNum'] = $roomNum;
                    $data['batch'] = $batch;
                    $data['exam_plan_id'] = $map['exam_enroll.exam_plan_id'];
                    $data['ExamSiteId'] = $map['exam_enroll.site_id'];
                    $this->SExamRoom->addRoom($data);
                }
                $seatNum++;
            }
            $count ++;
            if($count > $organize['seat_count'])
            {
                $batch_next = $batch+1;
                break;
            }
        }
        //初始化时间
        if($batch)
        {
            $data_time['exam_plan_id'] = $map['exam_enroll.exam_plan_id'];
            $data_time['exam_center'] = $map['exam_enroll.site_id'];
            $data_time['batch'] = $batch;
            if(!$this->SExamTime->getBase($data_time))
            {
                $data_time['exam_plan_id'] = $map['exam_enroll.exam_plan_id'];
                $data_time['exam_center'] = $map['exam_enroll.site_id'];
                $data_time['batch'] = $batch;
                $plan = $this->SExamPlan->getExamPlanData(array('id'=>$map['exam_enroll.exam_plan_id']));
                if($plan['theory_start_time'])
                {
                    $data_time['start_time'] = $plan['theory_start_time'];
                    $data_time['end_time'] = $plan['theory_end_time'];
                    $data_time['subject_id'] = 1;
                    $this->SExamTime->addData($data_time);
                }
                if($plan['comprehen_start_time'])
                {
                    $data_time['start_time'] = $plan['comprehen_start_time'];
                    $data_time['end_time'] = $plan['comprehen_end_time'];
                    $data_time['subject_id'] = 2;
                    $this->SExamTime->addData($data_time);
                }
                if($plan['operation_start_time'])
                {
                    $data_time['start_time'] = $plan['operation_start_time'];
                    $data_time['end_time'] = $plan['operation_end_time'];
                    $data_time['subject_id'] = 3;
                    $this->SExamTime->addData($data_time);
                }
            }
        }
        if($batch_next > $batch)
        {
            $this->roomDown($map,$batch_next);
        }
    }

    public function existSeat($map,$batch)
    {
        if($map['LiLun']) $where_or['LiLun'] = $map['LiLun'];
        if($map['ZhuanYe']) $where_or['ZhuanYe'] = $map['ZhuanYe'];
        if($map['ZongHe']) $where_or['ZongHe'] = $map['ZongHe'];
        $where['batch '] = $batch;
        return Db::table('exam_seat')->where($where)->where(function ($query) use($where_or){$query->whereor($where_or);})->order('batch desc')->find();
    }

    /**
     * @param Request $request
     * 生成座位排布
     */
    public function seatproduct(Request $request)
    {
        try{
            $subject = $this->SSubject->getAll();
            $where['ExamSiteId'] = $request->param('organize');
            $where['exam_plan_id'] = $request->param('plan');
            $pdf = new \fpdf\chinese('L');
            $pdf->AddGBFont('simhei', '楷体');
            $pdf->AddPage();
            $pdf->SetFont('simhei', '', 12);
            $organize = $this->SOrganize->getBaseFind(array('id'=>$where['ExamSiteId']));
            $str = '';
            $fu = "+";
            foreach($subject as $ks=>$vs)
            {
                if($ks>0)
                {
                    $pdf->AddPage();
                }
                unset($where[$str]);
                $str = $vs['id'] == 1 ? 'LiLun' : ($vs['id'] == 2 ? 'ZhuanYe' : 'ZongHe');
                $str_name = $vs['id'] == 1 ? '理论' : ($vs['id'] == 2 ? '综合' : '实操');
                $field = array("$str,ExamRoomNum,ExamSeatNum,exam_plan_id");
                $where[$str] = array('>','0');
                $enroll = $this->SExamSeat->getAllInfo($where,$field);
                if($enroll)
                {
                    $x = 10;
                    $y = 50;
                    $count = 1;
                    $rowCount = 1;
                    $num = $enroll[0]['ExamRoomNum'];
                    foreach ($enroll as $k => $v) {
                        $enrollInfo = $this->getEnrollInfo($v);
                        if($count > 30 || $num != $v['ExamRoomNum'] || $count==1)
                        {
                            //分页显示
                            if($count > 30 || $num != $v['ExamRoomNum'])
                            {
                                $pdf->AddPage();
                            }
                            $num = $v['ExamRoomNum'];
                            $pdf->SetFont('simhei', '', 12);
                            $pdf->Cell('',10,iconv("UTF-8","gbk",$organize['name']."考点".$enroll[0]['ExamRoomNum']."考场".$str_name."座位排布"),'0','0','C');
                            $pdf->SetFont('simhei', '', 9);
                            $pdf->Ln();
                            $pdf->Cell('',6,iconv("UTF-8","gbk","考试时间:2018-12-23 08:30:00"),'0','0','C');
                            $pdf->Ln();
                            $pdf->Cell(120,6,iconv("UTF-8","gbk","合计"),'0','0','R');
                            $x = 10;
                            $y = 50;
                        }
                        $pdf->setxy($x, $y);
                        $pdf->MultiCell(50, 6, iconv("utf-8", "gbk", $enrollInfo['workname']."            ".$enrollInfo['work_level_subject_level'] != 0 ? $this->level_name[$enrollInfo['work_level_subject_level']] : ''."\r\n".$enrollInfo['card']."  ".$enrollInfo['username']."\r\n ".$str_name), 1, 'left');
                        if ($rowCount % 5 == 0) {
                            $y = $y + 20;
                            $pdf->Ln();
                        } else {
                            if($count <= 5)
                            {
                                $fu = "+";
                                $x = $x + 55;
                            }elseif($count%5)
                            {
                                if($fu == '+')
                                {
                                    $x = $x - 55;
                                    $fu == '-';
                                }elseif($fu == '-')
                                {
                                    $x = $x + 55;
                                    $fu = "+";
                                }
                            }
                        }
                        $rowCount ++;
                        $count ++;
                    }
                }
            }
            $dir = 'download/seat/'.$request->param('plan').'/'.$request->param('organize').'/';
            if (!file_exists($dir)){
                mkdir ($dir,0777,true);
            }
            $pdf->Output($dir."/".$request->param('plan')."_".$request->param('organize')."_seat.pdf", 'F');
            $data['code'] = 200;
            return $data;
        }catch(Exception $e)
        {
            $data['code'] = 0;
            return $data;
        }
    }

    public function getSeatUser()
    {

    }

    /**
     * 桌贴
     */
    public function deskpaste(Request $request)
    {
        $roomList = $this->SExamRoom->getRoomPlanOrganize(array('ExamSiteId'=>$request->param('organize'),'exam_plan_id'=>$request->param('plan')));
        $organiez = $this->SOrganize->getBaseFind(array('id'=>$request->param('organize')));

        foreach ($roomList as $k=>$v)
        {
            $seatList_Lilun = $this->SExamSeat->getAllInfo(array('ExamSiteId'=>$request->param('organize'),'exam_plan_id'=>$request->param('plan'),'ExamRoomNum'=>$v['examRoomNum'],'LiLun'=>array('>','0')));
            $seatList_ZhuanYe = $this->SExamSeat->getAllInfo(array('ExamSiteId'=>$request->param('organize'),'exam_plan_id'=>$request->param('plan'),'ExamRoomNum'=>$v['examRoomNum'],'ZhuanYe'=>array('>','0')));
            $seatList_ZongHe = $this->SExamSeat->getAllInfo(array('ExamSiteId'=>$request->param('organize'),'exam_plan_id'=>$request->param('plan'),'ExamRoomNum'=>$v['examRoomNum'],'ZongHe'=>array('>','0')));
            $this->down($request,$seatList_Lilun,'LiLun',$organiez,$v);
            $this->down($request,$seatList_ZhuanYe,'ZhuanYe',$organiez,$v);
            $this->down($request,$seatList_ZongHe,'ZongHe',$organiez,$v);
        }
        $data['code'] = 200;
        return $data;
    }

    /**
     * @param $request
     * @param $data
     * @param $type
     * @param $organiez
     * @param $room
     * 桌贴pdf
     */
    public function down($request,$data,$type,$organiez,$room)
    {
        $examTime = $this->SExamTime->getBase(array('exam_plan_id'=>$request->param('plan'),'exam_center'=>$request->param('organize')));
        $x_count = 1;
        $y_count = 1;
        $count = 1;
        $pdf = new \fpdf\chinese();
        $pdf->AddGBFont('simhei', '楷体');
        $pdf->AddPage();
        $pdf->SetFont('simhei', '', 12);
        foreach ($data as $ks=>$vs)
        {
            //total length 62
            $card = $this->SExamCard->BaseFind(array('enroll_id'=>$vs[$type],'exam_plan_id'=>$request->param('plan')));
            if($count>30)
            {
                $pdf->AddPage();
                $x_count = 1;
                $y_count = 1;
                $count = 1;
            }
            if($x_count ==1)
            {
                $x = 0;
            }else{
                $x = $x +1;
            }
            if($y_count == 1)
            {
                $y = 0;
            }else{
                $y = 1;
            }
            $pdf->SetFont('simhei', '', 9);
            $pdf->setxy((62*($x_count-1))+$x+10,(23*($y_count-1))+$y+10);
            $pdf->Cell('40',6,iconv("UTF-8","gbk",$organiez['name']),'LT','0','L');
            $pdf->Cell('22',6,iconv("UTF-8","gbk",$room['examRoomNum']." 考场"),'TR','0','R');
            $pdf->setxy((62*($x_count-1))+$x+10,(23*($y_count-1))+$y+10+6);
            $pdf->SetFont('simhei', '', 9);
            $pdf->Cell('22',6,iconv("UTF-8","gbk","考试时间"),'L','0','C');
            $pdf->Cell('40',6,iconv("UTF-8","gbk",date('Y-m-d H:i:s',time())),'R','0','C');
            $pdf->SetFont('simhei', 'B', 18);
            $pdf->setxy((62*($x_count-1))+$x+10,(23*($y_count-1))+$y+10+6+6);
            $pdf->Cell('62',10,iconv("UTF-8","gbk",$card['card']),'LBR','0','C');
            $pdf->SetFont('simhei', '', 9);
            $x_count++;
            $count ++;
            if(($ks+1)%3 ==0)
            {
                $y_count ++;
                $x_count = 1;
            }
        }
        $dir = 'download/deskpaste/'.$request->param('plan').'/'.$request->param('organize');
        if (!file_exists($dir)){
            mkdir ($dir,0777,true);
        }
        $pdf->Output($dir."/".$request->param('plan')."_".$request->param('organize')."_seat_".$type.".pdf", 'F');
    }

    /**
     * @param $where
     * @return mixed
     * @throws \think\exception\DbException
     * 获取考生信息
     */
    public function getEnrollInfo($where)
    {
        $param['exam_enroll.id'] = isset($where['LiLun']) ? $where['LiLun'] : (isset($where['ZhuanYe']) ? $where['ZhuanYe'] : $where['ZongHe']);
        $enroll = $this->SExamEnroll->getworkForEnroll($param,'exam_enroll.id,exam_enroll.user_login_id,exam_enroll.exam_plan_id,exam_enroll.work_id,exam_enroll.work_level_subject_level,exam_enroll.site_id,exam_enroll.work_level_subject_level,work.name as workname');
        $user = $this->SUserinfo->findUserinfoData(array('user_login_id'=>$enroll['user_login_id']));
        $enroll['username'] = $user['username'];
        $card = $this->SExamCard->getCardInfo(array('enroll_id'=>$enroll['id'],'exam_plan_id'=>$where['exam_plan_id']));
        $enroll['card'] = $card['card'];
        return $enroll;
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 考务文件
     */
    public function allFile(Request $request)
    {
        $message['type'] = '考务文件';
        $message['code'] = '5';
        $list = action('CardController/getPlanMessage');
        foreach ($list as $k=>$v)
        {
            $list[$k]['code'] = 1;
        }
        return $this->thisView('card/index',['list'=>$list,'message'=>$message,'request'=>$request]);
    }
}