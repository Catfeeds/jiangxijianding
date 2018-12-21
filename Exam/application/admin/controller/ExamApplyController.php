<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/25
 * Time: 17:45
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\common\service\WorkType;
use app\common\service\ExamApply;
use think\Request;

class ExamApplyController extends AdminBase
{
    private $WorkTypeModel;
    private $ExamApplyModel;
    public function __construct()
    {
        parent::__construct();
        $this->ExamApplyModel = new ExamApply();
        $this->WorkTypeModel = new WorkType();
    }
    public function index()
    {
        $arrAdmin = session("organizeuser");
        // $arrApplyList = $this->ExamApplyModel->selectApply(['organize_id'=>$arrAdmin['id']]);
        $map = [];
//        $map['organize_id'] = $arrAdmin['id'];
        $map['status'] = 1;
        $field = ['*'];
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrExamPlan = $this->ExamApplyModel->selectApply($paginate,$map,$field);
        return view('applyshow',['applyList'=>$arrExamPlan,'toexamine'=>1]);
    }
    public function second()
    {
        $map = [];
        $map['status'] = 2;
        $field = ['*'];
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrExamPlan = $this->ExamApplyModel->selectApply($paginate,$map,$field);
//        print_r($arrExamPlan);die;

        return view('applyshow',['applyList'=>$arrExamPlan,'toexamine'=>2]);
    }

    /**
     * 详情
     * @return \think\response\View
     * @user 朱颖 {2018/10/26}~{11:42}
     */
    public function applydetail()
    {
        $work = [];
        if(Request::instance()->isGet())
        {
            $arrData = input();
            $field = "exam_apply.*,exam_work.work_name,exam_work.work_id,exam_work_level.work_level";
            $arrWhere['exam_apply.id'] = $arrData['id'];
            $arrWhere['exam_work.type'] = 1;
            $work = $this->applydata($arrWhere,$field);
        }
//        print_r($work);die;
        return view("applydetail",['arrWork'=>$work]);
    }

    /**
     * 对申请数据做的处理
     * @param  [type] $arrWhere [description]
     * @param  [type] $field    [description]
     * @return [type]           [description]
     */
    public function applydata($arrWhere,$field)
    {
        $result = $this->ExamApplyModel->getApplyDesc($arrWhere,$field);
        $result = collection($result)->toArray();
        $map = ['title','work_type','exam_time','work_id','work_level','exam_num','work_name'];
        $arrApply = array_columns($result,$map);
        $work = array_unique_key($result,"work_id");
        $applyList = [];
        foreach ($work as $k=>$v){
            $work[$k]['wdname'] = array_unique(array_column(array_where($arrApply,['work_id'=>$v['work_id'],]),'work_name'));
            $work_level = array_unique(array_column(array_where($arrApply,['work_id'=>$v['work_id'],]),'work_level'));
            sort($work_level);
            $work[$k]['level'] = $work_level;
        }
        return $work;
    }

    /**
     * @return \think\response\View
     * @user 朱颖 {2018/10/26}~{11:42}
     */
    public function because()
    {
        $arrData = [];
        $newPass = [];
        if(Request::instance()->isGet())
        {
            $arrData = Request::instance()->Get();
            $pass = xml_read(config('xml.special_application'));
            foreach ($pass as $k=>$v)
            {
                $newPass[$k] = $v['item'];
            }
//            print_r($pass);die;
        }
        return view('because',['arrData'=>$arrData,'pass'=>$newPass]);

    }
}