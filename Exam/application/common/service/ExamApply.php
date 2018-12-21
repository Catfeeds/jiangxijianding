<?php  

namespace app\common\service;

use app\common\model\ExamApply as MExamApply;
use app\common\model\ExamWork as MExamWork;
use app\common\model\ExamWorkLevel as MExamWorkLevel;
use app\common\model\Organize as MOrganize;
class ExamApply extends MExamApply
{
	private $SexamApply;
    private $SexamWork;
    private $SexamWorkLevel;
    private $SOrganize;
    public function __construct()
    {
        parent::__construct();
        $this->SexamApply = new MExamApply();
        $this->SexamWork = new MExamWork();
        $this->SexamWorkLevel = new MExamWorkLevel();
        $this->SOrganize = new MOrganize();
    }

    public function selectApply($paginate,$map,$field)
    {
    	$applyList = $this->SexamApply->BaseSelectPage($paginate,$map,$field,'id desc');
    	return $applyList;
    }

    /**
     * 添加专场申请
     * @throws \think\exception\PDOException
     */
	public function addApply($webData,$arrExamWork,$arrWorkLevel)
	{
        $this->startTrans();
		try{
			$apply_id = $this->SexamApply->BaseSave($webData);
			if(!$apply_id)
			{
                throw new \Exception('添加专场失败');
			}
			foreach ($arrExamWork as $key => $val) 
			{
				$arrExamWork[$key]['exam_id'] = $apply_id;
			}
			$ExamWork = $this->SexamWork->BaseSaveAll($arrExamWork,"",1);
	        $ExamWork = collection($ExamWork)->toArray();
	        if(!$ExamWork)
	        {
                throw new \Exception('添加工种失败');
	        }
	        foreach ($ExamWork as $key => $val) 
	        {
	        	foreach ($arrWorkLevel as $k => $v) 
	        	{
	        		$work = explode('_',$k);
	        		if($val['work_id']==$work[0])
	        		{
	        			$level = $v['work_level'];
		        		$arrWorkLevel[$val['work_id'].'_'.$level]['exam_work_id'] = $val['id'];
		        		$arrWorkLevel[$val['work_id'].'_'.$level]['alltype_id']   = $apply_id; 
	        		}

	        		
	        	}	
	        }

	        $ExamWorkLevel = $this->SexamWorkLevel->BaseSaveAll($arrWorkLevel);
	        
	        if(!$ExamWorkLevel)
	        {
                throw new \Exception('添加级别失败');
	        }
            $this->commit();

		}catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
		}
		

	}

	/**
	 * 获取申请详情
	 */
	public function getApplyDesc($arrWhere,$field)
	{
		$data = $this->SexamApply->join('__EXAM_WORK__','exam_apply.id=exam_work.exam_id','left')
					 ->join('__EXAM_WORK_LEVEL__','exam_apply.id=exam_work_level.alltype_id and exam_work.id=exam_work_level.exam_work_id','left')
					 ->join('__REVIEW_LOG__','review_log.reviewed_id=exam_apply.id','left')
					 ->where($arrWhere)
					 ->where("`exam_work`.`delete_time` IS NULL AND `exam_work_level`.`delete_time` IS NULL")
					 ->field($field)
					 ->select();

		return $data;
	}

    /**
     * @param $webData
     * @param $arrExamWork
     * @param $arrWorkLevel
     * @param $arrWhere
     * @return bool
     * @user 朱颖 2018/11/2~14:14
     * @throws \think\exception\PDOException
     */
	public function updateApply($webData,$arrExamWork,$arrWorkLevel,$arrWhere)
	{
	    //开启事务
        $this->startTrans();
		try{
			$apply_id = $this->SexamApply->BaseUpdate($webData,$arrWhere);
			if(!$apply_id)
			{
                throw new \Exception('修改失败');

            }
			$map['type'] = 1;
			$map['exam_id'] = $arrWhere['id'];
			$delWorkLog = $this->SexamWork->destroy($map);

            if ( !$delWorkLog )
            {
                throw new \Exception('修改失败');

            }

            $apply_id = $arrWhere['id'];

			foreach ($arrExamWork as $key => $val) 
			{
				$arrExamWork[$key]['exam_id'] = $apply_id;
			}
			$ExamWork = $this->SexamWork->BaseSaveAll($arrExamWork,"",1);
	        $ExamWork = collection($ExamWork)->toArray();
	        if(!$ExamWork)
	        {
                throw new \Exception('修改失败');

            }
	        $mapLevel['alltype_id'] = $arrWhere['id'];
            $mapLevel['type'] = 1;
            $delWorkLevelLog = $this->SexamWorkLevel->destroy($mapLevel);


            if ( !$delWorkLevelLog )
            {
                throw new \Exception('修改失败');

            }
	        foreach ($ExamWork as $key => $val) 
	        {
	        	foreach ($arrWorkLevel as $k => $v) 
	        	{
	        		$work = explode('_',$k);
	        		if($val['work_id']==$work[0])
	        		{
	        			$level = $v['work_level'];
		        		$arrWorkLevel[$val['work_id'].'_'.$level]['exam_work_id'] = $val['id'];
		        		$arrWorkLevel[$val['work_id'].'_'.$level]['alltype_id']   = $apply_id; 
	        		}	
	        	}	
	        }
//            print_r($arrWorkLevel);die;
	        $ExamWorkLevel = $this->SexamWorkLevel->BaseSaveAll($arrWorkLevel);
	        if(!$ExamWorkLevel)
	        {
                throw new \Exception('修改失败');

            }
            $this->commit();

		}catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
		}
		
	}

	public function applyput($arrWhere,$content)
	{
		$apply_id = $this->SexamApply->BaseUpdate($content,$arrWhere);
		if($apply_id)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	 * 删除申请的工种和级别表
	 * @param  [type] $data  [description]
	 * @param  [type] $table [description]
	 * @return [type]        [description]
	 */
	public function deletes($webData)
	{
		$delObj = $this->SexamApply->destroy($webData);
		return $delObj;
	}

	/**
	 * [selbyworktype 查询机构的工种权限]
	 * @return [type] [description]
	 */
	public function selbyworktype($webData)
	{
        $arrAdmin = session("organizeuser");
        $arrWorkType['organize.id'] = $arrAdmin['id'];
        switch ($arrAdmin['type']){
                case
                    1:
                    $arrWorkType['exam_work.type'] = 6;  //鉴定所
                    $arrWorkType['exam_work_level.type'] = 6;  //鉴定所
                break;
                case
                    2:
                    $arrWorkType['exam_work.type'] = 7;  //院校
                    $arrWorkType['exam_work_level.type'] = 7;  //院校
                break;
                case
                    3:
                    $arrWorkType['exam_work.type'] = 4;  //机构
                    $arrWorkType['exam_work_level.type'] = 4;  //机构
                break;
            }
        $arrWorkType['work_type.id'] = $webData['work_type'];
        $arrWorkType['organize.status'] = 1;
        $data = $this->datas($arrWorkType);
        return $data;
	}

	 /**
     * [data description]
     * @return [type] [description]
     */
    public function datas($arrWorkType)
    {
    	$field = "work_type.`name` as wtname,`work`.name as wname,work_direction.`name` as wdname,exam_work_level.work_level as wllevel,work.id as wid";
    	$data = $this->SOrganize
            ->join("__EXAM_WORK__","organize.id=`exam_work`.exam_id","left")
            ->join("__EXAM_WORK_LEVEL__","organize.id = exam_work_level.alltype_id AND exam_work_level.exam_work_id = exam_work.id","left")
            ->join("__WORK_TYPE__","exam_work.work_type = work_type.id","left")
            ->join("__WORK__","exam_work.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","work_direction.work_id = `work`.id","left")
            ->where($arrWorkType)
            ->field($field)
            ->where("exam_work.delete_time is null and exam_work_level.delete_time is null and work_type.delete_time is null and work.delete_time is null and work_direction.delete_time is null")
            ->select();
        $data = collection($data)->toArray();
        $arr = [];
        foreach ($data as $key => $val) 
        {
        	if(!array_key_exists($val['wid'],$arr))
        	{
        		$arr[$val['wid']]['wname'] = $val['wname'];
        		$arr[$val['wid']]['level'][] = $val['wllevel'];
        		$arr[$val['wid']]['wid'] = $val['wid'];
        	}
        	else
        	{
        		if(!in_array($val['wllevel'],$arr[$val['wid']]['level']))
        		{
        			$arr[$val['wid']]['level'][] = $val['wllevel'];
        		}
        	}

        }
        $data = [];
        foreach ($arr as $key => $value) {
        	$data[] = $value;
        }
        return $data;
    }


}
