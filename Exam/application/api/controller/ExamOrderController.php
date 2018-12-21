<?php
namespace app\api\controller;
use app\common\service\ExamOrder;
use app\common\service\ExamEnroll;
use app\common\service\WorkLevelSubject;
use app\common\controller\BaseApi;
use app\common\service\ExamOrderDetail;
use think\Request;
                            
class ExamOrderController extends BaseApi{

    /**
     * @var ExamOrder
     */
    private $SExamOrder;
    /**
     * @var ExamEnroll
     */
    private $SExamEnroll;
    /**
     * @var WorkLevelSubject
     */
    private $SWorkLevelSubject;
    private $SExamOrderDetail;


    /**
     * ExamOrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExamOrder = new ExamOrder();
        $this->SExamEnroll = new ExamEnroll();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SExamOrderDetail  = new ExamOrderDetail();
    }

    public function add()
  {

  }

  public function delete()
  {

  }

  public function update()

  {

  }


    /**
     * 新增订单
     * @return array
     * @user xuweiqi 2018/10/22
     */
    public function payAction()
    {
        $webData = Request::instance()->post();
        $order_num = 'JX'.rand(5,10).time();
        $typeId=$webData['type_id'];
        $orderDetailData['enroll_id'] = $typeId;
        $orderDetailData['order_num'] = $order_num;
        $orderDetailData['total_money'] = $webData['total_money'];
        $orderDetailData['status'] = 1;
        $orderDetailData['level'] = $webData['level'];

        unset($webData['level']);
        $webData['pay_state'] = 2;
        $webData['status'] = 1;
        $webData['order_num'] = $order_num;
        $webData['type'] = 0;

        $enrollStatus['status']=config("ExamEnrollstatus.paypass");
        $enrollMap['id'] = $typeId;
//        $arrExamEnroll = $this->SExamEnroll->findExamEnroll($enrollMap);
//        $array=[
//            'wls.work_id'=>$arrExamEnroll['work_id'],
//            'wls.level'=>$arrExamEnroll['work_level_subject_level'],
//        ];
//        $subject=$this->SWorkLevelSubject->selSubject($array,['s.name,wls.price']);
//
//        dump(array_merge($orderDetailData,$subject));die;
        $res = $this->SExamOrder->addOrder($webData,$orderDetailData,$enrollStatus,$enrollMap);
        if ($res === true) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(-1, '操作失败');
        }
    }

    /**
     * [apply 同一订单多次提交缓缴费]
     * @return [type] [description]
     */
    public function apply()
    {
      $webData = Request::instance()->post();
      $enroll = $this->SExamOrderDetail->BaseSelect(['order_id'=>$webData['order_id']],['enroll_id']);
      $enroll = collection($enroll)->toArray();
      $enroll_id = array_column($enroll,'enroll_id');

      $enrollUp['status'] = config('ExamEnrollStatus.huan');
      $orderUp['pay_state'] = 5;
      $orderUp['remark'] = $webData['remark'];
      $where['id'] = $webData['order_id'];
      $where['enroll'] = ['id'=>array('in',$enroll_id)];
      $res = $this->SExamOrder->orderUpdate($enrollUp,$orderUp,$where);
      if($res===true)
      {
        return layuiMsg(1,'申请成功');
      }else{
        return layuiMsg(-1,'申请失败');
      }

    }
}