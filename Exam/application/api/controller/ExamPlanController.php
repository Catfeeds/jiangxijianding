<?php
namespace app\api\controller;
use app\common\service\ExamPlanWork;
use app\common\service\ExamPlan;
use app\common\service\WorkType;
use app\common\service\Work;
use app\common\service\ExamCenter;
use app\common\service\ExamEnrollTable;
use app\common\service\Jury;
use app\common\service\ExamStaffLog;
use app\common\controller\BaseApi;
use app\common\service\Grade;
use app\common\service\ExamEnroll;
use app\common\service\WorkDirection;
use app\common\service\WorkLevelSubject;

use think\Request;
class ExamPlanController extends BaseApi{

    private $SexamPlanWork;
    private $SexamPlan;
    private $WorkType;
    private $Work;
    private $SExamCenter;
    private $SExamEnrollTable;
    private $SJury;
    private $SExamStaffLog;
    private $SGrade;
    private $SExamEnroll;
    private $SWorkDirection;
    private $SworkLevelSubject;

    public function __construct()
    {
        parent::__construct();
        $this->SexamPlanWork = new ExamPlanWork();
        $this->SexamPlan = new ExamPlan();

        $this->WorkType = new WorkType();
        $this->Work = new Work();
        $this->SExamCenter = new ExamCenter();
        $this->SExamEnrollTable = new ExamEnrollTable();
        $this->SExamStaffLog = new ExamStaffLog();

        $this->SJury = new Jury();
        $this->SGrade = new Grade();
        $this->SExamEnroll = new ExamEnroll();
        $this->SworkLevelSubject = new WorkLevelSubject();

        $this->SWorkDirection = new WorkDirection();
    }

    /**
     * 添加鉴定计划
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->param();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }

        $validata = Validate('app\admin\validate\ExamPlan');
        //场景应用
        if (!$validata->scene('addexamplan')->check($webData)) {
            return layuiMsg(-1, $validata);
        }

        $now = date("Y-m-d");
        $enroll = explode("到",$webData['enroll']);
        if ($enroll[0] && $enroll[1]){

            $enroll[0] = str_replace("00:00:00","09:00:00",$enroll[0]);
            $enroll[1] = str_replace("00:00:00","19:00:00",$enroll[1]);
            $webData['audit_endtime'] = str_replace("00:00:00","19:00:00",$webData['audit_endtime']);
            $webData['pay_endtime'] = str_replace("00:00:00","19:00:00",$webData['pay_endtime']);

            $webData['enroll_starttime'] = strtotime($enroll[0]);
            $webData['enroll_endtime'] = strtotime($enroll[1]);

            $webData['audit_endtime'] = strtotime($webData['audit_endtime']);
            $webData['pay_endtime'] = strtotime($webData['pay_endtime']);
            $webData['exam_time'] = strtotime($webData['exam_time']);
            unset($webData['enroll']);
            if (!$webData['enroll_starttime'] || $now > date("Y-m-d",$webData['enroll_starttime'])){
                return layuiMsg('-1', '请重新选择报名开始时间');
            }
            if (!$webData['enroll_endtime'] || $webData['enroll_endtime'] <= $webData['enroll_starttime']){
                return layuiMsg('-1', '请重新选择报名截止时间');
            }
            //判断审核时间是否大于报名截止时间
            if (!$webData['audit_endtime'] || $webData['enroll_endtime'] >= $webData['audit_endtime']){
                return layuiMsg('-1', '请重新选择审核截止时间');
            }
            //判断缴费时间是否大于审核时间
            if (!$webData['pay_endtime'] || $webData['enroll_endtime'] >= $webData['pay_endtime']){
                return layuiMsg('-1', '请重新选择缴费截止时间');
            }
            //判断打印准考证开始时间是否大于缴费时间
            $print = explode("到",$webData['print']);
            if (!$print[0] || !$print[1]){
                return layuiMsg('-1', '请重新选择打印准考证时间');
            }
            $print[0] = str_replace("00:00:00","09:00:00",$print[0]);
            $print[1] = str_replace("00:00:00","19:00:00",$print[1]);


            $webData['print_starttime'] = strtotime($print[0]);
            $webData['print_endtime'] = strtotime($print[1]);
            unset($webData['print']);
            if (!$webData['print_starttime'] || $webData['print_starttime'] <= $webData['pay_endtime']){
                return layuiMsg('-1', '请重新选择打印准考证时间');
            }
            if (!$webData['print_endtime'] || $webData['print_endtime'] <= $webData['print_starttime']){
                return layuiMsg('-1', '请重新选择打印准考证截止时间');
            }
            //判断考试时间是否大于准考证打印结束时间
            if (!$webData['exam_time'] || $webData['print_endtime'] >= $webData['exam_time']){
                return layuiMsg('-1', '请重新选择考试时间');
            }
        }

        $work_type = $this->WorkType->BaseFind(['id'=>$webData['work_type']]);


        $webData['work_type_name'] = $work_type->name;
        $arrAdmin = session("adminuser");
        $webData['create_id'] = $arrAdmin['id'];
        $webData['create_name'] = $arrAdmin['username'];
        $year = date("Y",$webData['exam_time']);
        $webData['year'] = $year;
        $webData['batch_num'] = 1;
        $batch_num = $this->SexamPlan->BaseFind(['year'=>$year],'max(batch_num) as batch_num')->batch_num;
        if ($batch_num){
            $webData['batch_num'] = $batch_num + 1;
        }
        $webData['create_time'] = time();
        $webData['update_time'] = time();
        $arrExamWork = [];
        $work_id = $webData['work'];

        unset($webData['work']);
        foreach ($work_id as $key=>$val){
            $arrExamWork[$key]['work_id'] = $val;
            $arrExamWork[$key]['work_type'] = $webData['work_type'];
            //鉴定中心
            $arrExamWork[$key]['type'] = 5;
            $arrExamWork[$key]['status'] = 1;
            $arrExamWork[$key]['create_time'] = time();
            $arrExamWork[$key]['update_time'] = time();
        }
        $workid = implode(",",$work_id);

        $new = [];
        $level = $this->SworkLevelSubject->selWorktype(['work_id'=>['in',$workid]],['work_level_subject.*,work.work_type_id'],'','','work_id,level');

        $adminuser = session('adminuser');
        $center_type = $adminuser['center_type'];
        if(!empty($level))
        {
            foreach ($level as $k=>$v)
            {
                $new[$k]['exam_work_id'] = $v['work_id'];
                $new[$k]['type'] = 5;
                $new[$k]['work_level'] = $v['level'];

                //省的a
                if ($v['work_type_id']==config("WorkLevel.a") && $center_type == 1 && $webData['type']!=4)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.aSheng")))
                    {
                        unset($new[$k]);
                    }
                    //省的b
                }
                if($v['work_type_id']==config("WorkLevel.b") && $center_type == 1)
                {

                    if (!in_array($new[$k]['work_level'],config("WorkLevel.bSheng")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.c") && $center_type == 1)
                {

                    if (!in_array($new[$k]['work_level'],config("WorkLevel.cSheng")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.z") && $center_type == 1)
                {

                    if (!in_array($new[$k]['work_level'],config("WorkLevel.zhuanSheng")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.y") && $center_type == 1)
                {

                    if (!in_array($new[$k]['work_level'],config("WorkLevel.yuSheng")))
                    {
                        unset($new[$k]);
                    }
                }
                if($webData['type']==4 && $center_type == 1)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.jiSheng")))
                    {
                        unset($new[$k]);
                    }
                }
                //市a
                if ($v['work_type_id']==config("WorkLevel.a") && $center_type == 2  && $webData['type']!=4)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.aShi")))
                    {
                        unset($new[$k]);
                    }
                    //市的b
                }
                if($v['work_type_id']==config("WorkLevel.b") && $center_type == 2)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.bShi")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.c") && $center_type == 2)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.cShi")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.z") && $center_type == 2)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.zhuanShi")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.y") && $center_type == 2)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.yuShi")))
                    {
                        unset($new[$k]);
                    }
                }
                if($webData['type']==4 && $center_type == 2)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.jiShi")))
                    {
                        unset($new[$k]);
                    }
                    //县a
                }
                if ($v['work_type_id']==config("WorkLevel.a") && $center_type == 3  && $webData['type']!=4)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.aXian")))
                    {
                        unset($new[$k]);
                    }
                    //县的b
                }
                if($v['work_type_id']==config("WorkLevel.b") && $center_type == 3)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.bXian")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.c") && $center_type == 3)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.cXian")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.z") && $center_type == 3)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.zhuanXian")))
                    {
                        unset($new[$k]);
                    }
                }
                if($v['work_type_id']==config("WorkLevel.y") && $center_type == 3)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.yuXian")))
                    {
                        unset($new[$k]);
                    }
                }

                if($webData['type']==4 && $center_type == 3)
                {
                    if (!in_array($new[$k]['work_level'],config("WorkLevel.jiXian")))
                    {
                        unset($new[$k]);
                    }
                }

            }
        }


        $work = $this->Work->getLastByid($workid);

        foreach ($arrExamWork as $k=>$v)
        {
            foreach ($work as $key=>$val){
                if ($v['work_id'] == $val['id']){
                    $arrExamWork[$k]['work_name'] = $val['name'];
                }
            }
        }

        //添加
        $add = $this->SexamPlanWork->addTable($webData,$arrExamWork,$new);
        if ($add === false){
            return layuiMsg(-1, "添加失败");
        }else{
            return layuiMsg(1, "添加成功");
        }
    }

    /**
     * 删除鉴定计划
     * @return array
     */
    public function delete()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->post();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }


        $objDel = $this->SexamPlan->destroy($webData);
        //删除另一张表
        $examwork['exam_id'] = $webData['id'];
        $examwork['type'] = 5;
        $objExamWork = $this->SexamPlanWork->deletes($examwork,'examwork');

        $examworklevel['alltype_id'] = $webData['id'];
        $examworklevel['type'] = 5;

        $objExamWorkLevel = $this->SexamPlanWork->deletes($examworklevel,'examworklevel');

        if ($objDel && $objExamWork && $objExamWorkLevel){
            return layuiMsg(1, "删除成功");
        }else{
            return layuiMsg(-1, "删除失败");
        }
    }


    /**
     * 修改考试计划
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->post();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }
        $validata = Validate('app\admin\validate\ExamPlan');
        //场景应用
        if (!$validata->scene('updexamplan')->check($webData)) {
            return layuiMsg(-1, $validata);
        }
//        print_r($webData);die;

        $enroll_starttime = $this->SexamPlan->BaseFind(['id'=>$webData['id']])['enroll_starttime'];

        $now = date("Y-m-d");

        $enroll = explode("到",$webData['enroll']);
        if ($enroll[0] && $enroll[1]){
            $enroll[0] = str_replace("00:00:00","09:00:00",$enroll[0]);
            $enroll[1] = str_replace("00:00:00","19:00:00",$enroll[1]);
            $webData['audit_endtime'] = str_replace("00:00:00","19:00:00",$webData['audit_endtime']);
            $webData['pay_endtime'] = str_replace("00:00:00","19:00:00",$webData['pay_endtime']);
            $webData['enroll_starttime'] = strtotime($enroll[0]);
            $webData['enroll_endtime'] = strtotime($enroll[1]);

            $webData['audit_endtime'] = strtotime($webData['audit_endtime']);
            $webData['pay_endtime'] = strtotime($webData['pay_endtime']);
            $webData['exam_time'] = strtotime($webData['exam_time']);
            unset($webData['enroll']);
            if (!$webData['enroll_endtime'] || $webData['enroll_endtime'] <= $webData['enroll_starttime']){
                return layuiMsg('-1', '请重新选择报名截止时间');
            }
            //判断审核时间是否大于报名截止时间
            if (!$webData['audit_endtime'] || $webData['enroll_endtime'] >= $webData['audit_endtime']){
                return layuiMsg('-1', '请重新选择审核截止时间');
            }
            //判断缴费时间是否大于审核时间
            if (!$webData['pay_endtime'] || $webData['enroll_endtime'] >= $webData['pay_endtime']){
                return layuiMsg('-1', '请重新选择缴费截止时间');
            }
            //判断打印准考证开始时间是否大于缴费时间
            $print = explode("到",$webData['print']);
            if (!$print[0] || !$print[1]){
                return layuiMsg('-1', '请重新选择打印准考证时间');
            }
            $print[0] = str_replace("00:00:00","09:00:00",$print[0]);
            $print[1] = str_replace("00:00:00","19:00:00",$print[1]);
            $webData['print_starttime'] = strtotime($print[0]);
            $webData['print_endtime'] = strtotime($print[1]);
            unset($webData['print']);
            if (!$webData['print_starttime'] || $webData['print_starttime'] <= $webData['pay_endtime']){
                return layuiMsg('-1', '请重新选择打印准考证时间');
            }
            if (!$webData['print_endtime'] || $webData['print_endtime'] <= $webData['print_starttime']){
                return layuiMsg('-1', '请重新选择打印准考证截止时间');
            }
            //判断考试时间是否大于准考证打印结束时间
            if (!$webData['exam_time'] || $webData['print_endtime'] >= $webData['exam_time']){
                return layuiMsg('-1', '请重新选择考试时间');
            }
        }
        $work_type = $this->WorkType->BaseFind(['id'=>$webData['work_type']]);
        $webData['work_type_name'] = $work_type->name;
        $arrAdmin = session("adminuser");
        $webData['create_id'] = $arrAdmin['id'];
        $webData['create_name'] = $arrAdmin['username'];
        $year = date("Y",$webData['exam_time']);
        $webData['year'] = $year;
        $webData['update_time'] = time();
        $arrExamWork = [];
        $work_id[] = $webData['work'];
        unset($webData['work']);

        foreach ($work_id as $k=>$v)
        {
            foreach ($v as $key=>$val){
                $arrExamWork[$key]['work_id'] = $val;
                $arrExamWork[$key]['work_type'] = $webData['work_type'];
                //鉴定中心
                $arrExamWork[$key]['type'] = 5;
                $arrExamWork[$key]['update_time'] = time();
            }
            $workid = implode(",",$v);

        }


        $work = $this->Work->getLastByid($workid);

        foreach ($arrExamWork as $k=>$v)
        {
            foreach ($work as $key=>$val){
                if ($v['work_id'] == $val['id']){
                    $arrExamWork[$k]['work_name'] = $val['name'];
                    $arrExamWork[$k]['exam_id'] = $webData['id'];
                }
            }
        }
        $where['id']=$webData['id'];
        unset($webData['id']);

        $level = $webData['level'];
        unset($webData['level']);
        foreach ($level as $k=>$v){
            foreach ($v as $key=>$val){
                $a[]['work_level'][$k] = $val;
                $new[]['work_level'] = $val;
                foreach ($a as $kk=>$vv){
                    foreach ($vv['work_level'] as $ke =>$va ){
                        if ($va == $val && $ke == $k ){
                            $new[$kk]['exam_work_id'] = $k;
                            $new[$kk]['type'] = 5;
                            $new[$kk]['create_time'] = time();
                            $new[$kk]['update_time'] = time();
                        }
                    }

                }
            }

        }


        //修改数据
        $update = $this->SexamPlanWork->updTable($where,$webData,$arrExamWork,$new,$type=5);
//        print_r($update);die;
        if ($update == false)
        {
            return layuiMsg('-1', '修改失败');
        }else{
            return layuiMsg('1', '修改成功');
        }
    }

    /**
     * 修改鉴定计划工种级别
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function updateWork()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->post();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }
        $work_id = $webData['work'];
        $exam_id = $webData['examid'];
        $work_type = $webData['work_type'];
        $work_type_name = $webData['work_type_name'];

        $addWorkArr = [];
        foreach ($work_id as $key=>$val){
            $addWorkArr[$key]['exam_id'] = $exam_id;
            $addWorkArr[$key]['work_name'] = $val;
            $addWorkArr[$key]['work_id'] = $key;
            $addWorkArr[$key]['work_type'] = $work_type;
            $addWorkArr[$key]['status'] = 1;
            //鉴定计划;
            $addWorkArr[$key]['type'] = 5;
            $addWorkArr[$key]['create_time'] = time();
            $addWorkArr[$key]['update_time'] = time();
        }
        $addWorkLevelArr = [];
        if(array_key_exists('worklevel',$webData)){
            $work_level = $webData['worklevel'];
            foreach ($work_level as $key=>$val){
                foreach ($val as $lkey=>$lval) {
                    $addWorkLevelArr[$key."".$lval]['work_id']=$key;
                    $addWorkLevelArr[$key."".$lval]['alltype_id'] = $exam_id;
                    $addWorkLevelArr[$key."".$lval]['work_level'] = $lval;
                    //鉴定中心[$lkey]
                    $addWorkLevelArr[$key."".$lval]['type'] = 5;
                    $addWorkLevelArr[$key."".$lval]['create_time'] = time();
                    $addWorkLevelArr[$key."".$lval]['update_time'] = time();

                }
            }
        }
        //修改数据
        $update = $this->SexamPlanWork->updExamWork($exam_id,$addWorkArr,$addWorkLevelArr,$work_type,$work_type_name);
//        print_r($update);die;
        if ($update == false)
        {
            return layuiMsg('-1', '修改失败!');
        }else{
            return layuiMsg('1', '修改成功!');
        }
    }

   /**
     * @根据 exam_plan中的id 获取work中的name
     * @param Request $request
     * @user 朱颖 9.19
     * @return array
     */
      public function selbyid(Request $request)
      {
          $webData = $request->post();
          $ExamPlandata['ep.id'] = $webData['exam_plan_id'];

          $field="ep.*,w.code,w.name,w.id as wid";
          $join=array(
              ["__EXAM_WORK__ ew","ep.id=ew.exam_id",'left'],
              ["__WORK__ w","ew.work_id=w.id",'left'],
          );
          $data = $this->SexamPlan->BaseJoinSelect('ep',$join,$ExamPlandata,[$field]);
          $datas = $this->SexamPlan->BaseSelect(['id'=>$webData['exam_plan_id']])[0];
            if ($data){
                return layuiMsg('1', '获取成功',[$data,$datas]);
            }else{
                return layuiMsg('-1', '此名称下暂无可报名工种');
            }

      }

      /**
       * [driectionBywork 根据工种id获取方向]
       * @param  Request $request [description]
       * @return [type]           [description]
       */
      public function driectionBywork(Request $request)
      {
        $webData = $request->post();
        $direction = $this->SWorkDirection->BaseSelect($webData);
        if($direction){
          return layuiMsg('1', '获取成功',$direction);
        }else{
          return layuiMsg('-1', '此名称下暂无可报名工种');
        }
      }

       /**
     * @根据 exam_plan中的id 获取work中的name
     * @param Request $request
     * @user xuweiqi 9.19
     * @return array
     */
      public function examSelbyid(Request $request)
      {
          $webData = $request->post();
          //鉴定计划
          $webData['type'] = 5;
          //查询计划的所有工种
          $data=$this->SexamPlan->selectExamPlan($webData);
          //查询类别
          $data1=$this->SexamPlan->findExamPlanData($webData);
            if ($data == true)
            {
                return layuiMsg('1', '获取成功',[$data,$data1]);
            }else{
                return layuiMsg('-1', '此名称下暂无可报名工种');
            }

      }

      public function selAuditExamSite(Request $request)
      {
          if (Request::instance()->isPost()) {
               $webData = $request->post();
              $typeprovid=['type'=>1];
              $centerStatus=['status'=>1];
              $mapMerge = array_merge($typeprovid,$centerStatus);
              $typeArea['type'] =  array(['=',2],['=',3],'or');
              $mapTypeMerge = array_merge($typeArea,$centerStatus);

              if($webData['work_type_id']==1){
                  //A类3级以上
                  if( in_array($webData['work_level_subject_level'],['1','2','3'])  ){
                      $allCenterObj=$this->SExamCenter->centerSiteAll($mapMerge);
                      return layuiMsg(1,'获取成功',[$allCenterObj,$allCenterObj]);
                  }elseif( in_array($webData['work_level_subject_level'],['4','5']) ){
                      $allCenterObj=$this->SExamCenter->centerSiteAll($centerStatus);
                      return layuiMsg(1,'获取成功',[$allCenterObj,$allCenterObj]);
                  }else{
                      return layuiMsg(-1,'此级别下暂无鉴定中心');
                  }
              }elseif(in_array($webData['work_type_id'],['2'])){
                  $allCenterObj=$this->SExamCenter->centerSiteAll($mapMerge);
                  $allCenterAreaObj=$this->SExamCenter->centerSiteAll($mapTypeMerge);
                  return layuiMsg(1,'获取成功',[$allCenterObj,$allCenterAreaObj]);
              }else{
                  return layuiMsg(-1,'此级别下暂无鉴定中心');
              }
          }
      }



    /**
     * 判断是否可以分配
     * @param $jury
     * @param $arrWork
     * @return bool|string
     * @user 朱颖 {2018/10/24}~{12:27}
     */
      public function isTrue($jury,$arrWork)
      {
//          $res = false;
          foreach ($jury as $k=>$v)
          {
              foreach ($arrWork as $key=>$val)
              {
                  if ($val['enroll_endtime'] > date("Y-m-d H:i:s"))
                  {
                      $res = "此工种报名未截止,请先为其他工种分配";
                      return $res;
                  }
                  if ($v['wname']==$val['workname'] )
                  {
                      //判断级别是否符合 鉴定计划
                      if ( in_array( $v['work_level'] ,$arrWork[$key]['level']) || $v['work_level'] < $arrWork[$key]['level'][count($arrWork[$key]['level'])-1] )
                      {
                          $res = true;
                          return $res;
                      }else{
                          $res = $v['name']."的工种级别不适合此鉴定计划";
                          return $res;
                      }

                  }else{
                      $res = $v['name']."的工种不适合此鉴定计划";
                      return $res;
                  }
              }
          }

      }

    /**
     * 获取联查数据
     * @param string $id
     * @param $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($id='',$field = "")
    {
        $map['exam_plan.id'] = $id;
//        $map['exam_work.type'] = 5;
//        $map['exam_work_level.type'] = 5;
        //查询鉴定计划所有的数据
        $arrExamPlan = $this->SexamPlan->getListData($map,$field);

        $arrExamPlan = collection($arrExamPlan)->toArray();


        //取出不同的东西
        $mapField = ['wid','workname','wdname','work_level'];
        $arr = array_columns($arrExamPlan,$mapField);
        //根据workid 去重
        $arrWork = array_unique_key($arrExamPlan,"wid");

        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k=>$v){
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'wdname'));
            $work_level = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
        }
        return $arrWork;
    }

    public function release()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->post();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }
        $map['status'] = 1;
        $release = $this->SexamPlan->BaseUpdate($map,['id'=>$webData['id']]);
        if ($release == true)
        {
            return layuiMsg('1', '发布成功');
        }else{
            return layuiMsg('-1', '发布失败');
        }
    }

    /**
     * 根据鉴定类型查找鉴定计划
     * @user wangzhong
     * @param Request $request
     */
    public function plan(Request $request)
    {
        $data = $request->post();
        $where[$data['type']=='exam'?'type':'work_type'] = $data['type_id'];
        $where['enroll_starttime'] =['<=',time()];
        $where['enroll_endtime'] = ['>=',time()];
       $infos = $this->SexamPlan->where($where)->field('id,title')->select();

        return $infos;
    }

    //批量补考
    public function makeExam(){
        $grade_no = $_POST['data'];
        $exam_plan_id = $_POST['exam_plan_id'];
        foreach($grade_no as $k=>$v){
            $data[$k]= $this->SGrade ->BaseFind($v);
        }
        foreach ($data as $a=>$b){
            $newData[$a]['user_login_id'] = session('user')['id'];
            $newData[$a]['exam_plan_id'] = $exam_plan_id;
            $newData[$a]['work_id'] = $b['work_id'];
            $newData[$a]['work_direction_id'] = $b['work_direction_id'];
            $newData[$a]['work_level_subject_level'] = $b['level'];
            $newData[$a]['exam_type'] = 2;
            $newData[$a]['type'] = 0;
            $newData[$a]['organize_id'] = 0;
            $newData[$a]['source'] = 7;
            $newData[$a]['status'] = config('ExamEnrollStatus.submit');
            $newData[$a]['audit_way'] = 1;
            $newData[$a]['thesis_state'] = 0;
        }

        $i=1;
        //报考条件限制
        foreach ($newData as $v){
            $data = $this->SExamEnroll ->conditions($v);
            if( !$data == false){
                return layuiMsg(-1,'第'.$i.'条,'.$data);
            }
            $i++;
        }

        $valueStr = $this->SExamEnroll->BaseSaveAll($newData);
        if($valueStr){
            return layuiMsg(1,'提交成功！');
        }else{
            return layuiMsg(-1,'提交失败');
        }
    }

}