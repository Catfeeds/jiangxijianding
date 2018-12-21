<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/30
 * Time: 9:35
 */

namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\service\ExamOrder;
use app\common\service\ExamPlan;
use think\Request;

class SlowPaymentController extends AdminBase
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
     * @user 朱颖 {2018/10/30}~{15:23}
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
        //申请缓缴费状态
        $map['pay_state'] = 5;
        $map['review_log.reviewed_type'] = null;
        $map['review_log.status'] = null;
        $map['exam_order.status'] = 1;
        $arrSlowPayment = $this->SexamOrder->getDataJoinLog($paginate,$map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status,exam_plan.title as exam_plan_name"]);
//        print_r($map);die;
        return view("index",['arrSlowPayment'=>$arrSlowPayment,'map'=>$arrData,'toexamine'=>1]);
    }

    /**
     * 复审页面
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/20~16:27
     */
    public function reexamine()
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
        //申请缓缴费状态
        $map['pay_state'] = 5;
        $map['review_log.reviewed_type'] = 5;
        $map['review_log.status'] = 1;
        $map['exam_order.status'] = 1;
        $arrSlowPayment = $this->SexamOrder->getDataJoinLog($paginate,$map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status,exam_plan.title as exam_plan_name"],"review_log.update_time desc");
        $arrSlowPayment = array_unique_key($arrSlowPayment,"order_num");
        $arrSlowPayment = $this->SexamOrder->getDataJoinLog($paginate,$map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status,exam_plan.title as exam_plan_name"],"review_log.id desc","review_log.reviewed_type,review_log.review_type,review_log.reviewed_id");

        $map['review_log.status'] = [">",1];
        $arrSlowPayments = $this->SexamOrder->getDataJoinLog($paginate,$map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status,exam_plan.title as exam_plan_name"],"review_log.id desc","review_log.reviewed_type,review_log.review_type,review_log.reviewed_id");
        //复审完则 不能复审
        foreach ($arrSlowPayment as $k=>$v)
        {
            foreach ($arrSlowPayments as $key=>$val)
            {
                if ($v->id == $val->id && $v->reviewed_type == $val->reviewed_type)
                {
                    unset($arrSlowPayment[$k]);
                }
            }
        }
        return view("index",['arrSlowPayment'=>$arrSlowPayment,'map'=>$arrData,'toexamine'=>2]);
    }

    public function finals()
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
        //申请缓缴费状态
        $map['pay_state'] = 5;
        $map['review_log.reviewed_type'] = 5;
        $map['review_log.status'] = 3;
        $map['exam_order.status'] = 1;
        $arrSlowPayment = $this->SexamOrder->getDataJoinLog($paginate,$map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status,exam_plan.title as exam_plan_name"],"review_log.update_time desc");
        $map['review_log.status'] = [">",3];
        $arrSlowPayments = $this->SexamOrder->getDataJoinLog($paginate,$map,['exam_order.*',"review_log.reviewed_type","review_log.status as log_status,exam_plan.title as exam_plan_name"],"review_log.id desc","review_log.reviewed_type,review_log.review_type,review_log.reviewed_id");
        //终审完则 不能复审
        foreach ($arrSlowPayment as $k=>$v)
        {
            foreach ($arrSlowPayments as $key=>$val)
            {
                if ($v->id == $val->id && $v->reviewed_type == $val->reviewed_type)
                {
                    unset($arrSlowPayment[$k]);
                }
            }
        }
        return view("index",['arrSlowPayment'=>$arrSlowPayment,'map'=>$arrData,'toexamine'=>3]);
    }


    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/30}~{15:23}
     */
    public function detail()
    {
        $objDetail = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['exam_order.order_num'] = $arrData['order_num'];
            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card,user_login.name as user_name,organize.name as organize_name,organize.type as organize_type,exam_order.exam_plan_id,review_log.review_time,review_log.review_ip,review_log.reason,review_log.status as log_status,admin.name as admin_name";
            $objDetail = $this->SexamOrder->getDetailById($map,$field,"","review_log.update_time desc");
            $objDetail = array_unique_key($objDetail,"id_card");
            $exam_plan_id = 0;
        }
        foreach ($objDetail as $k=>$v)
        {
            $exam_plan_id = $v['exam_plan_id'];
        }
        $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
        $arrWork = $this->getList($exam_plan_id,$field);
        return view("detail",['objDetail'=>$objDetail,'arrWork'=>$arrWork]);
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

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/30}~{15:23}
     */
    public function review()
    {
        $objDetail = [];
        $arrData = [];
        $arrWork = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['exam_order.order_num'] = $arrData['order_num'];
            $field = "exam_order.order_num,exam_order.total_money as total_price,exam_order_detail.*,`work`.`name` as work_name,`work`.`code`,user_login.id_card,user_login.name as user_name,organize.name as organize_name,organize.type as organize_type,exam_order.exam_plan_id,review_log.review_time,review_log.review_ip,review_log.reason,review_log.status as log_status,admin.name as admin_name";
            $objDetail = $this->SexamOrder->getDetailById($map,$field,"","review_log.update_time desc");
            $objDetail = array_unique_key($objDetail,"id_card");
            $exam_plan_id = 0;
            foreach ($objDetail as $k=>$v)
            {
                $exam_plan_id = $v['exam_plan_id'];
            }
            $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($exam_plan_id,$field);
        }
//        print_r($arrWork);die;
        return view("review",['objDetail'=>$objDetail,"order_num"=>$arrData['order_num'],'arrWork'=>$arrWork]);
    }

    /**
     * @return \think\response\View
     * @user 朱颖 {2018/10/30}~{15:23}
     */
    public function disagree()
    {
        $arrData = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
        }
        $pass = xml_read(config('xml.slow_payment'));
        $newPass = [];
        foreach ($pass as $k=>$v)
        {
            $newPass[$k] = $v['item'];
        }
//        print_r($newPass);die;
        return view("disagree",["order_num"=>$arrData['order_num'],'pass'=>$newPass]);
    }

}