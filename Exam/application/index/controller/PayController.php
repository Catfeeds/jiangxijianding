<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/10
 * Time: 2:15 PM
 */

namespace app\index\controller;

use app\common\service\ExamOrder;
use app\common\service\ExamOrderPay;
use think\Controller;
use think\Request;

/**
 * Class PayController
 * @package app\index\controller
 */
class PayController extends Controller
{

    private $SExamOrder;
    private $SExamOrderPay;
    protected $request;

    public function __construct()
    {
        parent::__construct();
        $this->request       = Request::instance();
        $this->SExamOrder    = new ExamOrder();
        $this->SExamOrderPay = new ExamOrderPay();
    }


    /**
     * 请求例子
     * @return \think\response\View
     * @user 李海江 2018/12/18~2:55 PM
     */
    public function payOrd()
    {
        //发起一个提交前先创建一个订单 和 订单详情
        //创建订单号
        $order = make_order_num();
        //订单价格
        $amt = '0.01';
        return view('payOrd', ['order' => $order, 'amt' => $amt]);
    }

    /**
     * 发送支付申请
     * @return \think\response\View
     * @user 李海江 2018/12/18~1:51 PM
     */
    public function sendPayOrd()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $request   = $request->post();
            $data      = $this->makeArray($request['order'], $request['amt']);
            $hmac_safe = gethamc_safe($data);
            $hmac      = HmacMd5(implode($data), config('yeepay.merchantKey'));
            return view('sendPayOrd', ['data' => $data, 'hmac_safe' => $hmac_safe, 'hmac' => $hmac]);
        } else {
            $this->error('非法请求');
            die;
        }
    }

    /**
     * 构造数组
     * @param string $order 订单号
     * @param string $amt 价格 单位元,精确到分
     * @return array
     * @user 李海江 2018/12/18~2:54 PM
     */
    protected function makeArray($order, $amt)
    {
        $data                       = array();
        $data['p0_Cmd']             = config('yeepay.cmd');
        $data['p1_MerId']           = config('yeepay.p1_MerId');
        $data['p2_Order']           = $order;
        $data['p3_Amt']             = $amt;
        $data['p4_Cur']             = config('yeepay.currency');
        $data['p5_Pid']             = iconv('utf-8', 'gb2312', config('yeepay.name'));
        $data['p6_Pcat']            = iconv('utf-8', 'gb2312', config('yeepay.pcat'));
        $data['p7_Pdesc']           = iconv('utf-8', 'gb2312', config('yeepay.pdesc'));
        $data['p8_Url']             = config('yeepay.callback');
        $data['pb_ServerNotifyUrl'] = config('yeepay.async');
        $data['pm_Period']          = config('yeepay.validity');
        $data['pn_Unit']            = config('yeepay.unit');
        $data['pr_NeedResponse']    = config('yeepay.NeedResponse');
        return $data;
    }


    /**
     * 同步回调
     * @throws \think\exception\PDOException
     * @user 李海江 2018/12/18~7:51 PM
     */
    public function callback()
    {
        $data              = array();
        $data['p1_MerId']  = $_REQUEST['p1_MerId'];
        $data['r0_Cmd']    = $_REQUEST['r0_Cmd'];
        $data['r1_Code']   = $_REQUEST['r1_Code'];
        $data['r2_TrxId']  = $_REQUEST['r2_TrxId'];
        $data['r3_Amt']    = $_REQUEST['r3_Amt'];
        $data['r4_Cur']    = $_REQUEST['r4_Cur'];
        $data['r5_Pid']    = $_REQUEST['r5_Pid'];
        $data['r6_Order']  = $_REQUEST['r6_Order'];
        $data['r7_Uid']    = $_REQUEST['r7_Uid'];
        $data['r8_MP']     = $_REQUEST['r8_MP'];
        $data['r9_BType']  = $_REQUEST['r9_BType'];
        $data['hmac']      = $_REQUEST['hmac'];
        $data['hmac_safe'] = $_REQUEST['hmac_safe'];
        $time              = $_REQUEST['rp_PayDate'];
        $hmacLocal         = HmacLocal($data);
        $safeLocal         = gethamc_safe($data);
        //验签
        if ($data['hmac'] != $hmacLocal || $data['hmac_safe'] != $safeLocal) {
            $this->error('非法操作');
            die;
        } else {

            if ($data['r1_Code'] == "1") {
                //验证数据
                $res = $this->checkOrd($data, $time);
                if ($res) {
                    if ($data['r9_BType'] == "1") {
                        echo "<script>window.close()</script>";
                        die;
                    } elseif ($data['r9_BType'] == "2") {
                        #如果需要应答机制则必须回写success.
                        echo "SUCCESS";
                        return;
                    }
                } else {
                    $this->error('非法操作');
                    die;
                }
            }
            $this->error('支付失败,请重试');
            die;
        }
    }


    /**
     * 检查订单
     * @param $data
     * @param $time
     * @return bool
     * @throws \think\exception\PDOException
     * @user 李海江 2018/12/18~8:27 PM
     */
    protected function checkOrd($data, $time)
    {
        //查询订单表里的数据状态 是否是已经支付
        $res = $this->SExamOrder->getOne(['order_num' => $data['r6_Order']]);
        //如果订单不存在 或者 该订单已经支付 返回异常
        if (empty($res) || $res->pay_state == 4) {
            return false;
        } else {
            //修改订单表状态
            $orderState = $this->SExamOrder->modifyOrderStatus($data, $time);
            if ($orderState) {
                return true;
            } else {
                return false;
            }
        }
    }
}