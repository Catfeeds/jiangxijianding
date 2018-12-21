<?php
namespace app\common\service;
use app\common\model\LearningHistory as MLearningHistory;

class LearningHistory extends MLearningHistory
{
    public function select($where='')
    {
        return $this
            ->where($where)
            ->order('id', 'desc')
            ->paginate(10);
    }

    public function Supdate($where,$update)
    {
        return $this
            ->where($where)
            ->update($update);

    }
    public function getWorkHistoryList($where = [],$field="*",$order="",$limit=3)
    {
        return $this
            ->join("__WORK__","learning_history.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","`learning_history`.work_direction_id = work_direction.id","left")
            ->where($where)
            ->limit($limit)
            ->field($field)
            ->order($order)
            ->select();
    }

//    public function getEnrollWork($map=[],$field="",$order="",$limit="")
//    {
//        return $this
//            ->join("__WORK__","work.id=learning_history.work_id")
//            ->join("__WORK_DIRECTION__","work_direction.work_id=work.id")
//            ->where($map)
//            ->field($field)
//            ->order($order)
//            ->limit($limit)
//            ->select();
//    }

    //SELECT work.id as work_id,`work`.name,work.`code`,learning_history.level,work_direction.name,work_direction.id as work_direction_id,learning_history.paper_name,learning_history.score FROM `learning_history`
    //INNER JOIN `work` ON `work`.id=learning_history.work_id
    //INNER JOIN work_direction ON work_direction.work_id=`work`.id;
    public function getWorkByRole($map=[],$field="",$order="",$limit="")
    {
        return $this
            ->join("__EXAM_ENROLL__","learning_history.user_id=exam_enroll.user_login_id")
            ->join("__WORK__","work.id=learning_history.work_id")
            ->join("__WORK_DIRECTION__","work_direction.work_id=work.id")
            ->where($map)
            ->field($field)
            ->order($order)
            ->limit($limit)
            ->select();
    }

    public function shanchu($param)
    {
        $a = $this->BaseDelete($param);

        dump($this->getLastSql());die;

        $result->delete();
    }

}