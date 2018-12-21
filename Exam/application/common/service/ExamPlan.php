<?php
namespace app\common\service;

use app\common\model\ExamPlan as MExamPlan;
use think\Db;
class ExamPlan extends MExamPlan
{

    /**
     * @param array $map
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDatabyid($map=[],$field=''){
        return $data = $this
            ->join("__WORK_TYPE__","exam_plan.work_type=work_type.id")
            ->join("__WORK__","work.work_type_id=work_type.id")
            ->field($field)
            ->where($map)
            ->select();
    }

    public function getJury($paginate, $param = [], $field = [], $order = '', $group = '')
    {
        $join = array(
            ["__JURY_REVIEW__","exam_plan.id=jury_review.exam_plan_id","left"],
        );
//        print_r($field);die;
        $data = $this->BaseJoinSelectPage($paginate,'exam_plan', $join, $param, $field, $order, $group);
        return $data;
    }

    public function getDataWithEnroll($paginate, $param = [], $field = [], $order = '', $group = '')
    {
        $join = array(
            ["__EXAM_ENROLL__","exam_plan.id=exam_enroll.exam_plan_id AND exam_enroll.organize_id=0","left"],
        );
//        print_r($field);die;
        $data = $this->BaseJoinSelectPage($paginate,'exam_plan', $join, $param, $field, $order, $group);
        return $data;
    }
    public function getOrganizeWithEnroll($paginate, $param = [], $field = [], $order = '', $group = '')
    {
        $join = array(
            ["__EXAM_ENROLL__","exam_plan.id=exam_enroll.exam_plan_id AND exam_enroll.organize_id>0","left"],
        );
//        print_r($field);die;
        $data = $this->BaseJoinSelectPage($paginate,'exam_plan', $join, $param, $field, $order, $group);
        return $data;
    }

    /**
     * @param array $map
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getDataJoin($map = []){
        //$field="exam_enroll.id,exam_enroll.work_level_subject_level,exam_enroll.user_login_id,exam_plan.title,`work`.name as workname,work_direction.name as directionname,work_level_subject.`level`,exam_enroll.exam_type,userinfo.current_level,exam_enroll.organize_id,exam_enroll.source,exam_enroll.create_time,exam_enroll.update_time,exam_enroll.status,exam_enroll.id,work.id as wid,work_direction.id as did";
        $data= $this
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
     * 根据计划获取级别 方向 工种 大类
     * @param array $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListData($map = [], $field = [], $order = '',$paginate=[])
    {
        $page = "select";
        $data = NULL;
        if (!empty($paginate)){
            list($listRows, $simple) = $paginate;
            $page = 'paginate';
            $data = $listRows.",". $simple;
        }
//        print_r($config);die;
        return $this
            ->join("__EXAM_WORK__","exam_plan.id=exam_work.exam_id AND `exam_work`.`type` = 5","left")
            ->join("__WORK_TYPE__","work_type.id=exam_plan.work_type","left")
            ->join("__WORK__","`work`.id=exam_work.work_id","left")
            ->join("__WORK_DIRECTION__","work_direction.work_id=`work`.id","left")
            ->join("__EXAM_WORK_LEVEL__","`exam_work_level`.`alltype_id` = `exam_plan`.`id` AND exam_work_level.exam_work_id = exam_work.id AND exam_work_level.type = exam_work.type AND `exam_work_level`.`type` = 5","left")
            ->where($map)
            ->where("`exam_work`.`delete_time` IS NULL AND `work_type`.`delete_time` IS NULL AND `work`.`delete_time` IS NULL AND `work_direction`.`delete_time` IS NULL AND `exam_work_level`.`delete_time` IS NULL")
            ->field($field)
            ->order($order)
            ->$page($data);
    }


    public function getAllWorkDirectionLevel($map = [], $field = [], $order = '')
    {
        return $this
            ->join("__EXAM_WORK__","`exam_plan`.`id` = `exam_work`.`exam_id`",'left')
            ->join("__WORK__","`work`.id = `exam_work`.`work_id`",'left')
            ->join("__WORK_DIRECTION__","`work_direction`.`work_id`",'left')
            ->join("__EXAM_WORK_LEVEL__","`exam_work_level`.`alltype_id` = `exam_plan`.`id`AND exam_work_level.exam_work_id = exam_work.id AND exam_work_level.type = exam_work.type",'left')
            ->where($map)
            ->where("`exam_work`.`delete_time` IS NULL AND `work`.`delete_time` IS NULL AND `work_direction`.`delete_time` IS NULL AND `exam_work_level`.`delete_time` IS NULL")
            ->field($field)
            ->order($order)
            ->group('work_id,work_direction_id,work_level')
            ->select()
            ;
    }

    /**
     * 鉴定计划对应的工种级别方向等
     * @param array $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wllevel($map=[]){
//        print_r($map);die;
        $field = "exam_plan.id AS exam_plan_id,exam_plan.title,work_type.`name` AS wtname,exam_plan.work_type,exam_work.id AS exam_work_id,`work`.`name` AS wname,`work`.id AS work_id,exam_work_level.work_level AS wllevel,exam_work_level.id AS work_level_id,work_direction.id AS wdid,work_direction.`name` AS wdname,exam_plan.enroll_starttime,exam_plan.enroll_endtime,exam_center.`name`";
        $where = " exam_work.type=5 AND exam_work.delete_time is NULL AND exam_plan.delete_time IS NULL AND exam_work_level.delete_time IS NULL AND `work`.delete_time IS NULL AND work_type.delete_time IS NULL AND work_direction.delete_time IS NULL AND admin.delete_time IS NULL AND exam_center.delete_time IS NULL";
        return $data = $this
            ->join("__EXAM_WORK__","exam_plan.id=exam_work.exam_id")
            ->join("__EXAM_WORK_LEVEL__","exam_plan.id=exam_work_level.alltype_id AND exam_work_level.exam_work_id=exam_work.id")
            ->join("__WORK__","exam_work.work_id=`work`.id")
            ->join("__WORK_TYPE__","exam_plan.work_type = work_type.id")
            ->join("__WORK_DIRECTION__","work_direction.work_id = `work`.id")
            ->join("__ADMIN__","exam_plan.create_id=admin.id")
            ->join("__EXAM_CENTER__","admin.exam_center_id=exam_center.id")
            ->field($field)
            ->where($map)
            ->where($where)
            ->select();
    }
    public function getDataWithTable($map=[]){
//        print_r($map);die;
        $field = "exam_plan.id AS exam_plan_id,exam_plan.title,work_type.`name` AS wtname,exam_plan.work_type,exam_work.id AS exam_work_id,`work`.`name` AS wname,`work`.id AS work_id,exam_work_level.work_level AS wllevel,exam_work_level.id AS work_level_id,work_direction.id AS wdid,work_direction.`name` AS wdname,exam_plan.enroll_starttime,exam_plan.enroll_endtime,exam_center.`name`";
        $where = " exam_work.type=5 AND exam_work.delete_time is NULL AND exam_plan.delete_time IS NULL AND exam_work_level.delete_time IS NULL AND `work`.delete_time IS NULL AND work_type.delete_time IS NULL AND work_direction.delete_time IS NULL AND admin.delete_time IS NULL AND exam_center.delete_time IS NULL";
        return $data = $this
            ->join("__EXAM_WORK__","exam_plan.id=exam_work.exam_id")
            ->join("__EXAM_WORK_LEVEL__","exam_plan.id=exam_work_level.alltype_id AND exam_work_level.exam_work_id=exam_work.id")
            ->join("__WORK__","exam_work.work_id=`work`.id")
            ->join("__WORK_TYPE__","exam_plan.work_type = work_type.id")
            ->join("__WORK_DIRECTION__","work_direction.work_id = `work`.id","left")
            ->join("__ADMIN__","exam_plan.create_id=admin.id")
            ->join("__EXAM_CENTER__","admin.exam_center_id=exam_center.id")
            ->field($field)
            ->where($map)
            ->where($where)
            ->select();
    }

    /**
     * 鉴定计划对应的工种级别
     */
    public function getTypelevel($map=[]){
        $field = "exam_plan.id AS exam_plan_id,exam_plan.title,work_type.`name` AS wtname,exam_plan.work_type,exam_work.id AS exam_work_id,`work`.`name` AS wname,`work`.id AS work_id,exam_work_level.work_level AS wllevel,exam_work_level.id AS work_level_id,exam_plan.enroll_starttime,exam_plan.enroll_endtime,exam_center.`name`";
        $where = " exam_work.delete_time is NULL AND exam_plan.delete_time IS NULL AND exam_work_level.delete_time IS NULL AND `work`.delete_time IS NULL AND work_type.delete_time IS NULL AND admin.delete_time IS NULL AND exam_center.delete_time IS NULL";
        return $data = $this
            ->join("__EXAM_WORK__","exam_plan.id=exam_work.exam_id")
            ->join("__EXAM_WORK_LEVEL__","exam_plan.id=exam_work_level.alltype_id AND exam_work_level.exam_work_id=exam_work.id")
            ->join("__WORK__","exam_work.work_id=`work`.id")
            ->join("__WORK_TYPE__","exam_plan.work_type = work_type.id")
            ->join("__ADMIN__","exam_plan.create_id=admin.id")
            ->join("__EXAM_CENTER__","admin.exam_center_id=exam_center.id")
            ->field($field)
            ->where($map)
            ->where($where)
            ->select();
    }
    /**查找计划表的工种和类别
     * @param $id
     * @return string
     * @time 2018/10/10
     * @user xuweiqi
     */
    public function selectExamPlan($id){
        $ExamPlandata['ep.id'] = $id['exam_plan_id'];
        $ExamPlandata['ew.type'] = $id['type'];
        $ExamPlandata['ew.delete_time'] = null;
        $ExamPlandata['ew.status'] = 1;
        $ExamPlandata['w.status'] = 1;
        $ExamPlandata['w.delete_time'] = null;
        $field="ep.*,w.code,w.name,w.id as wid,ew.work_id";
        $join = array(
            ["__EXAM_WORK__ ew","ep.id=ew.exam_id","left"],
            ["__WORK__ w","ew.work_id=w.id","left"],
        );

        $data = $this->BaseJoinSelect('ep',$join,$ExamPlandata,[$field]);
        return $data;
    }

    /**查找计划表的工种和类别
     * @param $id
     * @return string
     * @time 2018/10/10
     * @user xuweiqi
     */
    public function selectExamPlanSan($id){
        $ExamPlandata['ep.id'] = $id['exam_plan_id'];
        $ExamPlandata['ew.type'] = $id['type'];
        $ExamPlandata['ew.delete_time'] = null;
        $ExamPlandata['ew.status'] = 1;
        $ExamPlandata['w.status'] = 1;
        $ExamPlandata['w.delete_time'] = null;
        $field="ep.*,w.code,w.name,w.id as wid,ew.work_id,ewl.work_level";
        $join = array(
            ["__EXAM_WORK__ ew","ep.id=ew.exam_id","left"],
            ["__WORK__ w","ew.work_id=w.id","left"],
            ["__EXAM_WORK_LEVEL__ ewl","ew.id=ewl.exam_work_id","left"],
        );

        $data = $this->BaseJoinSelect('ep',$join,$ExamPlandata,[$field]);
        return $data;
    }


    //查找计划的数据
    public function findExamPlanData($id){
        //鉴定计划 type=5
        $datas = $this->BaseFind(['id'=>$id['exam_plan_id'],"status"=>1]);
        return $datas;
    }

    /**查询examplan 所有数据
     * @param $map
     * @return data
     * @user xuweiqi
     */
    public function selectExamPlanData($map){

        return $this->BaseSelect($map);

    }

    //查找计划的数据
    public function getExamPlanData($map){
        $datas = $this->BaseFind($map);
        return $datas;
    }


    //查找正在进行的鉴定计划
    public function nowPlan($where=[],$field,$column='ep.create_time desc')
    {
        // $where['ee.organize_id'] = session("organizeuser")['id'];
        $join = array(
            ['exam_enroll ee','ee.exam_plan_id=ep.id and ee.organize_id='.session("organizeuser")['id'].' and ee.delete_time IS NULL','left']
        );
        $paginate = array(
           config('paginate.list_rows'),
           false,
           ['query'=>Request()->param()]
        );
        return $this->BaseJoinSelectPage($paginate,'ep',$join,$where,[$field],$column,'ep.id');
    }

    public function orderPlan($where=[],$field,$column='ep.create_time desc')
    {
        $join = array(
            ['__EXAM_ORDER__ eo','eo.exam_plan_id=ep.id and eo.type_id='.session("organizeuser")['id'].' and eo.type='.session("organizeuser")['type'] ,'left'],
        );
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query'=>Request()->param()]
        );
        return $this->BaseJoinSelectPage($paginate,'ep',$join,$where,[$field],$column,'ep.id');
    }

    public function gradePlan($where=[],$field,$column='ep.create_time desc')
    {
        $join = array(
            ['exam_enroll ee','ee.exam_plan_id=ep.id and ee.organize_id='.session("organizeuser")['id'],'left'],
            ['grade g','g.user_login_id = ee.user_login_id','left']
        );
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query'=>Request()->param()]
        );
        return $this->BaseJoinSelectPage($paginate,'ep',$join,$where,[$field],$column,'ep.id');
    }

    public function certPlan($where=[],$field,$column='ep.create_time desc')
    {
        $join = array(
            ['exam_order eo','eo.exam_plan_id = ep.id','inner'],
            ['exam_order_detail eod','eod.order_id=eo.id'],
            ['certificate c','eod.enroll_id = c.exam_enroll_id and c.exam_plan_id=ep.id','left'],
            ['cert_way cw','cw.certificate_id = c.id','left']
        );
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query'=>Request()->param()]
        );
        return $this->BaseJoinSelectPage($paginate,'ep',$join,$where,[$field],$column,'eo.id');
    }

}