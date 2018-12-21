<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/21
 * Time: 9:09
 */


namespace app\common\service;

use app\common\model\Organization as MOrganization;

class Organization extends MOrganization
{
    public function saveAllInfo($data='')
    {
        return $this->BaseSave($data);
    }

    public function getBase($where)
    {
        return $this->BaseFind($where);
    }

    public function baseUpdateInfo($data,$where)
    {
        return $this->update($data,$where);
    }
}