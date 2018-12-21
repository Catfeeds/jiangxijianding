<?php
namespace app\common\service;
use app\common\model\LearningAnswerHistory as MAnswerHistory;

class LearningAnswerHistory extends MAnswerHistory
{
    public function questionList($field, $where)
    {
        return $this
            ->join('__LEARNING_QUESTION__','learning_question.id = learning_answer_history.question_id')
            ->field($field)
            ->where($where)
            ->select();
    }

    public function selectQuestionAnswerList($where, $field)
    {
        return $this
            ->join('__LEARNING_QUESTION__','learning_question.id = learning_answer_history.question_id')
            ->field($field)
            ->where($where)
            ->select();
    }
}