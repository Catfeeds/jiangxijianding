<?php
namespace app\common\service;

use app\common\model\Subject as MSubject;

/**
 * Class Subject
 * @package app\common\service
 */
class Subject extends MSubject{

    /**
     * 获取所有数据
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/9/29~11:09 AM
     */
    public function getAll()
    {
        return $this->BaseSelect('','','');
    }

    public function getSub($map)
    {
        $where['id'] = $map['subject_id'];
        return $this->BaseFind($where);
    }
}