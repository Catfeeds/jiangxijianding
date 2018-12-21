<?php  

namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\ExamEnroll;
use app\common\service\ExamPlan;
use app\common\service\Grade;
use app\common\service\ExamOrder;
use think\request;

class GradeController extends Organizebase
{
	private $SExamEnroll;
	private $SExamPlan;
	private $SGrade;
	private $SExamOrder;

	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$this->SExamEnroll = new ExamEnroll();
		$this->SExamPlan   = new ExamPlan();
		$this->SGrade      = new Grade();
		$this->SExamOrder  = new ExamOrder();
	}

	/**
	 * [passList 考试通过列表]
	 * @return [type] [description]
	 */
	public function index()
	{
		// echo 123;die;
		$webData = input();
		$arrAdmin = session("organizeuser");
		$arrWhere['ee.organize_id'] = $arrAdmin['id'];
		$arrWhere['ee.status']      = config('ExamEnrollStatus.printticket');
		$examGrade = [];
		$where = [];
		$field = "grade.TicketNum,grade.work_name,grade.level,ee.type,ul.name as username,if(theory !=0,grade.theory_score_result,'无此科目') as theory_score_result,if(operation !=0,grade.watch_score_result,'无此科目') as watch_score_result,if(comprehen !=0,grade.synthesize_score_result,'无此科目') as synthesize_score_result";
		if(!isset($webData['plan_id']))
        {
           
           $column = "ep.id,ep.title,ep.exam_time,sum(if(result=1,1,0)) as pass,sum(if(result=0,1,0)) as nopass,count(g.id) as num";
           $where['ep.status'] = 1;
           $where['ep.exam_time'] = array('lt',time());
           $examplan = $this->SExamPlan->gradePlan($where,$column);
           return view('gradeplan',['examplan'=>$examplan,'type'=>'/organize/Grade/index','title'=>'成绩查询']);
        }
		$arrWhere['grade.exam_plan_id'] = $webData['plan_id'];
		if(isset($webData['ticketnum']) && trim($webData['ticketnum']) != '')
		{
			$arrWhere['grade.TicketNum'] = trim($webData['ticketnum']);
		}
		if(isset($webData['id_card']) && trim($webData['id_card']) != '')
		{
			$arrWhere['grade.id_card'] = trim($webData['id_card']);
		}
		if(isset($webData['result']) && $webData['result'] != '')
		{
			if($webData['result']==1)
			{
				$where = "(theory_score_result=1 and watch_score_result=1 and (level=3 or level=4 or level=5)) or (theory_score_result=1 and watch_score_result=1 and synthesize_score_result=1 and (level=1 or level=2))";
											
			}else{
				$arrWhere =array_merge($arrWhere,['theory_score_result|watch_score_result|synthesize_score_result'=>$webData['result']]) ;
			}
		}else{
			$webData['result'] = '';
		}

		$result = config('EnrollStatusText.graderesult');
		$examGrade = $this->SGrade->selectExamGrade($arrWhere,$field,$where);
		return view('index',['examgrade'=>$examGrade,'map'=>$webData,'result'=>$result,'title'=>'成绩查询']);
		
	}

	public function exportGrade()
	{
		$plan_id = Request()->get('plan_id');
		$arrAdmin = session("organizeuser");
		$arrWhere['ee.organize_id'] = $arrAdmin['id'];
		$arrWhere['ee.status']      = config('ExamEnrollStatus.printticket');
		$arrWhere['grade.exam_plan_id'] = $plan_id;
        $where = "(theory_score_result=1 and watch_score_result=1 and (level=3 or level=4 or level=5)) or (theory_score_result=1 and watch_score_result=1 and synthesize_score_result=1 and (level=1 or level=2))";
        $field = "grade.TicketNum,grade.work_name,grade.level,ee.type,ul.name as username,if(theory !=0,grade.theory_score_result,'无此科目') as theory_score_result,if(operation !=0,grade.watch_score_result,'无此科目') as watch_score_result,if(comprehen !=0,grade.synthesize_score_result,'无此科目') as synthesize_score_result";
		$title = ['姓名','准考证号','证件号','报考工种','报考级别','理论结果','实操结果','综合结果','最终结果'];
		$column = ['username','TicketNum','id_card','work_name','level','theory_score','theory_score_result','watch_score','watch_score_result','synthesize_score','synthesize_score_result','result'];
		$data = $this->SGrade->selectExamGrade($arrWhere,$field,$where);
		foreach ($data as $key => $val) 
		{
			if($val['theory_score_result']==1 && $val['watch_score_result']==1 && $val['synthesize_score_result'])
			{
				$data[$key]['result'] = '通过';
			}else{
				$data[$key]['result'] = '不通过';
			}
		}
		$plan = $this->SExamPlan->BaseFind(['id'=>$plan_id],['title']);
		$filename = $plan['title'].'成绩'.'.xls';
		$this->SGrade->Excel($title,$data,$column,$filename);exit;

	}

	 /**
     * [exportRecord 导出审核记录]
     * @return [type] [description]
     */
    public function exportRecord()
    {
        $plan = input('plan');
        $arrAdmin = session("organizeuser");
        $arrWhere['eo.id'] = $plan;
        $arrWhere['eo.type']         = $arrAdmin['type'];
        $arrWhere['eo.type_id']      = $arrAdmin['id'];
        $arrWhere['rl.reviewed_type']    = 5;
        $field = 'o.username,eo.total_money,rl.reason,a.name as names,rl.create_time,eo.remark,rl.status as rlstatus';
        $title = ['审核人','申请金额','申请原因','审核人','审核时间','审核进度','不通过原因'];
        $column = ['username','total_money','remark','names','create_time','rlstatus','reason'];
        $data = $this->SExamOrder->record($arrWhere,$field);
        // $data = collection($data)->toArray();
        $plan = $this->SExamPlan->BaseFind(['id'=>$plan],['title']);
        $filename = $plan['title'].'计划缓缴费审核记录'.'.xls';
        $this->SGrade->Excel($title,$data,$column,$filename);exit;       
    }

}