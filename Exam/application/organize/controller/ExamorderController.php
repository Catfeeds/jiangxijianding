<?php  

namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\ExamPlan;
use app\common\service\ExamOrder;
use think\request;

class ExamorderController extends Organizebase
{
	private $SExamPlan;
	private $SExamOrder;

	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$this->SExamPlan   = new ExamPlan();
		$this->SExamOrder  = new ExamOrder();
	}

	/**
	 * [index  考试人员分配]
	 * @return [type] [description]
	 */
	public function index()
	{
		$arrAdmin = session("organizeuser");
		$arrWhere['type_id'] = $arrAdmin['id'];
		$arrWhere['type']    = $arrAdmin['type'];
		$webData = input();
		if(!isset($webData['plan_id']))
		{
			$where['ep.status'] = 1;
            $where['pay_endtime'] = array('gt',time());
            $where['enroll_starttime'] = array('lt',time());
            $field = "ep.id,ep.title,ep.pay_endtime,ep.exam_time,count(eo.id) as num";
            $examplan = $this->SExamPlan->orderPlan($where,$field);
            return view('payplan',['examplan'=>$examplan,'type'=>'/organize/Examorder/index','title'=>'缴费审核记录']);
		}
		$arrWhere['exam_plan_id'] = $webData['plan_id'];
		$info = $this->SExamOrder->BaseSelect($arrWhere);
		return view('index',['order'=>$info,'plan_id'=>$webData['plan_id'],'title'=>'缴费审核记录']);
	}

	 /**
     * [reviewrecord 缓缴费审核记录]
     * @return [type] [description]
     */
    public function reviewrecord()
    {
        $plan_id = input('order_id');
        $arrAdmin = session("organizeuser");
        $arrWhere['eo.id'] = $plan_id;
        $arrWhere['eo.type']         = $arrAdmin['type'];
        $arrWhere['eo.type_id']      = $arrAdmin['id'];
        $arrWhere['rl.reviewed_type']    = array('in',[5,6]);
        $field = 'o.username,eo.total_money,rl.reason,a.name,rl.create_time,eo.remark,rl.status as rlstatus,rl.reviewed_type';
        $data = $this->SExamOrder->record($arrWhere,$field);
        return view('reviewrecord',['data'=>$data,'plan_id'=>$plan_id]);

    }

   

}