<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/8/30
 * Time: 下午6:04
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Menu;
use app\common\service\Role;
use think\Request;

/**
 * Class RoleController
 * @package app\admin\controller
 */
class RoleController extends AdminBase
{
    /**
     * @var Role
     */
    private $SRole;
    /**
     * @var Menu
     */
    private $SMenu;

    /**
     * @var mixed
     */
    private $centerType;

    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SRole = new Role();
        $this->SMenu = new Menu();
        $this->centerType = getCenterType();
    }


    /**
     * 角色管理入口
     * @user 李海江 2018/10/30~10:59 AM
     */
    public function attribute()
    {
        $centerType = getCenterType();
        $centerId = getCenterId();
        if ($centerType == 1) $this->redirect('role/province');
        if ($centerType == 2) $this->redirect('role/city', ['cityId' => $centerId]);
        if ($centerType == 3) $this->redirect('role/county', ['countyId' => $centerId]);
    }

    /**
     * 省角色
     * @return \think\response\View
     * @user 李海江 2018/11/1~10:51 AM
     */
    public function province()
    {
        //搜索条件
        $map   = ['center_id' => getCenterId()];
        $param = searchLike(request()->param());
        $map   = array_merge($map, $param);
        //查询
        $list = $this->SRole->getAll($map);
        return view('province', ['list' => $list, 'cityId' => getCenterId()]);
    }

    /**
     * 市角色
     * @return \think\response\View
     * @user 李海江 2018/11/1~10:51 AM
     */
    public function city()
    {
        if (getCenterType() == 2) {
            $pid = getCenterId();
        } else {
            $pid = Request::instance()->route('cityId');
        }
        $map = ['center_id' => $pid];
        //搜索条件
        $param = searchLike(request()->param());
        $map   = array_merge($map, $param);
        $list  = $this->SRole->getAll($map);
        return view('city', ['list' => $list, 'cityId' => $pid, 'flag' => $this->centerType]);
    }

    /**
     * 县角色
     * @return \think\response\View
     * @user 李海江 2018/11/1~10:51 AM
     */
    public function county()
    {
        if (getCenterType() == 3) {
            $pid = getCenterId();
        } else {
            $pid = Request::instance()->route('cityId');
        }
        $map = ['center_id' => $pid];
        //搜索条件
        $param = searchLike(request()->param());
        $map   = array_merge($map, $param);
        $list  = $this->SRole->getAll($map);
        return view('county', ['list' => $list, 'cityId' => $pid, 'flag' => $this->centerType]);
    }


    /**
     * 修改角色的权限
     * @return \think\response\View
     * @user 李海江 2018/9/20~下午4:42
     */
    public function roleEdit()
    {
        $intRoleId = Request::instance()->param('roleid');
        $uid = getUid();

        $param = [];
        if (getUsername() != config('adminname')) {
            //获取当前登录的人的所有权限
            $rules = getMenu($uid);
            $param['id'] = ['in', $rules];
        }

        $loginAllMenu = $this->SMenu->getAllMenu($param);
        //获取当前角色的有所权限
        $menu = $this->SRole->showOne(['id' => $intRoleId]);
        $arrMenu = explode(',', $menu->rules);
        //勾选
        foreach ($loginAllMenu as $k => $v) {
            foreach ($arrMenu as $kk => $vv) {
                if ($v['id'] == $vv) {
                    $v['checked'] = true;
                }
            }
        }
        $allMenu = getTree($loginAllMenu);
        return view('roleEdit', ['res' => $menu, 'arrMenu' => $allMenu]);
    }

    /**
     * 添加角色
     * @param $cityId
     * @return \think\response\View
     * @user 李海江 2018/11/26~4:13 PM
     */
    public function add($cityId)
    {
        $uid = getUid();
        $param = [];
        if (getUsername() != config('adminname')) {
            $rules = getMenu($uid);
            $param['id'] = ['in', $rules];
        }
        $loginAllMenu = $this->SMenu->getAllMenu($param);
        $allMenu = getTree($loginAllMenu);
        return view('add', ['cityId' => $cityId, 'arrMenu' => $allMenu]);
    }

}