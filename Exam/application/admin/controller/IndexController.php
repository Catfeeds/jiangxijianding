<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Admin;

class IndexController extends AdminBase
{

    /**
     * 后台首页
     * @return \think\response\View
     * @user 李海江 2018/9/17~下午5:40
     */
    public function index()
    {
        $adminuser = session('adminuser');
        return view('',["adminuser"=>$adminuser]);
    }


    //修改密码页面
    public function updatepage()
    {
        return view('update');
    }


    /**
     * 修改详细信息页面
     * @return \think\response\View
     * @user 朱颖 2018/9/20~9:42
     */
    public function infopage()
    {
        $arrAdmin = session("adminuser");
        $map['id'] = $arrAdmin['id'];
        $map['status'] = 1;
        $model = new Admin();
        $currAdminAndRole = $model->BaseWithFind("role",$map);
        return view('userinfo',['currAdminAndRole'=>$currAdminAndRole]);
    }


}

