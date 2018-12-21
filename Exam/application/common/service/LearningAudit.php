<?php
namespace app\common\service;
use app\common\model\LearningAudit as MLearningAudit;

class LearningAudit extends MLearningAudit
{
    public function select($where='')
    {
        return $this
            ->where($where)
            ->order('id', 'desc')
            ->select();
    }

    public function Supdate($where,$update)
    {
        return $this
            ->where($where)
            ->update($update);

    }
}