<?php
namespace app\common\service;

use app\common\model\ExamPlan as MExamPlan;
use app\common\model\ExamWork as MExamWork;
use app\common\model\ExamWorkLevel as MExamWorkLevel;
class ExamPlanWork extends MExamPlan
{
    private $SexamPlan;
    private $SexamWork;
    private $SexamWorkLevel;

    public function __construct()
    {
        parent::__construct();
        $this->SexamPlan = new MExamPlan();
        $this->SexamWork = new MExamWork();
        $this->SexamWorkLevel = new MExamWorkLevel();
    }


    /**
     * @param array $map
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDatabyid($map=[],$field=''){
        return $data = $this->SexamPlan
            ->join("__WORK_TYPE__","exam_plan.work_type=work_type.id")
            ->join("__WORK__","work.work_type_id=work_type.id")
            ->field($field)
            ->where($map)
            ->select();
    }

    /**
     * @param array $map
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getDataJoin($map = []){
        //$field="exam_enroll.id,exam_enroll.work_level_subject_level,exam_enroll.user_login_id,exam_plan.title,`work`.name as workname,work_direction.name as directionname,work_level_subject.`level`,exam_enroll.exam_type,userinfo.current_level,exam_enroll.organize_id,exam_enroll.source,exam_enroll.create_time,exam_enroll.update_time,exam_enroll.status,exam_enroll.id,work.id as wid,work_direction.id as did";
        $data= $this->SexamPlan
            ->join("__USERINFO__","userinfo.user_login_id=exam_enroll.user_login_id","left")
            ->join("__WORK__","exam_enroll.work_id=`work`.id")
            ->join("__WORK_DIRECTION__","exam_enroll.work_direction_id=work_direction.id")
            ->join("__WORK_LEVEL_SUBJECT__","work_level_subject.work_id=`work`.id")
            //->field($field)
            ->where($map)
            ->group("exam_enroll.id")
            ->paginate();
        return $data;
    }


    /**
     * 添加鉴定计划
     * @param array $webData
     * @param array $arrExamWork
     * @param array $new
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function addTable($webData = [],$arrExamWork = [],$new = [])
    {
        // 启动事务
        $this->startTrans();
        try {
            $exam_id = $this->SexamPlan->BaseSave($webData);

            if (!$exam_id){
                throw new \Exception('添加失败');
            }
            foreach ($arrExamWork as $k=>$v){
                $arrExamWork[$k]['exam_id'] = $exam_id;
            }
            $ExamWork = $this->SexamWork->BaseSaveAll($arrExamWork,"",1);
            $ExamWork = collection($ExamWork)->toArray();

            foreach ($new as $key=>$val)
            {
                foreach ($ExamWork as $k=>$v)
                {
                    if ($val['exam_work_id'] == $v['work_id']){
                        $new[$key]['exam_work_id'] = $v['id'];
                        $new[$key]['alltype_id'] = $exam_id;
                    }
                }
            }
//            print_r($new);die;

            if (!$ExamWork){
                throw new \Exception('添加失败');
            }

            if (!empty($new))
            {
                $ExamWorkLevel = $this->SexamWorkLevel->BaseSaveAll($new);
                if (!$ExamWorkLevel){
                    throw new \Exception('添加失败');
                }
            }
            // 提交事务
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return $e->getMessage();
        }
    }


    /**
     * 修改鉴定计划
     * @param array $where
     * @param array $webData
     * @param array $arrExamWork
     * @param array $new
     * @param int $type
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function updTable($where=[] ,$webData = [],$arrExamWork = [] ,$new = [] ,$type = 0)
    {
        // 启动事务
        $this->startTrans();
        try {

            $examplan = $this->SexamPlan->BaseUpdate($webData,$where);

            if ( !$examplan )
            {
                throw new \Exception('修改失败');

            }

            $map['exam_id'] = $where['id'];
            $map['type'] = $type;
            $delWorkLog = $this->SexamWork->destroy($map);

            if ( !$delWorkLog )
            {
                throw new \Exception('修改失败');

            }
//            $ExamWork = $this->SexamWork->BaseSaveAll($arrExamWork);
            $ExamWork = $this->SexamWork->BaseSaveAll($arrExamWork,"",1);
            $ExamWork = collection($ExamWork)->toArray();


            if ( !$ExamWork )
            {
                throw new \Exception('修改失败');

            }

            $mapLevel['alltype_id'] = $where['id'];
            $mapLevel['type'] = $type;
            $delWorkLevelLog = $this->SexamWorkLevel->destroy($mapLevel);


            if ( !$delWorkLevelLog )
            {
                throw new \Exception('修改失败');

            }
            foreach ($new as $key=>$val)
            {
                foreach ($ExamWork as $k=>$v)
                {
                    if ($val['exam_work_id'] == $v['work_id']){
                        $new[$key]['exam_work_id'] = $v['id'];
                        $new[$key]['alltype_id'] = $where['id'];
                    }
                }
            }

            $ExamWorkLevel = $this->SexamWorkLevel->BaseSaveAll($new);
            if (!$ExamWorkLevel){
                throw new \Exception('修改失败');

            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return $e->getMessage();
        }
    }
    public function updExamWork($exam_id, $workArr = [] , $workLevelArr = [] ,$work_type,$work_type_name, $type = 5)
    {
        // 启动事务
        $this->startTrans();
        try {
            $this->SexamPlan->BaseUpdate(["work_type"=>$work_type,"work_type_name"=>$work_type_name],["id"=>$exam_id]);
            //清空当前工种
            $delWorkLog = $this->SexamWork->destroy(['exam_id'=>$exam_id,'type'=> $type]);
            $workAdded = $this->SexamWork->BaseSaveAll($workArr,"",1);
            $workAdded = collection($workAdded)->toArray();

            $delWorkLevelLog = $this->SexamWorkLevel->destroy(['alltype_id'=>$exam_id,'type'=> $type]);
            if(count($workLevelArr)>0) {
                foreach ($workLevelArr as $key => $val) {
                    foreach ($workAdded as $k => $v) {
                        if ($val['work_id'] == $v['work_id']) {
                            $workLevelArr[$key]['exam_work_id'] = $v['id'];
                            unset($workLevelArr[$key]['work_id']);
                        }
                    }
                }
                $ExamWorkLevel = $this->SexamWorkLevel->BaseSaveAll($workLevelArr);
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            throw $e;
        }
    }


    public function deletes($data=[] , $table = "")
    {
        if ($table == 'examwork'){
            return $this->SexamWork->destroy($data);
        }else if ($table == 'examworklevel'){
            return $this->SexamWorkLevel->destroy($data);
        }
    }

    /**
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function joinAdmin($where = [])
    {
        return $this->SexamPlan
            ->join("__EXAM_ENROLL__","exam_plan.id=exam_enroll.exam_plan_id")
            ->join("__ADMIN__","exam_plan.create_id=admin.id")
            ->join("__EXAM_CENTER__","exam_center.id=admin.exam_center_id")
            ->field("exam_center.*,exam_plan.id as eid,admin.id as uid")
            ->where($where)
            ->find();
    }

}