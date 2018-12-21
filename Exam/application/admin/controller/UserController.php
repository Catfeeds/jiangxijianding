<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/8/29
 * Time: 上午11:33
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Admin;
use app\common\service\AdminRole;
use app\common\service\AdminRoleMenu;
use app\common\service\Office;
use app\common\service\Role;
use think\Request;

/**
 * 用户操作
 * Class UserController
 * @package app\admin\controller
 */
class UserController extends AdminBase
{

    /**
     * @var Admin
     */
    private $SAdmin;
    /**
     * @var Role
     */
    private $SRole;
    /**
     * @var Adminrole
     */
    private $SAdminRole;
    /**
     * @var AdminRoleMenu
     */
    private $SAdminRoleMenu;
    private $SOffice;

    private $centerType;

    /**
     * 构造函数
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SAdmin = new Admin();
        $this->SRole = new Role();
        $this->SAdminRole = new AdminRole();
        $this->SAdminRoleMenu = new AdminRoleMenu();
        $this->SOffice = new Office();
        $this->centerType = getCenterType();

    }

    /**
     * 人员管理入口
     * @user 李海江 2018/10/30~10:59 AM
     */
    public function attribute()
    {
        $centerType = getCenterType();
        $centerId = getCenterId();
        if ($centerType == 1) $this->redirect('user/province');
        if ($centerType == 2) $this->redirect('user/city', ['cityId' => $centerId]);
        if ($centerType == 3) $this->redirect('user/county', ['countyId' => $centerId]);
    }

    /**
     * 省人员
     * @return \think\response\View
     * @user 李海江 2018/11/1~10:51 AM
     */
    public function province()
    {
        $center_id = getCenterId();
        //搜索条件构造
        $map   = ['exam_center_id' => $center_id, 'type' => 1];
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        //查询
        $list = $this->SAdmin->findCenterUser($map);
        return view('province', ['list' => $list, 'cityId' => $center_id]);
    }

    /**
     * 市人员
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
        //搜索条件构造
        $map   = ['exam_center_id' => $pid, 'type' => 1];
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        //查询
        $list = $this->SAdmin->findCenterUser($map);
        return view('city', ['list' => $list, 'cityId' => $pid, 'flag' => $this->centerType]);
    }

    /**
     * 县人员
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
        //搜索条件构造
        $map   = ['exam_center_id' => $pid, 'type' => 1];
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        //查询
        $list = $this->SAdmin->findCenterUser($map);
        return view('county', ['list' => $list, 'cityId' => $pid, 'flag' => $this->centerType]);
    }


    /**
     * 修改用户信息
     * @param $uid
     * @return \think\response\View
     * @user 李海江 2018/9/18~下午3:15
     */
    public function useredit($uid, $cityId)
    {
        //获取当前中心的所有科室
        $arrayOfficeList = $this->SOffice->getAlls(['center_id' => $cityId]);
        //个人信息
        $res = $this->SAdmin->BaseFind(['id' => $uid]);
        //所有角色
        $roleList = $this->SRole->getAllNoPage(['status' => 1, 'center_id' => $cityId]);
        $roleList = contrastArray($roleList, $res->role);
        return view('', ['res' => $res, 'role' => $roleList, 'office' => $arrayOfficeList]);
    }

    /**
     * 添加用户
     * @param $cityId
     * @return \think\response\View
     * @user 李海江 2018/11/26~9:28 AM
     */
    public function add($cityId)
    {
        //获取当前中心的所有科室
        $arrayOfficeList = $this->SOffice->getAlls(['center_id' => $cityId]);
        $roleList = $this->SRole->getAllNoPage(['status' => 1, 'center_id' => $cityId]);
        return view('add', ['role' => $roleList, 'cityId' => $cityId, 'office' => $arrayOfficeList]);
    }
}