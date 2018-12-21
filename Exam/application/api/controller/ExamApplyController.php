<?php
namespace app\api\controller;
use app\common\service\ExamApply;
use app\common\service\ExamPlanWork;
use app\common\service\WorkType;
use app\common\service\Work;
use app\common\service\ReviewLog;
use app\common\controller\BaseApi;
use think\Request;
							
class ExamApplyController extends BaseApi
{

	private $SexamPlanWork;
	private $SexamApply;
	private $WorkType;
	private $Work;
	private $SreviewLog;
	private $SOrganizeWork;

	public function __construct()
	{
		parent::__construct();
		$this->SexamPlanWork = new ExamPlanWork();
		$this->SexamApply = new ExamApply();
		$this->WorkType = new WorkType();
		$this->Work = new Work();
		$this->SreviewLog = new ReviewLog();

	}
	public function add()
	{
		if (!Request::instance()->isPost()){
			return layuiMsg('-1', '非法操作');
		}
		$webData = Request::instance()->param();
		if (!$webData){
			return layuiMsg('-1', '频繁操作');
		}
		$validata = Validate('app\organize\validate\ExamApply');

		//场景应用
		if (!$validata->scene('addexamapply')->check($webData)) {
			return layuiMsg(-1, $validata->getError());
		}
		$file = Request::instance()->file('pdf');
		$info = $file->validate(['size'=>3145728,'ext'=>'pdf'])->move(ROOT_PATH . 'public' . DS . 'uploads'.DS.'apply');
		if($info){
			$webData['appendix'] = '/uploads/apply'.DS.$info->getSaveName();

		}else{
			return layuiMsg('-1',$file->getError());
		}
		$arrAdmin = session("organizeuser");

		$work_type = $this->WorkType->BaseFind(['id'=>$webData['work_type']]);
		$arrExamWork = [];
		$arrWorkLevel = [];
		foreach ($webData['work'] as $val) 
		{
			if(array_key_exists($val,$webData['level']))
			{
				$arr = [];
				$arr = $this->Work->BaseFind(['id'=>$val]);
				$arrExamWork[$val]['work_id']     = $val;
				$arrExamWork[$val]['work_name']   = $arr->name;
				$arrExamWork[$val]['work_type']   = $webData['work_type'];
				$arrExamWork[$val]['status']      = 1;
				$arrExamWork[$val]['type']        = 1;
				$arrExamWork[$val]['create_time'] = time();
				$arrExamWork[$val]['update_time'] = time();
			}
			
		}
		unset($webData['work']);
		foreach ($webData['level'] as $key => $val) 
		{
			foreach ($val as $k =>  $v) 
			{

				$arrWorkLevel[$key.'_'.$v]['work_level'] = $v;
				$arrWorkLevel[$key.'_'.$v]['type']       = 1;
				$arrWorkLevel[$key.'_'.$v]['create_time']= time();
				$arrWorkLevel[$key.'_'.$v]['update_time']= time();
				
			}
		}
		unset($webData['level']);
		$webData['organize_id'] = $arrAdmin['id'];
		$webData['create_time'] = time();
		$webData['update_time'] = time();
		$result = $this->SexamApply->addApply($webData,$arrExamWork,$arrWorkLevel);
		if($result===false)
		{
			return layuiMsg('-1', '添加失败');
		}
		else{
			return layuiMsg('1', '添加成功,请确认后提交');
		}
	}

	public function delete()
	{
		if (!Request::instance()->isPost()){
			return layuiMsg('-1', '非法操作');
		}
		$webData = Request::instance()->post();
		if (!$webData){
			return layuiMsg('-1', '频繁操作');
		}
		$webData['id'] = (int)$webData['id'];
		$objDel = $this->SexamApply->deletes($webData);
		//删除另一张表
		$examwork['exam_id'] = $webData['id'];
		$examwork['type'] = 1;
		$objExamWork = $this->SexamPlanWork->deletes($examwork,'examwork');
		$examworklevel['alltype_id'] = $webData['id'];
		$examworklevel['type'] = 1;

		$objExamWorkLevel = $this->SexamPlanWork->deletes($examworklevel,'examworklevel');

		if ($objDel && $objExamWork && $objExamWorkLevel){
			return layuiMsg(1, "删除成功");
		}else{
			return layuiMsg(-1, "删除失败");
		}
	}

	public function update()
	{
		if (!Request::instance()->isPost()){
			return layuiMsg('-1', '非法操作');
		}
		$webData = Request::instance()->param();
		if (!$webData){
			return layuiMsg('-1', '频繁操作');
		}
		$validata = Validate('app\organize\validate\ExamApply');
		//场景应用
		if (!$validata->scene('addexamapply')->check($webData)) {
			return layuiMsg(-1, $validata);
		}
		$file = Request::instance()->file('pdf');
		if(!empty($file)){
			$info = $file->validate(['size'=>3145728,'ext'=>'pdf'])->move(ROOT_PATH . 'public' . DS . 'uploads'.DS.'apply');
			if($info){
				$webData['appendix'] = '/uploads/apply'.DS.$info->getSaveName();
			}
		}
		
		$arrAdmin = session("organizeuser");
		$arrWhere['id'] = $webData['id'];
		unset($webData['id']);
		$work_type = $this->WorkType->BaseFind(['id'=>$webData['work_type']]);
		$arrExamWork = [];
		$arrWorkLevel = [];
		foreach ($webData['work'] as $val) 
		{
			if(array_key_exists($val,$webData['level']))
			{
				$arr = [];
				$arr = $this->Work->BaseFind(['id'=>$val]);
				$arrExamWork[$val]['work_id']     = $val;
				$arrExamWork[$val]['work_name']   = $arr->name;
				$arrExamWork[$val]['work_type']   = $webData['work_type'];
				$arrExamWork[$val]['status']      = 1;
				$arrExamWork[$val]['type']        = 1;
				$arrExamWork[$val]['create_time'] = time();
				$arrExamWork[$val]['update_time'] = time();
			}	
		}
		unset($webData['work']);
		foreach ($webData['level'] as $key => $val) 
		{
			foreach ($val as  $v) 
			{
				$arrWorkLevel[$key.'_'.$v]['work_level'] = $v;
				$arrWorkLevel[$key.'_'.$v]['type']       = 1;
				$arrWorkLevel[$key.'_'.$v]['create_time']= time();
				$arrWorkLevel[$key.'_'.$v]['update_time']= time();
			}
		}
		unset($webData['level']);
		$webData['organize_id'] = $arrAdmin['id'];
		$webData['create_time'] = time();
		$webData['update_time'] = time();
		$result = $this->SexamApply->updateApply($webData,$arrExamWork,$arrWorkLevel,$arrWhere);
		if($result===false)
		{
			return layuiMsg('-1', '修改失败');
		}
		else{
			return layuiMsg('1', '修改成功');
		}
	}

	/**
	 * [selbyworktype 查询机构的工种权限]
	 * @return [type] [description]
	 */
	public function selbyworktype()
	{
		$webData = Request::instance()->param();
        if (!$webData['work_type']){
            return layuiMsg('-1', '非法操作');
        }
        $data = $this->SexamApply->selbyworktype($webData);
        // print_r($data);die;
        if ($data){
            return layuiMsg('1', '获取成功',$data);
        }else{
            return layuiMsg('-1', '此类型下暂无工种');
        }
	}

	/**
	 * 申请提交
	 * @return [type] [description]
	 */
	public function applyput()
	{
		if (!Request::instance()->isPost()){
			return layuiMsg('-1', '非法操作');
		}
		$arrWhere = Request::instance()->post();
		if (!$arrWhere){
			return layuiMsg('-1', '频繁操作');
		}

		$content['status'] = 1;
		$result = $this->SexamApply->applyput($arrWhere,$content); 
		if($result===false)
		{
			return layuiMsg('-1', '提交失败');
		}
		else{
			return layuiMsg('1', '提交成功');
		}
	}

    /**
     * 审核专场考试
     * @return array
     * @user 朱颖 {2018/10/26}~{10:19}
     */
	public function review()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $arrWhere = Request::instance()->post();
        if (!$arrWhere || empty($arrWhere['id']) || empty($arrWhere['listStatus'])){
            return layuiMsg('-1', '频繁操作');
        }
        //第一次审核
        if ($arrWhere['listStatus'] == 1)
        {
            $arrData['status'] = 2;
        //第二次审核
        }else if ($arrWhere['listStatus'] == 2)
        {
            $arrData['status'] = 3;
        }
        //不通过
        if (!empty($arrWhere['no']))
        {
            if ($arrWhere['listStatus'] == 1)
            {
                $arrData['status'] = 4;
            }else if ($arrWhere['listStatus'] == 2)
            {
                $arrData['status'] = 5;
            }
        }

        $Log = $this->SreviewLog->BaseFind(['reviewed_id'=>$arrWhere['id'],'reviewed_type'=>3,'status'=>$arrData['status']]);

        if ($Log)
        {
            return layuiMsg('-1', '频繁操作');
        }

        $reviewApply = $this->SexamApply->BaseUpdate($arrData,['id'=>$arrWhere['id']]);
        if ($reviewApply)
        {
            $adminuser = session('adminuser');
            $arrData['review_id'] = $adminuser['id'];
            $arrData['review_type'] = 1;    //鉴定中心
            $arrData['review_time'] = time();
            $arrData['reviewed_type'] = 3;  //专场申请
            $arrData['reviewed_id'] = $arrWhere['id'];
            $arrData['review_ip'] = getip();
            $arrData['create_time'] = time();
            $arrData['update_time'] = time();

            if (!empty($arrWhere['reason']) || isset($arrWhere['pass_reason'])){
                $arrData['reason'] = isset($arrData['reason'])?trim($arrData['reason']):'';
                $arrData['pass_reason'] = isset($arrWhere['pass_reason'])?implode(',',$arrWhere['pass_reason']):'';
            }
//            print_r($arrData);die;
            $applyLog = $this->SreviewLog->BaseSave($arrData);
            if ($applyLog)
            {
                return layuiMsg('1', '审核成功');
            }else{
                return layuiMsg('-1', '审核失败');
            }
        }else{
            return layuiMsg('-1', '审核失败');
        }

    }


}