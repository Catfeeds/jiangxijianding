<?php  

namespace app\organize\controller;

use app\common\controller\Organizebase;
use think\Request;
use app\common\service\WorkType;
use app\common\service\ExamApply;

class ExamApplyController extends Organizebase
{
	private $WorkTypeModel;
	private $ExamApplyModel;
	private $SOrganizeWork;
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->ExamApplyModel = new ExamApply();
		$this->WorkTypeModel = new WorkType();
	}

	/**
	 * 机构申请考试列表
	 * @return [type] [description]
	 * @author [张乐乐] <[email address]>
	 */
	public function applyshow()
	{
		$arrAdmin = session("organizeuser");
		$map = [];
		$map['organize_id'] = $arrAdmin['id'];
		$field = ['*'];
		$paginate = array(
           config('paginate.list_rows'),
           false,
           ['query' => request()->param()]
        );
        $arrExamPlan = $this->ExamApplyModel->selectApply($paginate,$map,$field);
		return view('applyshow',['applyList'=>$arrExamPlan,'type'=>'/organize/Exam_apply/applyshow','title'=>'考试申请']);
	}

	public function applyadd()
	{
		$workType = $this->WorkTypeModel->BaseSelect(['status'=>1]);
        return view("applyadd",['workType'=>$workType]);
	}
	/**
	 * 详情
	 * @return [type] [description]
	 */
	public function applydetail()
	{
		$arr = config('EnrollStatusText.work_level_subject_level');
		if(Request::instance()->isGet())
		{
			$arrData = input();
			$field = "exam_apply.*,exam_work.work_name,exam_work.work_id,exam_work_level.work_level,review_log.reason as reasons";
			$arrWhere['exam_apply.id'] = $arrData['id'];
			$arrWhere['exam_work.type'] = 1;
			// $arrWhere['review_log.reviewed_type'] = 3;
			$work = $this->applydata($arrWhere,$field);
			if ($arrData['id']){
            	return view("applydetail",['arrWork'=>$work,'arr'=>$arr]);
        	}else{
            	return view("/ExamApply/applyshow");
        	}	

		}

	}

	public function applyupdate()
	{
		if(Request::instance()->isGet())
		{
			$arr = config('EnrollStatusText.work_level_subject_level');
			$arrData = input();
			$field = "exam_apply.*,exam_work.work_name,exam_work.work_id,exam_work_level.work_level";
			$arrWhere['exam_apply.id'] = $arrData['id'];
			$arrWhere['exam_work.type'] = 1;
			$work = $this->applydata($arrWhere,$field);
			$data = array_column($work,'work_type');
			$data = $this->ExamApplyModel->selbyworktype(['work_type'=>$data[0]]);
			$workType = $this->WorkTypeModel->BaseSelect(['status'=>1]);
			if ($arrData['id']){
            	return view("applyupdate",['arrWork'=>$work,'workType'=>$workType,'level'=>$arr,'datas'=>$data]);
        	}else{
            	return view("/ExamApply/applyshow");
        	}	

		}
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
		// print_r($result);die;
		$map = ['title','work_type','exam_time','work_id','work_level','exam_num','work_name'];
		$arrApply = array_columns($result,$map);
		$work = array_unique_key($result,"work_id");
		// print_r();die;
		$applyList = [''];
		foreach ($work as $k=>$v){
            $work[$k]['wdname'] = array_unique(array_column(array_where($arrApply,['work_id'=>$v['work_id'],]),'work_name'));
            $work_level = array_unique(array_column(array_where($arrApply,['work_id'=>$v['work_id'],]),'work_level'));
            sort($work_level);
            $work[$k]['level'] = $work_level;
    	}
    	return $work;
	}

		
}