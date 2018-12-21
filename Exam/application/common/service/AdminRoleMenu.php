<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午6:13
 */

namespace app\common\service;


class AdminRoleMenu
{

    private $SAdmin;

    public function __construct()
    {
        $this->SAdmin = new Admin();
    }


}