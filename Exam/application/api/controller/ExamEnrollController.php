<?php
namespace app\api\controller;
use app\common\service\ExamEnroll;
use app\common\controller\BaseApi;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamOrder;
use app\common\service\ExamOrderDetail;
use think\Config;
use think\Request;
use app\common\service\WorkLevelSubject;
use app\common\service\ExamPlan;
use app\common\service\OrganizeWork;
use app\common\service\UserLogin;

class ExamEnrollController extends BaseApi{
    private $SExamEnroll;
    private $SExamEnrollOne;
    private $SExamOrder;
    private $SExamOrderDetail;
    private $SWorkLevelSubject;
    private $SExamPlan;
    private $SOrganizeWork;
    private $SUserLogin;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->SExamEnrollOne = new ExamEnroll();
        $this->SExamEnroll = new ExamEnrollTable();
        $this->SExamOrder  = new ExamOrder();
        $this->SExamOrderDetail = new ExamOrderDetail();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SOrganizeWork   = new OrganizeWork();
        $this->SExamPlan       = new ExamPlan();
        $this->SUserLogin      = new UserLogin();
    }

  public function add()
  {
      $request = Request::instance();
      if($request->isPost())
      {
          $webData = $request->post();
//          $map = [
//              'exam_plan_id' => $webData['exam_plan_id'],
//              'work_id' => $webData['work_id'],
////              'work_direction_id' => $webData['work_direction_id'],
//              'work_level_subject_level' => $webData['work_level_subject_level'],
//              'user_login_id' => session('user')['id'],
//          ] ;
////          $mapOriginal['type'] = session('user')['id_type'];
////          $mapOriginal['id_no'] = session('user')['id_card'];
////          $mapOriginal['current_level'] = $webData['current_level'];
////          $mapOriginal['work'] = $webData['work'];
////          $mapOriginal['certificate_no'] = $webData['certificate_no'];
//          //测试
//          $isRepetData = $this->SExamEnroll->findExamEnroll($map);
//          if(!empty($isRepetData)){
//              return layuiMsg(-1,'您已报过此次工种、方向、级别,请报其他信息!');
//          };
//          $countRepet = $this->SExamEnrollOne->BaseSelect(['user_login_id'=>session('user')['id']]);
////          dump(count($countRepet));
//          if(count($countRepet)>2){
//              return layuiMsg(-1,'您报考已经超过3次,不能再报哦!');
//          };

          $mapenroll = [
              'exam_plan_id' => $webData['exam_plan_id'],
              'user_login_id'=>session('user')['id'],
              'type'=>0,
              'organize_id'=>0,
              'source'=>7,
          ];
          //报考条件限制
          $data = $this->SExamEnrollOne ->conditions($webData,$mapenroll);
          if( !$data == false){
              return layuiMsg(-1,$data);
          }
          //数据验证
          $result = $this->validate($webData, 'ExamEnroll.examEnroll');
          if (true !== $result) {
              return layuiMsg(-1, $result);
          }
          //查询报考科目
          $subject = [
              'work_id' => $webData['work_id'],
              'level' => $webData['work_level_subject_level'],
          ];
          $subjectkind = $this->SWorkLevelSubject -> BaseSelect($subject);
          $i=0;
          $kind = [];
          foreach ($subjectkind as $k => $v){
              $kind[] =$v->subject_id;
              $i++;
          }
          $webData['user_login_id']=session('user')['id'];
          $webData['create_time'] = time();
          $webData['update_time'] = time();
          $ExamEnrollData = [
              'exam_plan_id' => $webData['exam_plan_id'],
              'work_id' => $webData['work_id'],
              'work_direction_id' => $webData['work_direction_id'],
              'work_level_subject_level' => $webData['work_level_subject_level'],
              'audit_way' => $webData['audit_way'],
              'exam_type' => $webData['exam_type'],
              'audit_site' => trim($webData['audit_site']),
              'exam_site' => trim($webData['exam_site']),
              'type' => 0,
              'organize_id' => 0,
              'remark' =>trim($webData['remark']),
              'user_login_id' => session('user')['id'],
              'source' =>7,
              'site_id' =>$webData['site_id'],
              'bar_code'=>(session('user')['id_card']).'1',
              'theory' => in_array(1,$kind)?1:0,
              'comprehen' => in_array(3,$kind)?1:0,
              'operation' => in_array(2,$kind)?1:0,
          ];
          $fileData = [
              'zheng'=>$webData['zheng'],
              'fan'=>$webData['fan'],
              'xueli'=>$webData['xueli'],
              'cert'=>$webData['cert'],
          ];
          $data = $this->SExamEnroll->addExamEnrollOne($ExamEnrollData,$fileData,$webData['audit_way']);
          if ($data == true){
              return layuiMsg(1, '添加成功');
          }else{
              return layuiMsg(-1, '添加失败');
          }
      }
  }

  public function delete()
  {
      $request = Request::instance();
      if($request->isPost())
      {
          $arrData = $request->post();
          if (!$arrData && empty($arrData['id'])){
              return layuiMsg(-1,'非法操作');
          }
          $objDel = $this->SExamEnrollOne->destroy($arrData);
          if ($objDel){
              return layuiMsg('1', '删除成功');
          }else{
              return layuiMsg('-1', '删除失败');
          }
      }
  }


  public function update()
  {
      $request = Request::instance();
      if($request->isPost())
      {
          $webData = $request->post();
          //数据验证
          $result = $this->validate($webData, 'ExamEnroll.examEnroll');
          if (true !== $result) {
              return layuiMsg(-1, $result);

          }
          $map['id'] = $webData['id'];
          $fileMap= $webData['id'];
          unset($webData['id']);
          $webData['update_time'] = time();
//          $data = $this->SExamEnrollOne->BaseUpdate($webData,$map);
//          print_r($data);die;
          $ExamEnrollData = [
              'exam_plan_id' => $webData['exam_plan_id'],
//              'work_type_id' => $webData['work_type_id'],
              'work_id' => $webData['work_id'],
              'work_direction_id' => $webData['work_direction_id'],
              'work_level_subject_level' => $webData['work_level_subject_level'],
              'audit_way' => $webData['audit_way'],
//              'exam_type' => $webData['exam_type'],
              'audit_site' =>$webData['audit_way']==0?trim($webData['audit_site']):'',
              'exam_site' => trim($webData['exam_site']),
              'type' => 0,
              'organize_id' => 0,
              'remark' =>trim($webData['remark']),
              'user_login_id' => session('user')['id'],
              'source' =>7,
          ];
//          dump($ExamEnrollData);die;
          $fileData = [
              'zheng'=>$webData['zheng'],
              'fan'=>$webData['fan'],
              'xueli'=>$webData['xueli'],
              'cert'=>$webData['cert'],
          ];
          $data = $this->SExamEnroll->editExamEnrollOne($ExamEnrollData,$map,$fileData,$fileMap,$webData['audit_way']);
//          dump($data);die;
          if ($data==true){
              return layuiMsg('1', '修改成功');
          }else{
              return layuiMsg('-1', '修改失败');
          }
      }
  }

  /**
   * [updateOrganize 机构报名信息修改]
   * @return [type] [description]
   */
  public function updateOrganize()
  {
    $request = Request::instance();
      if($request->isPost())
      {
        $webData = $request->post();
        $where['id'] = array('neq',$webData['login_id']);
        $where['mobile'] = $webData['mobile'];
        $data = $this->SUserLogin->BaseSelect($where);
        if(!empty($data))
        {
          return layuiMsg(-1,'手机号已存在');
        }
        unset($where['mobile']);
        $where['id_card'] = $webData['id_card'];
        $data = $this->SUserLogin->BaseSelect($where);
        if(!empty($data))
        {
          return layuiMsg(-1,'证件号已存在');
        }
        $webData['update_time'] = time();
        $data = $this->SExamEnroll->updateEnrollinfo($webData);
        if ($data===true){
              return layuiMsg('1', '修改成功');
        }else{
            return layuiMsg('-1', $data);
        }
      }
  }

  public function auditChick($id){
      $request = Request::instance();
      if($request->isGet())
      {
          $data = $this->SExamEnroll->updByid(['status'=>config('ExamEnrollStatus.submit')],['id'=>$id]);
          if ($data==true){
              //用户id
//              $user_login = $this->SExamEnroll->BaseFind($map)->user_login_id;
              return layuiMsg('1', '操作成功');
          }else{
              return layuiMsg('-1', '操作失败');
          }
      }
  }

  public function printApplyStatus(){

      $request=Request::instance();
      if($request->isGet()){
          $changeStatus = $request->get();
          $dataArray['status'] = $changeStatus['status'];
          $mapId['id'] = $changeStatus['id'];
          $printData= $this->SExamEnrollOne->updByid($dataArray,$mapId);
          if($printData==false){
              return layuiMsg(-1,"操作失败");
          }
      }else{
          return layuiMsg(-1,"请求失败");
      }
  }

  /**
   * 根据工种id 查询鉴定计划和机构权限的级别方向
   */
  public function getlevelByworkid()
  {
    $newArray = [];
    $request=Request::instance();
    $webData = $request->post();
    $examWhere['exam_plan.id'] = $webData['alltype_id'];
    $examWhere['exam_plan.status'] = 1;
    $examWhere['exam_work.type'] = config('ExamWorkType.exam');
    $examWhere['exam_work_level.type'] = config('ExamWorkType.exam');
    $examWhere['exam_plan.work_type'] = $webData['work_type'];
    $examWhere['work.id'] = $webData['work_id'];

    $organizeWhere['organize.status'] = 1;   //状态
    $organizeWhere['organize.id'] = session('organizeuser')['id'];   //id
    $organizeWhere = array_merge($organizeWhere,getTypeBy(session('organizeuser')['type']));
    $organizeWhere['work_type.id'] = $webData['work_type'];  
    $organizeWhere['work.id'] = $webData['work_id'];  

    $organizeWork = $this->SOrganizeWork->getWork($organizeWhere);
    $organizeWork = collection($organizeWork)->toArray();
    $arrWorkType = $this->SExamPlan->getDataWithTable($examWhere);
    $arrWorkType = collection($arrWorkType)->toArray();
    foreach ($organizeWork as $key => $val) 
    {
        $res = search($arrWorkType,$val);
        if($res !== false )
        {
            $newArray[] = $arrWorkType[$res];
        }
    }
    $direact = array_unique_key($newArray,'wdid');
    foreach ($direact as $key => $value) {
      if($value['wdid']==null)
      {
        unset($direact[$key]);
      }
      
    }
    
    $level   = array_unique_key($newArray,'wllevel');
    foreach ($level as $key => $value) {
        $wlevel[] = $value;
    }
    return layuiMsg(1,'获取成功',[$direact,$wlevel]);
  }

  /**
   * [报名提交审核]
   * @return [type] [description]
   * @author [zhanglele] <[email address]>
   */
  public function submit()
  {
    $arrAdmin = session("organizeuser");
    $arrWhere['organize_id'] = $arrAdmin['id'];
    $arrWhere['status'] = array(array('eq',config('ExamEnrollStatus.reject')),array('eq',config('ExamEnrollStatus.uploadfile')),'or');
    $whereor['audit_way'] = 0;
    $request=Request::instance();
    if($request->isPost()){

      $arrWhere['exam_plan_id'] = $request->post('plan');
      $field=['id'];
      $arrId = $this->SExamEnrollOne->where($arrWhere)->whereOr('organize_id='. $arrAdmin['id'].' and exam_plan_id='.$arrWhere['exam_plan_id'].' and audit_way=0 and status<10')->field($field)->select();

      $arrId = collection($arrId)->toArray();
      $arrId = array_column($arrId,'id');
      $where['id'] = array('in',$arrId);
      $data['status'] = config("ExamEnrollStatus.submit");
      $result = $this->SExamEnrollOne->BaseUpdate($data,$where);
      return layuiMsg(1,'成功提交'.$result.'条数据');
    }else{
      return layuiMsg(-1,'请求失败');
    }
    
  }

  /**
   * 撤销操作
   */
  
  public function cancel()
  {
    $arrAdmin = session("organizeuser");
    $arrWhere['organize_id'] = $arrAdmin['id'];
    $request = Request::instance();
    if($request->isPost())
    {
        $webData = $request->post();
        $arrWhere['id'] = $webData['id'];
        if($webData['type']=='cancel')
        {
          $data['status'] = config("ExamEnrollStatus.cancel");
        }
        else
        {
          $data['status'] = config("ExamEnrollStatus.submit");
        }
        $result = $this->SExamEnrollOne->BaseUpdate($data,$arrWhere);
        if ($result){
            return layuiMsg('1', '操作成功');
        }else{
            return layuiMsg('-1', '操作失败');
        }
    }
  }


  /**
   * [申请缓缴费]
   * @return [type] [description]
   */
  public function apply()
  {
    $arrAdmin = session("organizeuser");
    if($arrAdmin['is_institution'] == 1)
    {
        $column = 'institution_turnin_money';
    }else{
        $column = 'turnin_money';
    }
    $enrollWhere['organize_id'] = $arrAdmin['id'];
    $request = Request::instance();
    if($request->isPost())
    { 

        $webData = $request->post();
        $enrollWhere['exam_plan_id'] = $webData['plan_id'];
        $enrollWhere['status']       = array('in',[config('ExamEnrollStatus.checkpass'),config("ExamEnrollStatus.huanfalse")]);
        $orderData['order_num'] = 'JXL'.time();
        $orderData['total_money'] = $webData['price'];
        $orderData['pay_state']   = 5;
        $orderData['status']      = 1;
        $orderData['create_time'] = time();
        $orderData['type']        = $arrAdmin['type'];
        $orderData['type_id']     = $arrAdmin['id'];
        $orderData['exam_plan_id']= $webData['plan_id'];
        $orderData['remark']      = $webData['remark'];
        $orderDetail = $this->SExamEnroll->orderDetail($arrAdmin['id'],$webData['plan_id'],$enrollWhere['status'],$column);
        $res = $this->SExamOrder->addOrders($orderData,$enrollWhere,$orderDetail,['status'=>config('ExamEnrollStatus.huan')]);
        if($res===true)
        {
          return layuiMsg('1', '申请成功');
        }else{
          return layuiMsg('-1', '申请失败');
        }
    }
  }

  /**
   * [offlinePay 线下缴费]
   * @return [type] [description]
   */
  public function offlinePay()
  {
    $arrAdmin = session("organizeuser");
    $enrollWhere['organize_id'] = $arrAdmin['id'];
    $request = Request::instance();
    if($arrAdmin['is_institution'] == 1)
    {
        $column = 'institution_turnin_money';
    }else{
        $column = 'turnin_money';
    }
    if($request->isPost())
    { 
        $webData = $request->post();
        $file = $request->file('file');
        $uploadPath = ROOT_PATH . 'public' . DS .'uploads'.DS .'pay';
        if (! file_exists ($uploadPath )) {
            mkdir ( $uploadPath, 0755, true );
        }
        $info = $file->move($uploadPath);
        if(!$info)
        {
          return layuiMsg('-2','凭证上传失败');         
        } 
        $path = '/uploads/pay/'.$info->getSaveName();
        if(isset($webData['order_id']))
        {
          $enroll = $this->SExamOrderDetail->BaseSelect(['order_id'=>$webData['order_id']],['enroll_id']);
          $enroll = collection($enroll)->toArray();
          $enroll_id = array_column($enroll,'enroll_id');

          $enrollUp['status'] = config('ExamEnrollStatus.huan');
          $orderUp['pay_state'] = 2;
          $orderUp['water_no'] = $webData['water_no'];
          $orderUp['path']   = $path;
          $where['id'] = $webData['order_id'];
          $where['enroll'] = ['id'=>array('in',$enroll_id)];
          $res = $this->SExamOrder->orderUpdate($enrollUp,$orderUp,$where);

        }else{

          $enrollWhere['exam_plan_id'] = $webData['plan_id'];
          $enrollWhere['status']       = array(array('eq',config("ExamEnrollStatus.checkpass")),array('eq',config("ExamEnrollStatus.huanfalse")),'or');
          $orderData['order_num'] = 'JXL'.time();
          $orderData['total_money'] = $webData['price'];
          $orderData['pay_state']   = 2;
          $orderData['status']      = 1;
          $orderData['create_time'] = time();
          $orderData['type']        = $arrAdmin['type'];
          $orderData['type_id']     = $arrAdmin['id'];
          $orderData['exam_plan_id']= $webData['plan_id'];
          $orderData['water_no']      = $webData['water_no'];
          $orderData['path']        = $path;
          $orderDetail = $this->SExamEnroll->orderDetail($arrAdmin['id'],$webData['plan_id'],$enrollWhere['status'],$column);
          $res = $this->SExamOrder->addOrders($orderData,$enrollWhere,$orderDetail,['status'=>config('ExamEnrollStatus.payost')]);
        }

        if($res===true)
        {
          return layuiMsg('1', '操作成功');
        }else{
          return layuiMsg('-1', '操作失败');
        }
    }
  }

}