<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/29
 * Time: 13:43
 */

namespace app\common\controller;
use think\Controller;


class CertinquireBase extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('user')) {

        }else{
            $this->redirect("/examinee/Indexcert/index");
        }
    }

}