<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/8/29
 * Time: 上午11:33
 */

namespace app\organize\controller;

use app\common\service\Admin;
use think\Controller;
use think\Request;

/**
 * 用户操作
 * Class UserController
 * @package app\admin\controller
 */
class UserController extends Controller
{

    /**
     * 当前模型
     * @var Admin
     */
    private $obj;
    private $captcha;

    /**
     * 构造函数
     * UserController constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->obj = new Admin();
    }


    /**
     * 工作人员登录
     * @return array|mixed
     * @user 朱颖 2018/8/29~下午9:32
     */
    //工作人员登录
    public function login()
    {
        return $this->fetch();
    }

    public function forgetPass()
    {
        return view();
    }

    public function forgetPasstwo()
    {
        $mobile = input('get.mobile');
        return view('',['mobile'=>$mobile]);
    }

    public function forgetPassthree()
    {
        return view();
    }

}