<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/8/31
 * Time: 下午3:10
 */
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Menu;
use think\Request;
use Tools\GetTree;

/**
 * Class MenuController
 * @package app\admin\controller
 */
class MenuController extends AdminBase
{
    /**
     * @var Menu
     */
    private $SMenu;

    /**
     * MenuController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SMenu = new Menu();
    }

    /**
     * 获取全部权限
     * @return \think\response\View
     * @user 李海江 2018/9/21~下午2:03
     */
    public function getAllMenu()
    {
        $list = $this->SMenu->getAllMenu();
        $data = GetTree::getTreeData($list);
        return view('index',['data'=>$data]);
    }

    /**
     * 添加菜单
     * @return \think\response\View
     * @user 李海江 2018/9/21~下午2:09
     */
    public function addmenu()
    {
        $parent_id = Request::instance()->has('id') ? Request::instance()->route('id') : 0;
        return view('',['parent_id'=>$parent_id]);
    }

    /**
     * 修改菜单
     * @return string|\think\response\View
     * @user 李海江 2018/9/26~11:40 AM
     */
    public function editmenu()
    {
        $id = Request::instance()->route('id');
        if (empty($id)) return '请求失败';
        $arrayData = $this->SMenu->showMenu($id);
        return view('',['data'=>$arrayData]);
    }

}