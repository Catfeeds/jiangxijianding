<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/9
 * Time: 9:27 AM
 */

namespace app\api\controller;


use app\common\controller\BaseApi;
use app\common\service\ExamCenter;
use think\Request;

/**
 * Class ExamCenterController
 * @package app\api\controller
 */
class ExamCenterController extends BaseApi
{
    /**
     * @var ExamCenter
     */
    private $SExamCenter;

    /**
     * ExamCenterController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExamCenter = new ExamCenter();
    }

    /**
     * @return array
     * @user 李海江 2018/12/3~7:38 PM
     */
    public function edit()
    {
        $webData = Request::instance()->post();
        $res = $this->SExamCenter->edit($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }
}