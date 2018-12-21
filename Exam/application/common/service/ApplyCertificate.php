<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2018/12/18
 * Time: 18:25
 */

namespace app\common\service;
use app\common\model\ApplyCertificate as MApplyCertificate;

class ApplyCertificate extends MApplyCertificate
{
    public function addSave($addData)
    {
        return $this->BaseSave($addData);
    }
}