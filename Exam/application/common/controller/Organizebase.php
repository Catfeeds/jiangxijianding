<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/29
 * Time: 13:43
 */

namespace app\common\controller;
use think\Controller;


class Organizebase extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('organizeuser')) {

        }else{
            $this->redirect("/cms/index/login");
        }
    }

}