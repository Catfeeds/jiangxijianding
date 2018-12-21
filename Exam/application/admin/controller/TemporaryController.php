<?php
/**
 * Created by PhpStorm.
 * User: zhuying
 * Date: 2018/11/21
 * Time: 18:00
 */

namespace app\admin\controller;

use app\common\service\ExamPlan;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamCenter;
use app\common\service\ExamStaffLog;

use think\Request;
use app\common\controller\AdminBase;

class TemporaryController extends AdminBase
{
    private $SexamPlan;
    private $SExamEnrollTable;
    private $SexamCenter;
    private $SExamStaffLog;
    public function __construct()
    {
        parent::__construct();
        $this->SexamPlan = new ExamPlan();
        $this->SExamEnrollTable = new ExamEnrollTable();
        $this->SexamCenter = new ExamCenter();
        $this->SExamStaffLog = new ExamStaffLog();
    }

    /**
     * @return \think\response\View
     * @user 朱颖 2018/11/22~9:06
     */
    public function index()
    {
        //获取所有鉴定计划
        $field = ['id','title','work_type','work_type_name','enroll_starttime','enroll_endtime','exam_time','year','create_name','status'];
        $map = [];
        $map['status'] = 1;
        $arrData['title'] = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet())
        {
            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }

            if (!empty($arrData['worktype'])){
                $map['work_type_name'] = ['like','%'.$arrData['worktype'].'%'];
            }else{
                $arrData['worktype'] = '';
            }
        }
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrExamPlan = $this->SexamPlan->BaseSelectPage($paginate,$map,$field,"id desc");
        return view("index",['arrExamPlan'=>$arrExamPlan,'map'=>$arrData,"now"=>date("Y-m-d H:i:s")]);
    }

    public function details()
    {
        $arrWork = [];
        $res = true;
        $getArea = "";
        $examArea = [];
        $arrData = [];
        if (Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (empty($arrData['id'])){
                $res = false;
            }
            //分配 获取全部地区
            if (!empty($arrData['getArea'])){
                $getArea = $arrData['getArea'];
                $examMap['exam_plan_id'] = $arrData['id'];
                $field = "area.province,area.city,area.area,area.`code`,organize.address_code,exam_center.address_code as center_code,organize.name,exam_enroll.id,organize.id as organize_id,organize.type,exam_enroll.audit_site,exam_enroll.exam_site,exam_center.id as center_id";
                $examArea = $this->SExamEnrollTable->getAreaByExamPlanId($examMap,$field,"exam_enroll.organize_id");
            }
            $province = $this->SexamCenter->BaseSelect(["type"=>1],['address_code as center_code',"id as center_id","name as exam_site"]);

            foreach ($province as $k=>$v)
            {
                $province[$k]['organize_id'] = "";
            }

            $examArea = array_merge($province,$examArea);
            $examArea = array_unique_key($examArea,"center_id");

            $fields = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrData['id'],$fields);
        }
        $staff_log = $this->SExamStaffLog->getUser(['exam_plan_id'=>$arrData['id'],"exam_staff_log.type"=>['in',[1,2,7,8]]],'exam_staff_log.*,temporary.phone');
//        print_r($staff_log);die;

        return view("details",['arrWork'=>$arrWork,"getArea"=>$getArea,"examArea"=>$examArea,"exam_plan_id"=>$arrData['id'],"staff_log"=>$staff_log]);
    }

    public function getList($id = '', $field = "", $map = [])
    {
        $map['exam_plan.id'] = $id;
        //查询鉴定计划所有的数据
        $arrExamPlan = $this->SexamPlan->getListData($map, $field);

        $arrExamPlan = collection($arrExamPlan)->toArray();

        //取出不同的东西
        $mapField = ['wid', 'workname', 'wdname', 'work_level'];
        $arr = array_columns($arrExamPlan, $mapField);
        //根据workid 去重
        $arrWork = array_unique_key($arrExamPlan, "wid");

        $arrMsg = ['无','高级技师','技师','高级','中级','初级',''=>'无'];
        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k => $v) {
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr, ['wid' => $v['wid'],]), 'wdname'));
            $work_level = array_unique(array_column(array_where($arr, ['wid' => $v['wid'],]), 'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
            foreach ($arrWork[$k]['level'] as $key=>$val)
            {
                $arrWork[$k]['level'][$key] = $arrMsg[$val];
            }
//            die;
        }
//        print_r($arrWork);die;

        return $arrWork;
    }

    /**
     * @return array|\think\response\View
     * @user 朱颖 2018/11/23~14:41
     */
    public function inputTemporary()
    {
        $arrData = [];
        if (Request::instance()->isGet()) {
            $arrData = input();
            if (!$arrData && empty($arrData['code']) || empty($arrData['exam_plan_id'])  || empty($arrData['typeArea'] ) || empty($arrData['roleType'] )) {
                return layuiMsg(-1,"非法操作");
            }
        }
        return view("input",['arrData'=>$arrData]);
    }

}