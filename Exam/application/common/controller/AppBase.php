<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/24
 * Time: 2:10 PM
 */
namespace app\common\controller;

use think\Controller;

class AppBase extends Controller
{
    protected $info;
    /**
     * 公共函数 验证每次请求的token是否符合规则
     * 如果是GET请求不需要验证token
     * 如果是非GET需要验证token
     */
    public function __construct($request)
    {
        //不是所有的方法都验证token
        $url = strtolower(request()->controller() . '/' . request()->action());
        //不允许非post请求
        noIsPost($request, $url);
        if (!in_array($url, config('noToken'))) {
            if (request()->header('token') == "") {
                result('40003');
            } else {
                $userLogin = new \app\common\service\UserLogin;
                $res = $userLogin->show(['token' => $request->header('token')]);
                if (empty($res)) result('40002');
                $this->info = $res;
            }
        }
    }
}