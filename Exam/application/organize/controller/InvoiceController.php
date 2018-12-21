<?php  

namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\ExamOrder;
use app\common\service\Invoice;
use app\common\service\ExamOrderDetail;
use think\request;
class InvoiceController extends Organizebase
{
	private $SExamOrder;
	private $SInvoice;
	private $SExamOrderDetail;
	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$this->SExamOrder = new ExamOrder();
		$this->SInvoice   = new Invoice();
		$this->SExamOrderDetail = new ExamOrderDetail();
	}
	public function index()
	{
		$arrAdmin = session("organizeuser");
		$arrWhere['eo.type']=$arrAdmin['type'];
		$arrWhere['eo.type_id'] = $arrAdmin['id'];
		$arrWhere['eo.pay_state'] = array('in',[config('ExamOrderStatus.deferpay'),config('ExamOrderStatus.paysuccess')]);
		$field="eo.id,eo.order_num,eo.total_money,eo.pay_state,i.status as status";
		$orderData = $this->SExamOrder->invoiceOrder($arrWhere,$field);
		// print_r($orderData);die;
		return view('index',['orderData'=>$orderData]);

	}

	/**
	 * [detail 订单详情]
	 * @return [type] [description]
	 */
	public function detail()
	{
		if (Request::instance()->isGet())
        {
            $arrData = input('id');
            $where['od.order_id'] = $arrData;
            $detailData = $this->SExamOrderDetail->orderDetail($where);
            // dump($detailData);die;
            return view("detail", ['detailData' => $detailData]);
        }
	}
	/**
	 * 发票信息填写
	 * @return [type] [description]
	 */
	public function openInvoice()
	{
		if (Request::instance()->isGet())
        {
            $arrData = input('id');
            $money = $this->SExamOrder->BaseFind(['id'=>$arrData],['total_money']);
            return view("openInvoice", ['order_id' => $arrData,'total_money'=>$money['total_money']]);
        }
	}


	/**
	 * [receivetype 领取方式确认]
	 * @return [type] [description]
	 */
	public function receivetype()
	{
		$arrData = input('id');
		$type = input('type');
		$info = $this->SInvoice->BaseFind(['order_id'=>$arrData]);
		if($type=='express')
		{
			return view("receivetype", ['info' => $info,'status'=>4]);
		}else{
			return view("send", ['info' => $info,'status'=>5]);
		}
		
	}
}