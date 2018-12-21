<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/8/29
 * Time: 下午7:34
 */
namespace app\common\controller;

use think\Controller;
use think\Request;

class BaseApi extends Controller{

    public function __construct()
    {
        parent::__construct();
        if (!Request::instance()->isAjax()){
            $this->error('禁止非Ajax请求', '/admin/index/index');
        }
    }

}