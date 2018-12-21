<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2018/12/17
 * Time: 11:43
 */

namespace app\admin\controller;
use app\common\service\JuryReview;
use app\common\service\ExamPlan;
use think\Request;
use app\common\controller\AdminBase;
use app\common\service\Organize;

class ExaminerController extends AdminBase
{
    private $SJuryReview;
    private $SExamPlan;
    private $SOrganize;

    public function __construct()
    {
        parent::__construct();
        $this->SJuryReview = new JuryReview();
        $this->SExamPlan = new ExamPlan();
        $this->SOrganize = new Organize();
    }

    /**
     * @return \think\response\View
     * @user 朱颖 2018/12/17~16:11
     */
    public function audit()
    {
        $map = [];
        $arrData = [];
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        if (Request::instance()->isPost() || Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (!empty($arrData['title'])){
                $map['exam_plan.title'] = ['like','%'.$arrData['title'].'%'];
            }else{
                $arrData['title'] = '';
            }
        }
        $field = ['count(jury_review.id) as num_jury,SUM(CASE WHEN jury_review.`status`<=1 THEN 1 ELSE 0 END) as audited,SUM(CASE WHEN jury_review.`status`>1 THEN 1 ELSE 0 END) as audit,`exam_plan`.`title`,`exam_plan`.`id`'];
        $group = 'exam_plan.id';
        $arrJuryList = $this->SExamPlan->getJury($paginate,$map,$field,'exam_plan.id desc',$group);
//        print_r();die;
        return view('',['arrJuryList'=>$arrJuryList,'map'=>$arrData]);
    }

    /**
     * @return \think\response\View
     * @user 朱颖 2018/12/17~16:11
     */
    public function organizeList()
    {
        $map = [];
        $exam_plan_id = '';
        if (Request::instance()->isGet())
        {
            $paginate = array(
                config('paginate.list_rows'),
                false,
                ['query' => request()->param()]
            );
            $arrData = Request::instance()->param();
            if (Request::instance()->isPost() || Request::instance()->isGet()){
                $arrData = Request::instance()->param();
                if (!empty($arrData['title'])){
                    $map['exam_plan.title'] = ['like','%'.$arrData['title'].'%'];
                }else{
                    $arrData['title'] = '';
                }
            }
            $exam_plan_id = $arrData['exam_plan_id'];
        }
        $field = ['count(jury_review.id) as num_jury,SUM(CASE WHEN jury_review.`status`<=1 THEN 1 ELSE 0 END) as audited,SUM(CASE WHEN jury_review.`status`>1 THEN 1 ELSE 0 END) as audit,`organize`.`name`,`organize`.`type`,`organize`.`id`'];
        $group = 'organize.id';

        $arrJuryList = $this->SOrganize->getJuryList($paginate,$map,$field,'organize.id desc',$group,$exam_plan_id);
        return view('',['arrJuryList'=>$arrJuryList,'map'=>$arrData,'exam_plan_id'=>$exam_plan_id]);
    }


    public function auditing()
    {
        $exam_plan_id = '';
        $organize_id = '';
        $arrData = [];
        if (Request::instance()->isPost() ||Request::instance()->isGet())
        {
            $arrData = Request::instance()->param();
//            print_r($arrData);die;

            if (!empty($arrData['jury_name'])){
                $map['jury_certificate.name'] = ['like','%'.$arrData['jury_name'].'%'];
            }else{
                $arrData['jury_name'] = '';
            }
            if (!empty($arrData['card_no'])){
                $map['jury_certificate.card_no'] = ['like','%'.$arrData['card_no'].'%'];
            }else{
                $arrData['card_no'] = '';
            }
            $exam_plan_id = $arrData['exam_plan_id'];
            $organize_id = $arrData['organize_id'];
        }
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $field = ['jury_review.*,`work`.`name` as work_name,jury_certificate.`name` as jury_name,jury_certificate.card_no '];
        $map['organize_id'] = $organize_id;
        $map['exam_plan_id'] = $exam_plan_id;
        $juryList = $this->SJuryReview->getOrganizeJury($paginate,$map,$field);
//        print_r($arrData);die;

        return view('',['juryList'=>$juryList,'map'=>$arrData,'exam_plan_id'=>$exam_plan_id,'organize_id'=>$organize_id]);

    }

    public function reason()
    {
        $arrData['id'] = "";
        if (Request::instance()->isGet())
        {
            $arrData = Request::instance()->param();
            $arr = explode(",",$arrData['id']);
            if (!$arrData && empty($arrData['id'])) {
                return layuiMsg(-1, "非法操作");
            }
        }
        $pass = xml_read(config('xml.appraiser_no_pass'));
        $newPass = [];
        foreach ($pass as $k=>$v)
        {
            $newPass[$k] = $v['item'];
        }
//        print_r($newPass);die;
        return view("",['id'=>$arrData['id'],'pass'=>$newPass]);

    }

}