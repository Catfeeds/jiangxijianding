<?php
namespace app\common\service;
use app\common\model\Organize as MOrganize;


class Organize extends MOrganize
{

    /**
     * 获取所有组织机构
     * @param array $param
     * @param bool $field
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/12/4~3:35 PM
     */
    public function getAll($param = [], $field = [])
    {
        return $this->BaseSelect($param, $field);
    }

    /**
     * @param $data
     * @param $field
     * @param string $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function onlyUser($data, $field, $userid = '')
    {
        if ($userid) {
            return $this->where($field, $data)
                ->where('id', 'neq', $userid)
                ->find();
        } else {
            return $this->where($field, $data)
                ->find();
        }

    }

    public function getJuryList($paginate, $param = [], $field = [], $order = '', $group = '',$exam_plan_id)
    {
        $join = array(
            ["__JURY_REVIEW__","organize.id=jury_review.organize_id AND jury_review.`exam_plan_id` = ".$exam_plan_id,"left"],
        );
//        print_r($field);die;
        $data = $this->BaseJoinSelectPage($paginate,'organize', $join, $param, $field, $order, $group);
        return $data;
    }

    public function getOrganizeWithEnroll($paginate, $param = [], $field = [], $order = '', $group = '',$exam_plan_id)
    {
        $join = array(
            ["__EXAM_ENROLL__","organize.id=exam_enroll.organize_id AND organize_id>0 AND exam_enroll.`exam_plan_id` = ".$exam_plan_id,"left"],
        );
//        print_r($field);die;
        $data = $this->BaseJoinSelectPage($paginate,'organize', $join, $param, $field, $order, $group);
        return $data;
    }

    /**
     * @param array $map
     * @param array $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListData($map = [], $field = [], $order = '')
    {
        return $this
            ->join("__EXAM_WORK__","organize.id=exam_work.exam_id","left")
            ->join("__WORK_TYPE__","work_type.id=exam_work.work_type","left")
            ->join("__WORK__","`work`.id=exam_work.work_id","left")
            ->join("__WORK_DIRECTION__","work_direction.work_id=`work`.id","left")
            ->join("__EXAM_WORK_LEVEL__","`exam_work_level`.`alltype_id` = `organize`.`id` AND exam_work_level.exam_work_id = exam_work.id AND exam_work_level.type = exam_work.type","left")
            ->where($map)
            ->where("`exam_work`.`delete_time` IS NULL AND `work_type`.`delete_time` IS NULL AND `work`.`delete_time` IS NULL AND `work_direction`.`delete_time` IS NULL AND `exam_work_level`.`delete_time` IS NULL")
            ->field($field)
            ->order($order)
            ->select();
    }

    public function getAllOrganize()
    {
        return $this
                ->where('id','in',function($query){
                        $query->table('exam_enroll')
                        ->where('exam_plan.status=1')
                        ->join('__EXAM_PLAN__','exam_plan.id=exam_enroll.exam_plan_id','left')
                        ->Distinct(true)
                        ->field('organize_id');
                })->select();
    }

    public function getOrganize($data='')
    {
        return $this
            ->where('id','in',function($query) use ($data){
                $query->table('exam_enroll')
                    ->where('exam_plan.id','=',$data)
                    ->join('__EXAM_PLAN__','exam_plan.id=exam_enroll.exam_plan_id','left')
                    ->Distinct(true)
                    ->field('site_id');
            })->select();
    }

    public function getBaseFind($where)
    {
        return $this->BaseFind($where);
    }


}