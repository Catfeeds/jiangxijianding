<?php

namespace app\api\controller;

use app\common\service\Menu;

use app\common\controller\BaseApi;
use think\Request;

/**
 * Class MenuController
 * @package app\api\controller
 */
class MenuController extends BaseApi
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
     * 添加权限菜单
     * @return array
     * @user 李海江 2018/9/25~上午9:41
     */
    public function addMenu()
    {
        $arrayWebData = Request::instance()->post();
        $intResult = $this->SMenu->add($arrayWebData);
        if ($intResult > 0) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }
    }


    /**
     * 删除菜单
     * @return array
     * @user 李海江 2018/9/26~1:29 PM
     */
    public function deleteMenu()
    {
        $id = Request::instance()->post('id');
        $data = $this->SMenu->findMenuData($id);
        if ($data) return layuiMsg(-1, '请先删除子权限');
        $intResult = $this->SMenu->deleteMenu(['id' => $id]);

        if ($intResult) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }

    }


    /**
     * 修改菜单
     * @return array
     * @user 李海江 2018/9/26~1:30 PM
     */
    public function editMenu()
    {
        $data = Request::instance()->only('sort,id,title,url');
        $intResult = $this->SMenu->editMenu($data);

        if ($intResult) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }
    }
}