<?php
namespace app\common\service;

use app\common\model\WorkLevelSubject as MWorkLevelSubject;
use think\Db;

class WorkLevelSubject extends MWorkLevelSubject
{

    /**use
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取统计信息
     */
    public function getCountSubject()
    {
        return $this->field('id,work_id,level,count(*) as sut')->group('work_id,level')->order('sut desc,work_id asc,level asc')->select();
    }

    /** 获取用户报名信息的科目
     * @param $map
     * @return string
     */
    public function selAllSubject($map){
        $join=array(
            ['__SUBJECT__ s',"wls.subject_id=s.id"],
        );
        $subjectNames=$this->BaseJoinSelect('wls',$join,$map,['s.name'],'','','wls.subject_id');
        $subjectName='';
        foreach ($subjectNames as $k=>$v){
             $subjectName .= $v->data['name'];
        }
        return $subjectName;
    }



    /**
     * 获取科目数量
     */
    public function getSubject($field='')
    {
        return $this->field('work_id,level,count(*) as count_subject')->where($field)->group('work_id,level')->order('work_id asc,level asc,count_subject desc')->select();
    }

    /**use
     * @return false|\PDOStatement|string|\think\Collection
     * 获取全部work、level对应信息
     */
    public function getAll()
    {
        $field = ['work_id,level,subject_id'];
        return $this->BaseSelect('',$field,'work_id,level,subject_id');
    }

    /**
     * @param $work
     * @param $level
     */
    public function getAllForWorkLevel($work,$level)
    {
        return $this->where('work_id','=',$work)->where('level','=',$level)->select();
    }

    /**获取用户报名信息的科目信息
     * @param $map
     * @param string $field
     * @return string
     */
    public function selSubject($map,$field=''){
        $join=array(
            ['__SUBJECT__ s',"wls.subject_id=s.id"],
            ['__LEVEL_SUBJECT_PRICE__ p',["wls.subject_id=p.subject_id","wls.level=p.level"]]
        );
        $subjectNames=$this->BaseJoinSelect('wls',$join,$map,$field);
        return $subjectNames;
    }

    /**
     * @param array $param
     * @param array $field
     * @param string $order
     * @param string $limit
     * @param string $group
     * @return string
     * @user 朱颖 2018/12/7~11:52
     */
    public function selWorktype($param = [], $field = [], $order = '', $limit = "", $group = '')
    {
        $join=array(
            ['__WORK__',"work.id=work_level_subject.work_id"],
        );
        $subWorkType=$this->BaseJoinSelect('work_level_subject',$join,$param,$field,$order,$limit,$group);
        return $subWorkType;
    }

}