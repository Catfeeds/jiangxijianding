<?php  

namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\ExamPlan;
use app\common\service\Certificate;
use app\common\service\Organize;
use app\common\service\Grade;
use app\common\service\ExamCenter;
use think\request;

class CertificateController extends Organizebase
{
	private $SExamPlan;
	private $SCertificate;
	private $SOrganize;
	private $SGrade;
	private $SExamCenter;

	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$this->SExamPlan   = new ExamPlan();
		$this->SCertificate= new Certificate();
		$this->SOrganize   = new Organize();
		$this->SGrade      = new Grade();
		$this->SExamCenter = new ExamCenter();
	}

	/**
	 * [passList 考试通过列表]
	 * @return [type] [description]
	 */
	public function passList()
	{
		$arrAdmin = session("organizeuser");
		$arrWhere['c.source'] = $arrAdmin['id'];
		$webData = input();
		if(!isset($webData['plan_id']))
		{
		    $where['eo.type']  = $arrAdmin['type'];
		    $where['eo.type_id'] = $arrAdmin['id'];
		    $where['ep.status'] = 1;
            $where['exam_time'] = array('lt',time());
            $where['eo.pay_state'] = array('in',[config('ExamOrderStatus.deferpay'),config('ExamOrderStatus.paysuccess')]);
            // $where['enroll_starttime'] = array('lt',time());
            $field = 'ep.*,count(c.id) as pass,cw.order,cw.cert_get_way,is_take,order_num,eo.id as eoid';
            $examplan = $this->SExamPlan->certPlan($where,$field,'exam_time desc');
            return view('examplan',['examplan'=>$examplan,'type'=>'/organize/Certificate/passList','title'=>'证书申领']);
		}
		$field = "c.*";
		$apply    = $this->SCertificate->selectExamGrade(['order_id'=>$webData['plan_id']],$field);
		return view('passList',['plan_id'=>$webData['plan_id'],'apply'=>$apply,'title'=>'证书申领']);
	}

	public function receiveType()
    {
        $data['cert'] = input('cert');
        $data['plan'] = input('plan');

        $arrAdmin = session("organizeuser");
        $info = $this->SOrganize->BaseFind(['id'=>$arrAdmin['id']],['detail_address','phone','subordinate_admin','name']);
        $center = $this->SExamCenter->BaseFind(['id'=>$info['subordinate_admin']],['name','address']);
        return view('receiveType',['cert'=>$data,'info'=>$info,'center'=>$center]);
    }

	public function detail()
	{
		$id = input('id');
		$detail = $this->SCertificate->BaseFind(['id'=>$id]);
		return view('detail',['detail'=>$detail]);
	}

	public function export()
	{
		$webData = input();
		$field = "c.username,c.certificate_no";
		$title = ['姓名','证书编号'];
		$apply    = $this->SCertificate->selectExamGrade(['order_id'=>$webData['plan_id']]);
		$column = ['username','certificate_no'];
		$filename = '证书编号'.'.xls';
		$this->SGrade->Excel($title,$apply,$column,$filename);exit;
	}

}