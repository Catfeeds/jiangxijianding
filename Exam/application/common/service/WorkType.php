<?php
namespace app\common\service;

use app\common\model\WorkType as MWorkType;

/**
 * Class WorkType
 * @package app\common\service
 */
class WorkType extends MWorkType
{

    /**
     * 获取所有类别
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/9/28~2:02 PM
     */
    public function showall()
    {
        return $this::BaseSelect();
    }

    /**
     * 删除工种分类
     * @param $map
     * @return int
     * @user 李海江 2018/10/10~7:09 PM
     */
    public function deleteWorkType($map)
    {
        return $this::destroy($map, false);
    }

    /**
     * 四张表联查
     * @param string $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getwithWork($map=""){
//        print_r($map);die;
        return $data = $this
            ->join("__WORK__","work_type.id=work.work_type_id")
            ->join("__WORK_DIRECTION__","work.id=work_direction.work_id")
            ->join("__WORK_LEVEL__","work.id=work_level.work_id")
            ->field("work.*,work_type.id as tid,work_type.name as tname,work_direction.name as dname,work_direction.id as did,work_level.id as lid,work_level.level")
            ->where('work_type.id','in',$map)
            ->select();
    }



}