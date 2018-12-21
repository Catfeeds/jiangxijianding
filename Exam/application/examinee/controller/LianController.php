<?php
namespace app\examinee\controller;
use app\common\controller\Examineebase;
use think\Controller;
use app\common\model\ExamPlan;
use app\common\model\WorkType;
use app\common\model\WorkLevel;
use app\common\model\WorkDirection;
use app\common\model\Work;
use think\Request;

class LianController extends Examineebase
{
    protected $ExamPlan;
    protected $WorkType;
    protected $WorkLevel;
    protected $WorkDirection;
    protected $Work;

    public function  __construct(Request $request)
    {
        parent::__construct($request);
        $this->ExamPlan=new ExamPlan();
        $this->WorkType=new WorkType();
        $this->WorkLevel=new WorkLevel();
        $this->WorkDirection=new WorkDirection();
        $this->Work=new Work();
    }

    //考生后台首页
    public function index()
    {
        //    获取鉴定计划数据
        $examPlandata= $this->ExamPlan->BaseSelect();
//        foreach($examPlandata as $k=>$v){
//            $examPlandata[$k]['work_type']=$this->WorkType->BaseFind(['id'=>$v['work_type']])->toArray();
//            $examPlandata[$k]['work']->$this->Work->BaseSelect(['wort_type_id'=>$examPlandata[$k]['work_type']['id']]);
//            foreach ($examPlandata[$k]['work'] as $key=>$vo){
//                $vo['direction']=$this->WorkDirection->BaseSelect(['work_id'=>$vo['id']]);
//                $vo['level']=$this->WorkLevel->BaseSelect(['work_id'=>$vo['id']]);
//            }
//        }
        $work=$this->Work->BaseSelect();
        return view('',['examPlan'=>$examPlandata,'work'=>$work]);
    }




}