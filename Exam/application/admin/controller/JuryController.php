<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/22
 * Time: 11:58
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\common\model\ExamCenter;
use app\common\service\Area;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamPlan;
use app\common\service\ExamPlanWork;
use app\common\service\ExamStaffLog;
use app\common\service\Jury;
use app\common\service\JuryCertificate;
use app\common\service\Organize;
use app\common\service\WorkType;
use think\Request;


class JuryController extends AdminBase
{
    private $SexamPlan;
    private $SExamEnrollTable;
    private $SexamCenter;
    private $SExamPlanWork;
    private $SJury;
    private $SArea;
    private $SExamStaffLog;
    private $SOrganize;
    private $WorkTypeModel;
    private $SJuryCertificate;

    public function __construct()
    {
        parent::__construct();
        $this->SexamPlan        = new ExamPlan();
        $this->SExamEnrollTable = new ExamEnrollTable();
        $this->SexamCenter      = new ExamCenter();
        $this->SExamPlanWork    = new ExamPlanWork();
        $this->SJury            = new Jury();
        $this->SArea            = new Area();
        $this->SExamStaffLog    = new ExamStaffLog();
        $this->SOrganize        = new Organize();
        $this->WorkTypeModel    = new WorkType();
        $this->SJuryCertificate = new JuryCertificate();
    }

    /**
     * 考评员管理列表
     * @return \think\response\View
     * @user 李海江 2018/11/8~6:51 PM
     */
    public function lists()
    {
        $param = searchLike(request()->param());
        $field = ['id', 'name', 'organize_id', 'id_number', 'phone', 'create_time', 'status'];
        $list  = $this->SJury->getAll($param, $field);
        return view('', ['list' => $list]);
    }


    /**
     * 需改考评员信息
     * @return \think\response\View
     * @user 李海江 2018/11/8~6:51 PM
     */
    public function edit()
    {
        $id = Request::instance()->route('id');

        //获取所有组织
        $orgList = $this->SOrganize->getAll(['status' => 1]);
        $map     = ['id' => $id];
        $field   = ['id', 'name', 'id_number', 'organize_id', 'phone', 'status'];
        $res     = $this->SJury->getOne($map, $field);
        return view('', ['res' => $res, 'orglist' => $orgList]);
    }


    /**
     * 功能管理
     * @return \think\response\View
     * @user 李海江 2018/12/6~9:29 PM
     */
    public function manage()
    {
        $id   = Request::instance()->route('id');
        $list = $this->SJuryCertificate->getAll(['jury_id' => $id]);
        return view('', ['list' => $list]);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/7~9:44
     */
    public function distribution()
    {
        //获取所有鉴定计划
        $field               = ['id', 'title', 'work_type', 'work_type_name', 'enroll_starttime', 'enroll_endtime', 'exam_time', 'year', 'create_name', 'status'];
        $map                 = [];
        $map['status']       = config('ExamEnrollStatus.init');
        $arrData['title']    = '';
        $arrData['worktype'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet()) {
            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])) {
                $map['title'] = ['like', '%' . $arrData['title'] . '%'];
            } else {
                $arrData['title'] = '';
            }

            if (!empty($arrData['worktype'])) {
                $map['work_type'] = $arrData['worktype'];
            } else {
                $arrData['worktype'] = '';
            }

        }
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $type     = $this->WorkTypeModel->BaseSelect(["status" => 1]);
//print_r($type);die;
        $arrExamPlan = $this->SexamPlan->BaseSelectPage($paginate, $map, $field, "id desc");
        return view("distribution", ['arrExamPlan' => $arrExamPlan, 'map' => $arrData, "type" => $type, "now" => date("Y-m-d H:i:s")]);
    }

    /**
     * 详情展示
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details()
    {
        $arrWork  = [];
        $res      = true;
        $getArea  = "";
        $examArea = [];
        $arrData  = [];
        if (Request::instance()->isGet()) {
            $arrData = Request::instance()->param();
            if (empty($arrData['id'])) {
                $res = false;
            }
            //分配 获取全部地区
            if (!empty($arrData['getArea'])) {
                $getArea                 = $arrData['getArea'];
                $examMap['exam_plan_id'] = $arrData['id'];
                $field                   = "area.province,area.city,area.area,area.`code`,organize.address_code,exam_center.address_code as center_code,organize.name,exam_enroll.id,organize.id as organize_id,organize.type,exam_enroll.audit_site,exam_enroll.exam_site,exam_center.id as center_id";
                $examArea                = $this->SExamEnrollTable->getAreaByExamPlanId($examMap, $field, "exam_enroll.organize_id");
            }
            $province = $this->SexamCenter->BaseSelect(["type" => 1], ['address_code as center_code', "id as center_id", "name as exam_site"]);

            foreach ($province as $k => $v) {
                $province[$k]['organize_id'] = "";
            }

            $examArea = array_merge($province, $examArea);
            $examArea = array_unique_key($examArea, "center_id");

            $fields                      = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $map['exam_work.type']       = 5;
            $map['exam_work_level.type'] = 5;
            $arrWork                     = $this->getList($arrData['id'], $fields, $map);

            $staff_log = $this->SExamStaffLog->BaseSelect(['exam_plan_id' => $arrData['id'], "type" => 3]);
        }
//        print_r($arrWork);die;
        return view("details", ['arrWork' => $arrWork, "getArea" => $getArea, "examArea" => $examArea, "exam_plan_id" => $arrData['id'], "staff_log" => $staff_log]);
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
    public function getList($id = '', $field = "")
    {
        $map['exam_plan.id'] = $id;
        //查询鉴定计划所有的数据
        $arrExamPlan = $this->SexamPlan->getListData($map, $field);
        $arrExamPlan = collection($arrExamPlan)->toArray();
//        print_r($arrExamPlan);die;

        //取出不同的东西
        $mapField = ['wid', 'workname', 'wdname', 'work_level'];
        $arr      = array_columns($arrExamPlan, $mapField);
        //根据workid 去重
        $arrWork = array_unique_key($arrExamPlan, "wid");

        $arrMsg = ['无', '高级技师', '技师', '高级', '中级', '初级', '' => '无'];
        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k => $v) {
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr, ['wid' => $v['wid'],]), 'wdname'));
            $work_level            = array_unique(array_column(array_where($arr, ['wid' => $v['wid'],]), 'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
            foreach ($arrWork[$k]['level'] as $key => $val) {
                $arrWork[$k]['level'][$key] = $arrMsg[$val];
            }
        }
        return $arrWork;
    }

    /**
     * @return array|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/15~18:23
     */
    public function choiceJury()
    {
        $objWorkJury = [];
        if (Request::instance()->isGet()) {
            $arrData = input();
            if (!$arrData && empty($arrData['code']) || empty($arrData['exam_plan_id']) || empty($arrData['typeArea'])) {
                return layuiMsg(-1, "非法操作");
            }
            $fields                      = "exam_plan.id,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $map['exam_work.type']       = 5;
            $map['exam_work_level.type'] = 5;
            //获取单个的鉴定计划的工种级别
            $workExamPlan = $this->getList($arrData['exam_plan_id'], $fields, $map);

            $array = [];
            foreach ($workExamPlan as $k => $v) {
                foreach ($v['level'] as $kk => $vv) {
                    $array[]    = array_merge(["level" => $vv], ["wid" => $v['wid']]);
                    $arrLevel[] = $vv;
                }
            }
            $levelStatus = '01';
            if (in_array('3', $arrLevel) || in_array('4', $arrLevel) || in_array('5', $arrLevel)) {
                $levelStatus = '00';
            }
            //获取地区code  2是组织 1是鉴定中心
            if ($arrData['typeArea'] == 2) {
                $code = $this->SOrganize->BaseFind(['id' => $arrData['code']], "address_code")['address_code'];
            } else if ($arrData['typeArea'] == 3) {
                $arrData['code'] = substr($arrData['code'], 0, strlen($arrData['code']) - 1);
                $code            = $this->SexamCenter->BaseFind(['id' => $arrData['code']], "address_code")['address_code'];
            } else {
                $code = "";
            }
            $where = "";
            foreach ($array as $k => $v) {
                if ($levelStatus == 00) {
                    $where .= "jury.area=" . $code . " and work_id=" . $v['wid'] . " or ";
                } else {
                    $where .= "jury.area=" . $code . " and work_id=" . $v['wid'] . " and jury_certificate.card_level= 01 or ";
                }
            }
            $where       = substr($where, 0, strlen($where) - 3);
            $field       = "jury.id,jury.name,jury_certificate.card_no,jury_certificate.hire_time,jury_certificate.expire_date,jury.phone,id_number,jury_certificate.card_level";
            $objWorkJury = $this->SJury->getWorkByWorkId($where, $field, "jury.update_time");
//            print_r($where);die;
        }
        return view("examiners", ['objWorkJury' => $objWorkJury, 'address_code' => $code, "code" => $arrData['code'], 'typeArea' => $arrData['typeArea'], "exam_plan_id" => $arrData['exam_plan_id']]);
    }


    /**
     * 添加考评员
     * @return \think\response\View
     * @user 李海江 2018/11/28~10:35 AM
     */
    public function add()
    {
        $list = $this->SOrganize->getAll(['status' => 1]);
        return view('', ['list' => $list]);
    }

}