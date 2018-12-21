<?php

/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/6
 * Time: 18:11
 */


namespace app\examinee\controller;

use app\common\controller\ExamineeMenu;
use think\Controller;
use app\common\service\Userinfo;
use think\Request;
use app\common\controller\Examineebase;

class MenuController extends Examineebase
{
    /**
     * @var Userinfo
     */
    protected $SUserinfo;

    /**
     * MenuController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SUserinfo = new Userinfo();
    }

    /** 报名须知
     * @return \think\response\View
     * @user xuweiqi
     */
    public function index()
    {
        $infoData = $this->SUserinfo->BaseFind(['user_login_id'=>session('user')['id']]);
        return view('',['avatar' => $infoData['avatar']]);
    }

    /**
     * 用户修改
     * @return \think\response\View
     * @user xuweiqi
     */
    public function my_center()
    {
        return view();
    }

}