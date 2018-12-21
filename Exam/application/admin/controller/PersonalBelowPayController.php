<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/30
 * Time: 15:43
 */

namespace app\admin\controller;

use app\common\service\ExamOrder;
use think\Request;
use app\common\controller\AdminBase;

class PersonalBelowPayController extends AdminBase
{
    private $SexamOrder;

    public function __construct()
    {
        parent::__construct();
        $this->SexamOrder = new ExamOrder();
    }

    /**
     * @return \think\response\View
     * @user 朱颖 {2018/10/30}~{14:49}
     */
    public function index()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrData = [];
        if (Request::instance()->isPost() || Request::instance()->isGet()){

            $arrData = Request::instance()->param();

            if (!empty($arrData['order_num'])){
                $map['order_num'] = $arrData['order_num'];
            }else{
                $arrData['order_num'] = '';
            }
        }
        $map['pay_state'] = [[['=',2],['=',4]],'or'];   //线下支付
        $map['type'] = 1;  //个人线下支付
        $map['status'] = 1;

        $arrExamPayment = $this->SexamOrder->BaseSelectPage($paginate,$map);
//        print_r($arrExamPayment);die;
        return view("index",['arrExamPayment'=>$arrExamPayment,'map'=>$arrData]);

    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/31}~{9:49}
     */
    public function detail()
    {
        $objDetail = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['exam_order.order_num'] = $arrData['order_num'];
            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card";
            $objDetail = $this->SexamOrder->getDetailById($map,$field);
        }
        return view("detail",['objDetail'=>$objDetail]);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/31}~{9:49}
     */
    public function review()
    {
        $objDetail = [];
        $arrData = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['exam_order.order_num'] = $arrData['order_num'];
            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card";
            $objDetail = $this->SexamOrder->getDetailById($map,$field);
        }
        return view("review",['objDetail'=>$objDetail,"order_num"=>$arrData['order_num']]);
    }

    /**
     * @return \think\response\View
     * @user 朱颖 {2018/10/31}~{9:49}
     */
    public function agree()
    {
        $arrData = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
        }
        return view("agree",["order_num"=>$arrData['order_num']]);
    }

}