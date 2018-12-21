<?php
namespace app\common\service;
use app\common\model\LearningTopicPaper as MLearningSetvolume;

class LearningTopicPaper extends MLearningSetvolume
{
    public function getList($field = '*',$where = '')
    {
        return $this
            ->join('__WORK__', '`learning_topic_paper`.work_id = `work`.id', 'left')
            ->join('__WORK_DIRECTION__', '`work`.id = work_direction.work_id', 'left')
            ->field($field)
            ->where($where)
            ->select();
    }

    /**
     * 模考试卷列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 刘欣 2018/12/8~16:30
     */
    public function getPaperList($where = [], $field="*", $order="",$user_id)
    {
        return $this
            ->join("__WORK__","learning_topic_paper.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","`learning_topic_paper`.work_direction_id = work_direction.id","left")
            ->join("__LEARNING_PAPER_HISTORY__","learning_topic_paper.id = `learning_paper_history`.paper_id and `learning_paper_history`.user_id = ".$user_id
                ." and `learning_paper_history`.id = (select max(id) from learning_paper_history where paper_id = learning_topic_paper.id and user_id = "
                .$user_id.")",'left')
            ->where($where)
            ->field($field)
            ->order($order)
            ->limit(1)
            ->select();
    }



    public function selectWorkDirection($where='',$field='*')
    {
        return $this->alias("pap")
            ->join("__WORK__", "pap.work_id = work.id")
            ->join("__WORK_DIRECTION__", 'pap.work_direction_id = work_direction.id')
            ->where($where)
            ->field($field)
            ->order('id desc')
            ->select();
    }

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

    public function getPaperDetail($where = [],$user_id,$field="*",$order="",$limit=3)
    {
        return $this
            ->join("__WORK__","learning_topic_paper.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","`learning_topic_paper`.work_direction_id = work_direction.id","left")
            ->join("__LEARNING_PAPER_HISTORY__","learning_topic_paper.id = `learning_paper_history`.paper_id and `learning_paper_history`.user_id = ".$user_id
                ." and `learning_paper_history`.id = (select max(id) from learning_paper_history where paper_id = learning_topic_paper.id and user_id = "
                .$user_id.")",'left')
            ->where($where)
            ->limit($limit)
            ->field($field)
            ->order($order)
            ->select();
    }
}