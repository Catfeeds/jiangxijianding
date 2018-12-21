<?php

namespace app\common\service;

use app\common\model\Work as MWork;
use think\Request;

/**
 * Class Work
 * @package app\common\service
 */
class Work extends MWork
{

    /**
     * 获取所有工种
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/10/9~1:46 PM
     */
    public function getAlls($map='', $field = [])
    {
        return $this->BaseSelect($map, $field);
    }

    /**
     * 根据选择的角色和工种返回等级和方向
     * @return mixed
     * @user 李海江 2018/11/20~2:35 PM
     */
    public function dirAndLevel()
    {
        $appData = Request::instance()->only(['work_id', 'role'], 'post');
        //获得当前所选工种的信息
        $res = $this->showOne($appData['work_id']);
        $data = [];
        if ($appData['role']) {
            //考评员的级别
            $data['level'] = array(
                ['id' => '00', 'name' => '考评员'],
                ['id' => '01', 'name' => '高级考评员'],
            );
        } else {
            //考生的级别
            $workLevelSubject = $res->workLevelSubject;
            if (!empty($workLevelSubject)) {
                for ($i = 0; $i < count($workLevelSubject); $i++) {
                    $data['level'][$workLevelSubject[$i]->pivot->level]['id'] = "{$workLevelSubject[$i]->pivot->level}";
                    $data['level'][$workLevelSubject[$i]->pivot->level]['name'] = config('EnrollStatusText.work_level_subject_level')[$workLevelSubject[$i]->pivot->level];
                }
                asort($data['level']);
                $data['level'] = array_values($data['level']);
            } else {
                $data['level'] = [];
            }
        }
        //方向
        $workDirection = $res->workDirection;
        if (!empty($workDirection)) {
            for ($i = 0; $i < count($workDirection); $i++) {
                if (!empty($workDirection[$i])) {
                    $data['dir'][$i]['id'] = "{$workDirection[$i]['id']}";
                    $data['dir'][$i]['name'] = $workDirection[$i]['name'];
                }
            }
        } else {
            $data['dir'] = [];
        }
        return $data;
    }


    /**
     * 获取所有工种 关联 分类
     * @param string $param
     * @param string $map
     * @param string $order
     * @return mixed
     * @user 李海江 2018/12/13~2:49 PM
     */
    public function getAll($param = '', $map = '', $order = 'id desc')
    {
        $data = $this->BaseWithSelectPage(['', '', ['query' => $param]], 'workType', $map, '', $order)->each(function ($item) {
            $item->dataStatus = $item->getData('status');
        });
        return $data;
    }

    /**
     * 删除工种(软)
     * @param array $map
     * @return int
     * @user 李海江 2018/9/27~7:11 PM
     */
    public function deleteWork($map)
    {
        return $this::destroy($map, false);
    }

    /**
     * 或者指定工种信息
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/9/28~10:03 AM
     */
    public function showOne($id)
    {
        return $this->BaseFind(['id' => $id]);
    }


    /**
     * 把同一等级的 不同科目合并到一个数组
     * @param $obj
     * @return array
     * @user 李海江 2018/9/29~12:10 PM
     */
    public function sortSubject($obj)
    {
        $getAllLevel = collection($obj)->toArray();
        $array = [];
        foreach ($getAllLevel as $k => $v) {
            $array[$v['pivot']['level']][] = $v;
        }
        ksort($array);
        return $array;
    }

    /**
     * 修改工种信息
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/9/28~3:30 PM
     */
    public function edit($data)
    {
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 添加
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/10/8~2:12 PM
     */
    public function add($data)
    {
        return $this->BaseSave($data);
    }

    /**
     * 根据条件查找一个工种的个数
     * @param $param
     * @return int|string
     * @user 李海江 2018/9/28~3:12 PM
     */
    public function findData($param)
    {
        return $this->BaseSelectCount($param);
    }

    /**
     * 根据id 获取work 联查方向表
     * @param array $map
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selByid($map = [], $field = '')
    {
        return $data = $this
            ->join("__WORK_DIRECTION__", "work.id=work_direction.work_id", 'left')
            ->field($field)
            ->where($map)
            ->select();
    }

    /**
     * 联查级别表
     * @param array $map
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLevelByid($map = [], $field = '')
    {
        return $data = $this
            ->join("__WORK_LEVEL_SUBJECT__", "`work`.`id` = `work_level_subject`.`work_id` AND `work_level_subject`.`status` = 1",'left')
            ->field($field)
            ->where($map)
            ->select();
    }

    /**
     * 根据id in 查询
     * @param $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLastByid($where)
    {
        return $data = $this
            ->where("id", "in", $where)
            ->select();
    }
}