<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/7
 * Time: 15:42
 */

namespace app\common\service;

use app\common\model\ExamDiscipline as MExamDiscipline;

class ExamDiscipline extends MExamDiscipline
{

    public function examInfoUpdate($map)
    {
        return $this->BaseSave($map);
    }

    public function getInfo($map)
    {
        return $this->BaseSelect($map);
    }

    public function examInfoReturn($map,$where)
    {
        return $this->BaseUpdate($map,$where);
    }
}