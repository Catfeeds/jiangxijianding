<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\ExpertWorkLevel;
use think\Request;

class ExpertWorkLevelController extends BaseApi
{

    private $SExpertWorkLevel;

    public function __construct()
    {
        parent::__construct();
        $this->SExpertWorkLevel = new ExpertWorkLevel();
    }

    public function edit()
    {
        $webData = Request::instance()->only(['id', 'is_check', 'is_debate', 'is_question', 'is_thesis', 'status'], 'post');
        $res = $this->SExpertWorkLevel->edit($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }
}