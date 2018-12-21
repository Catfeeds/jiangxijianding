<?php
namespace app\api\controller;
use app\common\service\Invoice;
use app\common\controller\BaseApi;
use think\Request;

class InvoiceController extends BaseApi{

    /**
     * @var Invoice
     */
    protected $SInvoice;

    /**
     * InvoiceController constructor.
     */
    public function __construct()
    {
        $this->SInvoice=new Invoice();
    }

    public function add(Request $request)
    {
//        $uid = model("User")->getUidByphone(\session('user.phone'));
        $dataArray = $request->post();
        //数据验证
        if($dataArray['type'] == 2){
            $result = $this->validate($dataArray,'ExamEnroll.invoiceOne');
            if (true !== $result) {
                return layuiMsg(-1, $result);
            }
        }else{
            $result = $this->validate($dataArray,'ExamEnroll.invoiceTwo');
            if (true !== $result) {
                return layuiMsg(-1, $result);
            }
        }
        $money=$dataArray['total_money'];
        unset($dataArray['total_money']);
        $dataArray['money'] =  $money;
        $dataArray['enroll_type'] = 1;
        $findRes = $this->SInvoice->selInvoiceData(['order_id'=>$dataArray['order_id']]);
        if (empty($findRes)) {
            $result = $this->SInvoice->saveInvoiceData($dataArray);
        } else {
            $result = $this->SInvoice->BaseUpdate($dataArray, ['order_id' => $dataArray['order_id']]);
        }
        if ($result) {
            return layuiMsg(1,'操作成功');
        } else {
            return layuiMsg(-1,'您未修改操作');
        }

    }



    public function delete()
    {

    }

    public function update()

    {

    }

    public function invoiceMail(){
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $res = $this->SInvoice->BaseUpdate(['status'=>4],['order_id'=>$arrData['id']]);
            if($res==true){
                return layuiMsg(1,'操作成功');
            }else{
                return layuiMsg(-1,'您未修改操作!');
            }
        };
    }

    public function updateInfo(){
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $res = $this->SInvoice->updateInvoiceData($arrData,['order_id'=>$arrData['id']]);
            if($res==true){
                return layuiMsg(1,'修改成功');
            }else{
                return layuiMsg(-1,'您未修改操作!');
            }
        };
    }
}