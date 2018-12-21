<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\Expert;
use think\Request;

/**
 * Class ExpertController
 * @package app\api\controller
 */
class ExpertController extends BaseApi
{
    /**
     * @var Expert
     */
    private $SExpert;

    /**
     * ExpertController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExpert = new Expert();
    }

    /**
     * 查找是否有指定的username
     * @user 李海江 2018/11/8~11:46 AM
     */
    public function find()
    {
        $username = Request::instance()->post('username');
        $map = ['username' => $username];
        $this->SExpert->getOne($map);
    }

    /**
     * @return array
     * @user 李海江 2018/11/30~9:53 AM
     */
    public function doedit()
    {
        $webData = Request::instance()->only(['id', 'name', 'id_number', 'hire_time', 'phone', 'status'], 'post');
        //验证传来的几个是否符合规则
        $result = $this->validate($webData, 'Expert.edit');
        if (true !== $result) return layuiMsg(0, $result);
        //验证身份证
        $isIdCardValid = validation_filter_id_card($webData['id_number']);
        if (!$isIdCardValid) return layuiMsg(0, '身份证号码格式有误');
        //验证账号是否存在
        $count = $this->SExpert->getOne(['phone' => $webData['phone'], 'id' => ['neq', $webData['id']]]);
        if (!empty($count)) return layuiMsg(0, '该手机号码已存在');
        $res = $this->SExpert->edit($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * @return array
     * @user 李海江 2018/11/30~9:53 AM
     */
    public function add()
    {
        $webData = Request::instance()->only(['name', 'id_number', 'phone', 'hire_time', 'status'], 'post');
        //验证传来的几个是否符合规则
        $result = $this->validate($webData, 'Expert.add');
        if (true !== $result) return layuiMsg(0, $result);
        //验证身份证
        $isIdCardValid = validation_filter_id_card($webData['id_number']);
        if (!$isIdCardValid) return layuiMsg(0, '身份证号码格式有误');
        //验证账号是否存在
        $count = $this->SExpert->getOne(['phone' => $webData['phone']]);
        if (!empty($count)) return layuiMsg(0, '该手机号码已存在');
        //添加
        $res = $this->SExpert->add($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }


}