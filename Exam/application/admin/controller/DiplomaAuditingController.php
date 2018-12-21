<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/31
 * Time: 14:16
 */

namespace app\admin\controller;
use think\Request;
use app\common\service\Certificate;
use app\common\controller\AdminBase;
use app\common\service\ExamPlan;
use app\common\service\ExamEnroll;
use app\common\service\Grade;
use app\common\service\ApplyCertificate;
use app\common\service\ApplyCertDetail;

class DiplomaAuditingController extends AdminBase
{
    private $Scertificate;
    private $SexamPlan;
    private $SexamEnroll;
    private $Sgrade;
    private $SapplyCertificate;
    private $SapplyCertDetail;
    public function __construct()
    {
        parent::__construct();
        $this->Scertificate = new Certificate();
        $this->SexamPlan = new ExamPlan();
        $this->SexamEnroll = new ExamEnroll();
        $this->Sgrade = new Grade();
        $this->SapplyCertificate = new ApplyCertificate();
        $this->SapplyCertDetail = new ApplyCertDetail();
    }

    /**
     * 空白证书审核列表
     * @return \think\response\View
     * @user 朱颖 2018/11/2~10:44
     */
    public function index()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $applyCertificate = $this->SapplyCertificate->BaseSelectPage($paginate);
        return view('',['applyCertificate'=>$applyCertificate]);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/2~10:44
     */
    public function detail()
    {
        $objDetail = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            $map['certificate.id'] = $arrData['id'];
            $field = "certificate.*,user_login.id_card,`work`.`name`,work_type.`name` as work_type_name,exam_enroll.*,work_direction.`name` as work_direction_name";
            $objDetail = $this->Scertificate->getDetailById($map,$field);
//            print_r($this->Scertificate->getLastSql());die;
        }
        return view("detail",['objDetail'=>$objDetail]);

    }

    /**
     * 空白证书申请列表
     * @return \think\response\View
     * @user 朱颖 2018/12/18~11:16
     */
    public function applyList()
    {
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $applyCertificate = $this->SapplyCertificate->BaseSelectPage($paginate);
        return view('',['applyCertificate'=>$applyCertificate]);
    }

    /**
     * 添加页面
     * @return \think\response\View
     * @user 朱颖 2018/12/20~9:36
     */
    public function certificate()
    {
        return view();
    }

    /**
     * @return \think\response\View
     * @user 朱颖 2018/12/20~9:37
     */
    public function certData()
    {
        $plan = Request::instance()->get();
//        print_r($plan);die;
        return view('',['cert_id'=>$plan['cert_id']]);
    }

    /**
     * 空白证书编号导入
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @user 朱颖 2018/12/20~9:37
     */
    public function certificateImport()
    {
        $adminuser = session('adminuser');
        vendor("PHPExcel.PHPExcel");
        $file = request()->file('file');
        $cert_id = request()->post('cert_id');
        if (is_null($file)) {
            return layuiMsg('-1','文件不能为空!');
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'certificate');
        $arr = [];
        if ($info) {
            $exclePath = $info->getSaveName();
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'certificate' . DS . $exclePath;
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();
            array_shift($excel_array);
            $data = [];
            $apply_num = $this->SapplyCertificate->BaseFind(['id'=>$cert_id])['apply_num'];
            if ($apply_num != count($excel_array))
            {
                return layuiMsg('-1','审核失败,生成个数不对应');
            }
            foreach ($excel_array as $k => $v)
            {
                $data[$k]['apply_cert_id'] = $cert_id;
                $data[$k]['certificate_code'] = trim($v[0]);
                $data[$k]['center_id'] = $adminuser['center_id'];
                $data[$k]['create_id'] = $adminuser['id'];
            }
            $arr = $this->SapplyCertDetail->addLog($data,$cert_id,$adminuser['center_id'],$adminuser['id']);
        }
        return $arr;
    }

    /**
     * 鉴定计划列表
     * @user 朱颖 2018/12/20~9:43
     */
    public function examPlanList()
    {
        //获取所有鉴定计划
        $field = ['id','title','work_type','work_type_name','enroll_starttime','enroll_endtime','exam_time','year','create_name','status'];
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $map['status'] = 1;
        $today = strtotime(date("Y-m-d",time()));
        $map['exam_time'] = ['<',$today];
        $arrExamPlan = $this->SexamPlan->BaseSelectPage($paginate,$map,$field,"update_time desc");
//        print_r($arrExamPlan);die;
        return view('',['arrExamPlan'=>$arrExamPlan,'map'=>$map]);
    }

    public function scoreList()
    {
        $arrGrade = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->get();
            //分页参数
            $paginate = array(
                config('paginate.list_rows'),
                false,
                ['query' => request()->param()]
            );
            $arrGrade = $this->Sgrade->getGradeByidWithPage($paginate,['result'=>1,'grade.exam_plan_id'=>$arrData['exam_plan_id']],['grade.*','exam_order.pay_state']);
        }
//        print_r($this->Sgrade->getLastSql());die;
        return view('',['arrGrade'=>$arrGrade,'exam_plan_id'=>$arrData['exam_plan_id']]);
    }
}