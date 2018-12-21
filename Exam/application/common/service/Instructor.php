<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/25
 * Time: 13:46
 */

namespace app\common\service;

use app\common\model\Instructor as MInstructor;

/**
 * Class Instructor
 * @package app\common\service
 */
class Instructor extends MInstructor
{
    /**
     * @param array $paginate
     * @param array $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/25}~{13:50}
     */
    public function selWorkLevelByid($paginate = [], $where = [])
    {
        list($listRows, $simple, $config) = $paginate;

        return $this
            ->join("__EXAM_WORK__", "exam_work.exam_id=instructor.id", "left")
            ->join("__WORK__", "`work`.id=exam_work.work_id", "left")
            ->join("__EXAM_WORK_LEVEL__", "exam_work_level.exam_work_id=exam_work.id AND exam_work_level.alltype_id=instructor.id", "left")
            ->where($where)
            ->field("instructor.*,exam_work.id as eid,`work`.id as wid,`work`.`name` as wname,exam_work_level.id as ewlid,exam_work_level.work_level")
            ->paginate($listRows, $simple, $config);
    }


    /**
     * 获取所有专家 分页
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/8~10:16 AM
     */
    public function getAll($map = [], $field = [], $order = 'id desc')
    {
        $page = ['', '', ['query' => request()->param()]];
        return $this->BaseSelectPage($page, $map, $field, $order);
    }

    /**
     * 获取指定一条
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/8~11:43 AM
     */
    public function getOne($map, $field = [])
    {
        return $this->BaseFind($map, $field);
    }


    /**
     * 查询有没有
     * @param array $map 第一个条件
     * @param array $mapTwo 第二个条件
     * @return int|string
     * @throws \think\Exception
     * @user 李海江 2018/12/2~6:51 PM
     */
    public function getCount($map, $mapTwo)
    {
        return $this->where($map)->whereOr($mapTwo)->count();
    }

    /**
     * 更新数据
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/11/8~3:03 PM
     */
    public function edit($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 添加督导员
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/11/27~5:28 PM
     */
    public function add($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseSave($data);
    }

    /**
     * 批量添加
     * @param $data
     * @return int|string
     * @user 李海江 2018/12/2~5:49 PM
     */
    public function addAll($data)
    {
        return $this->BaseSaveAll($data);
    }
}