<?php
namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\ExamStaffLog as SExamStaffLog;
use app\common\service\TemporaryExamStaffLog;
use app\common\service\JuryReview;


use think\Request;
                            
class ExamStaffLogController extends BaseApi{
    private $SExamStaffLog;
    private $StemporaryExamStaffLog;
    private $SjuryReview;

    public function __construct()
    {
        parent::__construct();
        $this->SExamStaffLog = new SExamStaffLog();
        $this->StemporaryExamStaffLog = new TemporaryExamStaffLog();
        $this->SjuryReview = new JuryReview();
    }
  public function add()
  {
      if (!Request::instance()->isPost()){
          return layuiMsg('-1', '非法操作');
      }
      $webData = Request::instance()->param();
      if (!$webData){
          return layuiMsg('-1', '频繁操作');
      }
      $adminuser = session('adminuser');

      $Verification = $this->SExamStaffLog->BaseSelect(['be_assigned_id'=>['in',$webData['jury']],'exam_plan_id'=>$webData['exam_plan_id'],'type'=>$webData['type']]);

      $Verif = $this->SExamStaffLog->selExamPlan(['be_assigned_id'=>['in',$webData['jury']],'exam_staff_log.type'=>$webData['type']]);

      //判断是否有重复数据
      if (!empty($Verification))
      {
          $name = '';
          foreach ($Verification as $k=>$v)
          {
              $name .= $v['name']."、";
          }
          $name = rtrim($name,"、");
          return layuiMsg(-1,"分配失败 ".$name." 已经被分配过此计划");
      }
      //判断时间 当天是否已有活
      if (!empty($Verif) && !empty($arrWork))
      {
          $name = '';
          foreach ($Verif as $k=>$v)
          {
              foreach ($arrWork as $key=>$val)
              {
                  if (date("Y-m-d",$v['exam_time'])==$val['exam_time'])
                  {
//                          echo 1;
                      $name .= $v['name'].",";

                  }
              }
          }
          if (!empty($name))
          {
              return layuiMsg(-1,"分配失败,".$name."当天已有任务");
          }
      }
      $addData = [];
      if (is_array($webData['jury'])){
          foreach ($webData['jury'] as $k=>$v)
          {
              $addData[$k]['exam_plan_id'] = $webData['exam_plan_id'];
              $addData[$k]['be_assigned_id'] = $v;
              $addData[$k]['name'] = $webData['name'][$k];
              $addData[$k]['site_id'] = $webData['code'];
              $addData[$k]['type'] = $webData['type'];      //考评人员
              $addData[$k]['site_type'] = $webData['typeArea'];
              $addData[$k]['exam_place'] = $webData['address_code'];
              $addData[$k]['distribution_id'] = $adminuser['center_id'];
              $addData[$k]['distribution_type'] = $adminuser['center_type'];
              $addData[$k]['status'] = 1;
              $addData[$k]['create_time'] = time();
              $addData[$k]['update_time'] = time();
          }
      }
      $return = $this->SExamStaffLog->BaseSaveAll($addData);
      if ($return)
      {
          return layuiMsg(1,'分配成功');
      }else{
          return layuiMsg(-1,'分配失败,网络错误');
      }
  }

  public function addTemporary()
  {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->param();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }

//        print_r($webData);die;
        $adminuser = session('adminuser');

        $arrTemporary = $this->StemporaryExamStaffLog->addTemporary($webData,$adminuser);
        return $arrTemporary;
  }

  public function delete()
  {
      if (!Request::instance()->isPost()){
          return layuiMsg('-1', '非法操作');
      }
      $webData = Request::instance()->param();
      if (!$webData){
          return layuiMsg('-1', '频繁操作');
      }
      $objDel = $this->SExamStaffLog->destroy(['id'=>$webData['id']]);
      if ($objDel){
          return layuiMsg(1, "删除成功");
      }else{
          return layuiMsg(-1, "删除失败");
      }
  }
    //审核考评员
  public function audited()
  {
      if (!Request::instance()->isPost()){
          return layuiMsg('-1', '非法操作');
      }
      $webData = Request::instance()->param();
      if (!$webData){
          return layuiMsg('-1', '频繁操作');
      }
      $adminuser = session('adminuser');
      $arrAudit = $this->SjuryReview->selectAllWork(['jury_review.id'=>['in',$webData['id']]],['jury_review.*','jury_certificate.name','organize.address_code']);
//      $arrAudit = $this->SjuryReview->BaseSelect(['id'=>['in',$webData['id']]]);
      $where = 'type = 3 and ';
      foreach ($arrAudit as $k=>$v)
      {
          $where .= 'be_assigned_id='.$v['jury_id'].' and exam_plan_id='.$v['exam_plan_id'].' and site_id='.$v['organize_id'].' or ';
      }
      $where = substr($where,0,strlen($where)-3);
      $logJury = $this->SExamStaffLog->where($where)->select();
      if (!empty($logJury))
      {
          $name = '';
          foreach ($logJury as $k=>$v)
          {
              $name .= $v['name']."、";
          }
          $name = rtrim($name,"、");
          return layuiMsg(-1,"审核失败 ".$name." 已经被分配过此计划");
      }
      $webData['id'] = explode(',',$webData['id']);
      $addData = [];
      if (is_array($webData['id'])){
          foreach ($arrAudit as $k=>$v)
          {
              $addData[$k]['exam_plan_id'] = $v['exam_plan_id'];
              $addData[$k]['be_assigned_id'] = $v['jury_id'];
              $addData[$k]['name'] = $v['name'];
              $addData[$k]['site_id'] = $v['organize_id'];
              $addData[$k]['type'] = 3;      //考评人员
              $addData[$k]['site_type'] = 2;    //组织
              $addData[$k]['exam_place'] = $v['address_code'];
              $addData[$k]['distribution_id'] = $adminuser['center_id'];
              $addData[$k]['distribution_type'] = $adminuser['center_type'];
              $addData[$k]['status'] = 1;

              if(isset($webData['reason']) || isset($arrWhere['pass_reason'])){
                  $addData[$k]['status'] = 2;
              }

              $addData[$k]['create_time'] = time();
              $addData[$k]['update_time'] = time();
          }
      }
      $arrLog = $this->SExamStaffLog->addJuryLog($webData,$addData);
      return $arrLog;
  }

  public function update()

  {

  }
}