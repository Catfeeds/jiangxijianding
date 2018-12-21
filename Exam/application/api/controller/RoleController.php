<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\Admin;
use app\common\service\Role;
use think\Request;

/**
 * Class RoleController
 * @package app\api\controller
 */
class RoleController extends BaseApi
{
    /**
     * @var Role
     */
    private $Srole;
    /**
     * @var Admin
     */
    private $SAdmin;

    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->Srole = new Role();
        $this->SAdmin = new Admin();
    }


    /**
     * 创建角色
     * @return array
     * @throws \think\exception\DbException
     * @user 李海江 2018/10/18~5:59 PM
     */
    public function add()
    {
        $arrayWebData = Request::instance()->post();
        //验证
        $validate = $this->validate($arrayWebData, 'Role.addRole');
        if (true !== $validate) return layuiMsg(0, $validate);
        //如果有选择权限获取权限id
        $menu_id = [];
        if (!empty($arrayWebData['menu'])) {
            //array 权限ID
            $menu_id = array_keys($arrayWebData['menu']);
        }
        $strRule = implode(',', $menu_id);
        $center_id = $arrayWebData['cityId'];

        $arrayData = ['name' => $arrayWebData['name'], 'rules' => $strRule, 'center_id' => $center_id];
        //执行添加
        $res = $this->Srole->addRole($arrayData);
        //返回消息
        switch ($res) {
            case 1:
                return layuiMsg('1', '角色创建成功');
                break;
            case -1:
                return layuiMsg('5', '用户名已存在');
                break;
            case -2:
            default:
                return layuiMsg('5', '创建失败请重试');
                break;
        }
    }

    /**
     * 查找角色
     * @return mixed
     * @user 李海江 2018/9/20~下午1:42
     */
    public function findRole()
    {
        $webData = Request::instance()->post();
        $map = array('name' => $webData['name'], 'center_id' => $webData['cityId']);
        return $this->Srole->findRole($map);
    }

    /**
     * 查找除了自己以外的角色
     * @return int
     * @user 李海江 2018/9/20~下午9:26
     */
    public function findRoleExceptSelf()
    {
        $webData = Request::instance()->post();
        $res = $this->Srole->BaseFind(['name' => $webData['name'], 'id' => ['neq', $webData['id']]]);
        if ($res) {
            //如果有 表示用户存在
            return 1;
        }
    }

    /**
     * 禁用 / 解除禁用 角色
     * @return mixed
     * @user 李海江 2018/9/20~下午1:58
     */
    public function disable()
    {
        $arrayWebData = Request::instance()->post();
        $res = $this->Srole->BaseUpdate(['status' => (bool)$arrayWebData['status']], ['id' => $arrayWebData['roleId']]);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }


    /**
     * 修改用户
     * @return array
     * @user 李海江 2018/11/26~4:21 PM
     */
    public function editRole()
    {
        $arrayWebData = Request::instance()->post();
        $validate = $this->validate($arrayWebData, 'Role.editRole');
        if (true !== $validate) return layuiMsg(0, $validate);
        //如果有选择权限获取权限id
        $menu_id = [];
        if (!empty($arrayWebData['menu'])) {
            //array 权限ID
            $menu_id = layuiCheckboxToArray($arrayWebData, 'menu');
        }
        $arrayRule = implode(',', $menu_id);
        $arrayData = array('name' => $arrayWebData['name'], 'rules' => $arrayRule);
        $intRes = $this->Srole->editRole($arrayData, $arrayWebData['id']);
        if ($intRes) {
            return layuiMsg(1, '操作成功');
        }
        return layuiMsg(0, '无任何修改');
    }

}