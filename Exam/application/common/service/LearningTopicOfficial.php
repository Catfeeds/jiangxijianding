<?php
namespace app\common\service;
use app\common\model\LearningTopicOfficial as MLearningTopicOfficial;

class LearningTopicOfficial extends MLearningTopicOfficial
{
    public function select($where='',$field='*')
    {
        return $this
            ->where($where)
            ->field($field)
            ->order('id', 'desc')
            ->select();
    }
    public function selectQuestionWithAnswer($where='',$field='*')
    {
        $user_id = appGetUid();
        return $this->alias("que")
            ->join("__LEARNING_ANSWER_HISTORY__ answer","que.id = answer.question_id and answer.user_id = ".$user_id,"left")
            ->where($where)
            ->field($field)
            ->order('que.id', 'desc')
            ->select();
//        echo $this->getLastSql();die;
    }


    /**
     * @param string $where
     * @param string $field
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selectEachCount($where='', $field='*')
    {
        return $this
            ->field($field)
            ->where($where)
            ->group('type')
            ->order('id', 'desc')
            ->select();
    }

    /***
     * @param string $where
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selectEachDetail($where='', $field='*')
    {
        return $this
            ->field($field)
            ->where($where)
            ->order('id', 'desc')
            ->select();
    }

}