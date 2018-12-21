<?php  

namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\ExamPlan;
use app\common\service\ExamEnrollTable;
use app\common\service\Jury;
use app\common\service\JuryReview;
use app\common\service\ExamStaffLog;
use think\request;

class AlloctionController extends Organizebase
{
	private $SExamPlan;
	private $SExamEnroll;
	private $SJury;
	private $SJuryReview;
	private $SExamStaffLog;

	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$this->SExamPlan   = new ExamPlan();
		$this->SExamEnroll = new ExamEnrollTable();
		$this->SJury       = new Jury();
		$this->SJuryReview = new JuryReview();
		$this->SExamStaffLog = new ExamStaffLog();
	}

	/**
	 * [index  考试人员分配]
	 * @return [type] [description]
	 */
	public function index()
	{
		$arrAdmin = session("organizeuser");
		$webData = input();
		if(!isset($webData['plan_id']))
		{
		   $where['ep.status'] = 1;
           $where['exam_time'] = array('gt',time());
           // $where['enroll_starttime'] = array('lt',time());
           $examplan = $this->SExamPlan->nowPlan($where,'ep.*,count(ee.id) as num');
           return view('allotplan',['examplan'=>$examplan,'type'=>'/organize/Alloction/index','title'=>'考评员']);
		}
		$arrWhere['exam_enroll.exam_plan_id'] = $webData['plan_id'];
		$arrWhere['exam_enroll.organize_id']  = $arrAdmin['id'];
		$info = $this->SExamEnroll->selectEnrollwork($arrWhere);
		$info = $this->datadispose($info);
		$data = $this->SJuryReview->BaseSelect(['exam_plan_id'=>$webData['plan_id'],'organize_id'=>$arrAdmin['id'],'status'=>['neq',1]],['work_id','level','count(level) as count'],'','','work_id,level');
		return view('index',['info'=>$info,'plan_id'=>$webData['plan_id'],'title'=>'考评员','data'=>$data]);
	}

	/**
	 * [allot 查询]
	 * @return [type] [description]
	 */
	public function allot()
	{
		$webData = input();
		$arrAdmin = session('organizeuser');
		$where['j.organize_id'] = $arrAdmin['id'];
		$where['jc.work_id'] = $webData['work'];
		if($webData['level'] >=3)
        {
            $where['jc.card_level'] = 00;
        }else{
            $where['jc.card_level'] = 01;
        }
		$jury_id = $this->SJuryReview->BaseSelect(['exam_plan_id'=>$webData['plan'],'organize_id'=>$arrAdmin['id'],'status'=>['neq',1]],['jury_id']);
		$jury_id = collection($jury_id)->toArray();
		$jury_id = array_column($jury_id,'jury_id');
        $where['jc.hire_time'] = array('lt',time());
		$where['jc.expire_date'] = array('gt',time());
		$where['jc.card_expire_time'] = array('gt',time());
        $where['jc.id'] = array('not in',$jury_id);
		$data = $this->SJury->allotExam($where);
		return view('allot',['data'=>$data,'webdata'=>$webData]);
	}

	/**
	 * [record 申请记录]
	 * @return [type] [description]
	 */
	public function record()
	{
		$webData = Request()->param();
		$arrAdmin = session('organizeuser');
		$where['work_id'] = $webData['work_id'];
		$where['level']   = $webData['level'];
		$where['exam_plan_id'] = $webData['exam_plan_id'];
		$where['jr.organize_id']  = $arrAdmin['id'];
		$field = "w.name as wname,level,jr.status,j.name as uname,id_number,jr.create_time";
		$info = $this->SJuryReview->selectAllotRecord($where,[$field]);
		return view('record',['data'=>$info]);

	} 

	/**
	 * [success 分配成功]
	 * @return [type] [description]
	 */
	public function allotTrue()
	{
		$webData = Request()->param();
		$arrAdmin = session('organizeuser');
		$where['exam_plan_id'] = $webData['plan'];
		$where['site_type']    = 2;
		$where['site_id']      = $arrAdmin['id'];
		$where['type']         = 3;
		$where['jc.work_id']   = $webData['work'];
		$field = "jc.name as uname,w.name as wname,jc.card_level,jc.card_no";
		$data = $this->SExamStaffLog->allotTrue($where,$field);
		return view('allottrue',['data'=>$data]);

	}

	/**
	 * 根据报考信息进行工种级别去重
	 */
	public function datadispose($data)
	{
		$arr = [];
		foreach ($data as $key => $val) 
		{
			$str = $val['wid'].$val->work_level_subject_level;
			if(!in_array($str,$arr))
			{
				$arr[] = $str;
			}else{
				unset($data[$key]);
			}
		}
		return $data;
	}

}