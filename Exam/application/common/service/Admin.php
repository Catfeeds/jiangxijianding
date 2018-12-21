<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午5:35
 */

namespace app\common\service;

use app\common\model\Admin as MAdmin;


/**
 * Class Admin
 * @package app\common\service
 */
class Admin extends MAdmin
{
    /**
     * @var AdminRole
     */
    private $SAdminRole;

    /**
     * Admin constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->SAdminRole = new AdminRole();
    }

    /**
     * 获取一个用户信息
     * @param $param
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/12/7~4:56 PM
     */
    public function getOne($param)
    {
        return $this->BaseFind($param);
    }

    /**
     * 添加用户
     * @param $adminData
     * @param $adminRoleData
     * @return bool|string
     * @throws \think\exception\PDOException
     * @user 李海江 2018/9/19~上午10:22
     */
    public function addAdmin($adminData, $adminRoleData)
    {
        $this->startTrans();
        try {
            $admin_id = $this->BaseSave($adminData, true);
            if (!$admin_id) {
                throw new \Exception('新建用户失败');
            }
            if (!empty($adminRoleData)) {
                $role_id = array_keys($adminRoleData);
                $data = array_string_megre($role_id, 'role_id', $admin_id, 'admin_id');
                $res = $this->SAdminRole->BaseSaveAll($data);
                if (!$res) {
                    throw new \Exception('新建用户关系失败');
                }
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }


    /**
     * 修改用户信息
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/9/19~下午10:14
     */
    public function editUserInfo($data)
    {
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 获得自己拥有的菜单权限
     * @return mixed
     * @user 李海江 2018/10/17~10:24 AM
     */
    public function getMyRules()
    {
        $res = $this->BaseFind(['id' => session('adminuser.id')]);
        $rules = $res->role[0]->rules;
        return $rules;
    }


    /**
     * 获取admin信息
     * @param int $uid
     * @return Admin|null
     * @throws \think\exception\DbException
     * @user 李海江 2018/10/18~5:43 PM
     */
    public function getOneAdmin($uid)
    {
        return $this::get($uid);
    }

    /** 查找exam_center 鉴定中心信息
     * @param string $map
     * @return mixed
     */
    public function selPlanCenter($map = '')
    {
        $field = "c.*,a.id,a.exam_center_id";
        $join = array(
            ["__EXAM_CENTER__ c", "a.exam_center_id=c.id"],
        );
        $data = $this->BaseJoinSelect('a', $join, $map, [$field]);
        return $data;
    }


    /**
     * 查找指定中心下的人员
     * @param $param
     * @return mixed
     * @user 李海江 2018/12/4~4:10 PM
     */
    public function findCenterUser($param, $field = [], $order = 'id desc')
    {
        $page = [config('paginate.list_rows'), false, ['query' => request()->param()]];
        $arrayData = $this->BaseSelectPage($page, $param, $field, $order)->each(function ($item) {
            if (empty($item->role)) {
                $item->Irole = '暂无';
            } else {
                foreach ($item->role as $k => $v) {
                    $role[] = $v->getAttr('name');
                }
                $item->Irole = implode('，', $role);
            }
            $item->dataStatus = $item->getData('status');
        });
        return $arrayData;
    }

    /**
     * [codelogin 查询机构代码和手机号是否匹配]
     * @return [type] [description]
     */
    public function codelogin($map)
    {
        $join = array(
            ["__ORGANIZE__ o", "a.exam_center_id=o.id"],
        );
        $data = $this->BaseJoinFind('a', $join, $map, ['a.phone']);
        return $data;
    }

}