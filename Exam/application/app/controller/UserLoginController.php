<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/24
 * Time: 2:20 PM
 */
namespace app\app\controller;

use app\common\controller\AppBase;
use app\common\service\UserLogin;
use think\Request;

/**
 * app登录
 * Class UserLoginController
 * @package app\app\controller
 */
class UserLoginController extends AppBase
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var UserLogin
     */
    private $SUserLogin;

    /**
     * UserLoginController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->SUserLogin = new UserLogin;
    }

    /**
     * APP登录
     * @user 李海江 2018/10/25~8:25 PM
     */
    public function index()
    {
        $token = $this->request->header('token');
        $data = $this->request->post();
        //-1 token失效 -2 冻结 -3 用户不存在 -4 密码错误 -5 登录失败
        $res = $this->SUserLogin->login($data, $token);
        if (!$res['flag']) {
            switch ($res['id']) {
                case -1:$code = '40002';break;
                case -2:$code = '40007';break;
                case -3:$code = '40008';break;
                case -4:$code = '40009';break;
                case -6:
                    $code = '40018';
                    break;
                case -5:
                default:$code = '40010';break;
            }
            result($code);
        } else {
            config('app.20001.data', $res['data']);
            result('20001');
        }
    }

}