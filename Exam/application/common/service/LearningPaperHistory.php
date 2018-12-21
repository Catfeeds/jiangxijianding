<?php
namespace app\common\service;
use app\common\model\LearningPaperHistory as MPaper;

class LearningPaperHistory extends MPaper
{
    public function getList($where, $field)
    {
        return $this
            ->join('__WORK__','`learning_paper_history`.work_id = `work`.id','left')
            ->join('__WORK_DIRECTION__','`work`.id = work_direction.work_id','left')
            ->join('__USER_LOGIN__','learning_paper_history.user_id = user_login.id','left')
            ->field($field)
            ->where($where)
            ->select();
    }


    public function getWorkHistoryList($where = [],$field="*",$order="",$limit=3)
    {
        return $this
            ->join("__WORK__","learning_paper_history.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","`learning_paper_history`.work_direction_id = work_direction.id","left")
            ->where($where)
            ->limit($limit)
            ->field($field)
            ->order($order)
            ->select();
    }

    public function getWebWorkHistoryList($where = [],$field="*",$order="",$limit=3)
    {
        return $this
            ->join("__WORK__","learning_paper_history.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","`learning_paper_history`.work_direction_id = work_direction.id","left")
            ->where($where)
            ->limit($limit)
            ->field($field)
            ->order($order)
            ->select();
    }

}