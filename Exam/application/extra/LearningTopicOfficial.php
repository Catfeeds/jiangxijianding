<?php
/**
 * Created by PhpStorm.
 * User: 刘欣
 * Date: 2018/10/17
 * Time: 10:41 AM
 */

$type = [1 => '单选题', 2 => '多选题', 3 => '判断题', 4 => '填空题', 5 => '简答题', 6 => '作文题', 7 => '论述题', 8 => '分析题', 9 => '操作题'];
//$range = [1 => '正规题库', 2 => '作业题库', 3 => '模拟题库'];
//$range = [1 => '易', 2 => '偏易', 3 => '适中'];
$topicLevel = [1 => '易', 2 => '偏易', 3 => '适中', 4 => '偏难', 5 => '难'];

return ['type' => $type, 'topicLevel' => $topicLevel];
