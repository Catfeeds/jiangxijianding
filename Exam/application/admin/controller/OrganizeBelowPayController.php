<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/31
 * Time: 9:09
 */

namespace app\admin\controller;

use app\common\service\ExamOrder;
use think\Request;
use app\common\service\ExamPlan;

use app\common\controller\AdminBase;

class OrganizeBelowPayController extends AdminBase
{
    private $SexamOrder;
    private $SexamPlan;

    public function __construct()
    {
        parent::__construct();
        $this->SexamOrder = new ExamOrder();
        $this->SexamPlan = new ExamPlan();

    }

    /**
     * @return \think\response\View
     * @user 朱颖 {2018/10/31}~{9:50}
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
                $map['order_num'] = ['like','%'.$arrData['order_num'].'%'];
            }else{
                $arrData['order_num'] = '';
            }
        }
        $map['pay_state'] = [[['=',2],['=',4]],'or'];   //线下支付
        $map['type'] = ['>',0];  //组织线下支付
        $map['status'] = 1;
//echo 1;die;
        $arrExamPayment = $this->SexamOrder->BaseSelectPage($paginate,$map,"","id desc");
        return view("index",['arrExamPayment'=>$arrExamPayment,'map'=>$arrData]);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/31}~{9:50}
     */
    public function detail()
    {
        $objDetail = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['exam_order.order_num'] = $arrData['order_num'];
            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card,user_login.name as user_name,organize.name as organize_name,organize.type as organize_type,exam_order.exam_plan_id,review_log.review_time,review_log.review_ip,review_log.reason,review_log.status as log_status,admin.name as admin_name";
//            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card";
            $objDetail = $this->SexamOrder->getDetailById($map,$field);
        }
//        print_r($this->SexamOrder->getLastSql());die;
        return view("detail",['objDetail'=>$objDetail]);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/31}~{9:50}
     */
    public function review()
    {
        $objDetail = [];
        $arrData = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['exam_order.order_num'] = $arrData['order_num'];
            $field = "exam_order.order_num,exam_order.path as order_path,exam_order.water_no,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card,user_login.name as user_name,organize.name as organize_name,organize.type as organize_type,exam_order.exam_plan_id,review_log.review_time,review_log.review_ip,review_log.reason,review_log.status as log_status,admin.name as admin_name";
//            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card";
            $objDetail = $this->SexamOrder->getDetailById($map,$field);
        }
//        print_r($objDetail);die;

        return view("review",['objDetail'=>$objDetail,"order_num"=>$arrData['order_num']]);
    }

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

    /**
     * @return \think\response\View
     * @user 朱颖 {2018/10/31}~{9:56}
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