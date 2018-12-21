<?php
namespace app\common\model;

class Subject extends BaseModel
{


    //获取科目表对应的所有级别
    public function getLevelByid($map=[],$field='')
    {
        return $data = $this
            ->join("__SUBJECT_LEVEL_ID__ sl","s.id=sl.subject_id")
            ->field($field)
            ->where($map)
            ->select();
    }
}