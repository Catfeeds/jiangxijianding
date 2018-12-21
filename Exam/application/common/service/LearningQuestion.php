<?php
namespace app\common\service;
use app\common\model\LearningQuestion as MLearningTopicOfficial;
use think\Db;
class LearningQuestion extends MLearningTopicOfficial
{

    public function selectSimulationQuestionAnswer($where, $field='*',$learning_paper_history_id)
    {
        $user_id = session('user')['id'];
        $learning_paper_history_id = $learning_paper_history_id ? $learning_paper_history_id : 0;
        return $this
            ->join('__LEARNING_ANSWER_HISTORY__',"learning_question.id = learning_answer_history.question_id and learning_answer_history.paper_id = ".$learning_paper_history_id." and learning_answer_history.user_id = ".$user_id,'left')
            ->field($field)
            ->where($where)
            ->select();

    }


    //查询模考试题
    public function selectQuestion($where,$field)
    {
        $single = $this->single($where, $field);
        $judge = $this->judge($where, $field);
        $more = $this->more($where, $field);
        return array_merge($single, $judge, $more);
    }

    //单选
    public function single($where, $field)
    {
        return Db::query("SELECT ".$field." FROM `learning_question` `que`
                WHERE
                    (
                        `que`.`work_id` = ".$where['work_id']."
                        AND `que`.`work_direction_id` =  ".$where['work_direction_id']."
                        AND `que`.`level_id` = ".$where['level_id']."
                        AND `que`. `type` = 1
                    )
                AND `que`.`delete_time` IS NULL
                ORDER BY
                    right(`que`.`create_time`,4)%FLOOR(rand()*10+1)
                LIMIT 80");
    }

    //多选
    public function more($where, $field)
    {
        return Db::query("SELECT ".$field." FROM `learning_question` `que`
                WHERE
                    (
                        `que`.`work_id` = ".$where['work_id']."
                        AND `que`.`work_direction_id` =  ".$where['work_direction_id']."
                        AND `que`.`level_id` = ".$where['level_id']."
                        AND `que`. `type` = 2
                    )
                AND `que`.`delete_time` IS NULL
                ORDER BY
                    right(`que`.`create_time`,4)%FLOOR(rand()*10+1)
                LIMIT 80");
    }

    //判断
    public function judge($where, $field)
    {
        return Db::query("SELECT ".$field." FROM `learning_question` `que`
                WHERE
                    (
                        `que`.`work_id` = ".$where['work_id']."
                        AND `que`.`work_direction_id` =  ".$where['work_direction_id']."
                        AND `que`.`level_id` = ".$where['level_id']."
                        AND `que`. `type` = 3
                    )
                AND `que`.`delete_time` IS NULL
                ORDER BY
                    right(`que`.`create_time`,4)%FLOOR(rand()*10+1)
                LIMIT 20");
    }





    //web 在线练习
    public function selectWebQuestionWithAnswer($where='',$field='*',$limit='',$learningPaperHistoryId)
    {
        $user_id = session('user')['id'];
        $learningPaperHistoryId = $learningPaperHistoryId ? $learningPaperHistoryId : 0;//learning_paper_history.id = learning_answer_history.paper_id 查询
        return Db::query("SELECT ".$field." FROM `learning_question` `que`
                LEFT JOIN learning_answer_history ON `que`.id = learning_answer_history.question_id
                AND learning_answer_history.user_id = ".$user_id." AND learning_answer_history.paper_id = ".$learningPaperHistoryId."
                WHERE
                    (
                        `que`.`work_id` = ".$where['work_id']."
                        AND `que`.`work_direction_id` = ".$where['work_direction_id']."
                        AND `que`.`level_id` = ".$where['level_id']."
                        AND `que`.`type` = ".$where['type']."
                        AND `que`.`range` = ".$where['range']."
                    )
                AND `que`.`delete_time` IS NULL
                ORDER BY
                    right(`que`.`create_time`,4)%FLOOR(rand()*10+1)
                LIMIT ".$limit);

    }



    public function selectWorkLevelDirection($where='',$field='*',$group)
    {
        return $this
            ->join('__WORK__', 'learning_question.work_id = work.id','left')
            ->join('__WORK_DIRECTION__', 'learning_question.work_direction_id = work_direction.id','left')
            ->where($where)
            ->field($field)
            ->group($group)
            ->select();
    }



    public function selectQuestionWithAnswer($where='',$field='*',$join='left',$limit='')
    {
        $user_id = appGetUid();
        return $this->alias("que")
            ->join("__LEARNING_ANSWER_HISTORY__ answer","que.id = answer.question_id and answer.user_id = ".$user_id,$join)
            ->where($where)
            ->field($field)
            ->order('que.id', 'desc')
            ->group('que.id')
            ->limit($limit)
            ->select();
    }

    public function selectWorkDirection($where='',$field='*')
    {
        return $this->alias("que")
            ->join("__WORK__", "que.work_id = work.id")
            ->join("__WORK_DIRECTION__", 'que.work_direction_id = work_direction.id')
            ->where($where)
            ->field($field)
            ->order('id desc')
            ->limit('20')
            ->select();
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