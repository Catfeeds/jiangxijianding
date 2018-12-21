<?php
namespace app\common\service;
use app\common\model\LearningSetVolume as MLearningSetvolume;

class LearningSetVolume extends MLearningSetvolume
{
    public function select($where='')
    {
        return $this
            ->where($where)
            ->order('id', 'desc')
            ->select();
    }

    /***
     * 查询某一列的值
     * @param string $where
     * @param $column
     * @return array
     */
    public function selectColumn($column,$where='')
    {
        return $this
            ->where($where)
            ->order('id', 'desc')
            ->column($column);
    }
}