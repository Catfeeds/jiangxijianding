<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\Admin;
use app\common\service\AdminRole;
use app\common\service\Organize;
use SendMsm\Msm;
use think\Cache;
use think\Request;

/**
 * Class AdminController
 */
class AdminController extends BaseApi
{

    /**
     * @var Admin
     */
    private $Sadmin;
    /**
     * @var AdminRole
     */
    private $SadminRole;

    private $Sorganize;

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->Sadmin = new Admin();
        $this->SadminRole = new AdminRole();
        $this->Sorganize = new Organize();
    }

    /**
     * 获取用户名存不存在
     * @param Request $request
     * @return mixed
     * @user 李海江 2018/8/30~上午11:22
     */
    public function findAdmin()
    {
        $username = Request::instance()->post('username');
        $res = $this->Sadmin->BaseFind(['username' => $username]);
        if ($res) {
            //如果有 表示用户存在
            return 1;
        }
    }


    /**
     * 发送验证码
     * @return array
     * @user 李海江 2018/12/7~5:25 PM
     */
    public function sendMsg()
    {
        //查看附加码是否正确 代码的手机号 和手机号是否一致
        $webData = Request::instance()->only(['username', 'phone', 'code'], 'post');
        if (!captcha_check_lihaijiang($webData['code'])) return layuiMsg(-1, '附加码错误');
        $res = $this->Sadmin->getOne(['username' => $webData['username']]);
        if (empty($res)) {
            //账号不存在
            return layuiMsg(-2, '该组织代码不存在');
        } else {
            if ($res->phone != $webData['phone']) {
                //手机号和账号的手机号不一样
                return layuiMsg(-3, '您输入的手机号和组织代码不一致');
            }
        }
        //如果正正确发送验证码
        $res = Msm::sendMessage($res->phone, 0);
        if ($res['flag']) {
            return layuiMsg(1, $res['msg']);
        } else {
            return layuiMsg(-4, $res['msg']);
        }
    }


    /**
     * 获取除了自己以外的用户名存不存在
     * @return int
     * @user 李海江 2018/9/19~下午5:29
     */
    public function findAdminExceptSelf()
    {
        $webData = Request::instance()->post();
        $res = $this->Sadmin->BaseFind(['username' => $webData['username'], 'id' => ['neq', $webData['id']]]);
        if ($res) {
            //如果有 表示用户存在
            return 1;
        }
    }

    /**
     * 禁用 / 解除禁止管理员
     * @param $uid
     * @param int $status
     * @return array
     * @user 李海江 2018/9/18~上午11:37
     */
    public function disable($uid, $status = 0)
    {
        $res = $this->Sadmin->BaseUpdate(['status' => (bool)$status], ['id' => $uid]);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * 后台进行登录
     * @return mixed
     * @user 李海江 2018/9/17~下午6:08
     */
    public function dologin()
    {
        $arrData = Request::instance()->only(['username', 'phone', 'sms', 'code'], 'post');
        //数据验证
        $result = $this->validate($arrData, 'User.login');
        if (true !== $result) return layuiMsg(0, $result);
        //验证附加码
        if (!captcha_check($arrData['code'])) return layuiMsg(0, '附加码错误');
        //验证短信验证码
        $res = check_code($arrData['sms'], $arrData['phone']);
        if (!$res) return layuiMsg(0, '短信验证码错误');
        //查找当前角色存
        $data = $this->Sadmin->BaseFind(['username' => $arrData['username'], 'phone' => $arrData['phone']]);
        //开始判断
        if (empty($data)) {
            return layuiMsg(0, '异常操作');
        } elseif ($data->getData('status') == -1) {
            return layuiMsg(0, '账号已冻结');
        } else {
            $center = $data->center;
            $sdata = [
                'id' => $data['id'],
                'username' => $data['username'],
                'phone' => $data['phone'],
                'center_id' => empty($data->center->id) ? '' : $center['id'],
                'center_type' => $center['type'],
            ];
            //存入Session
            session('adminuser', $sdata);
            //登录成功
            return layuiMsg(1, '登录成功');
        }
    }

    /**
     * [codePhone 查询机构代码与手机是否匹配 发送验证码]
     * @return [type] [description]
     */
    public function codePhone()
    {
        $data = Request()->post();
        $result = $this->Sadmin->codelogin(['a.phone' => $data['phone'], 'o.code' => $data['code']]);
        if (!$result) {
            return layuiMsg(-1, '机构代码与手机号不匹配');
        }

        $res = Msm::sendMessage($data['phone'], 0);
        if (!$res['flag']) {
            return layuiMsg(0, $res['message']);
        } else {
            return layuiMsg(1, '发送成功');
        }


    }

    /**
     * [dologin 机构登录]
     * @return [type] [description]
     */
    public function login()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $code = Cache::get($arrData['phone']);
            if ($code != $arrData['yzm']) {
                $arrMsg['status'] = -2;
                $arrMsg['msg'] = "验证码错误";
                return $arrMsg;
            }
            $map['username'] = $arrData['code'];
            $map['phone'] = $arrData['phone'];
            $map['status'] = 1;
            $map['type'] = 2;

            $data = $this->Sadmin->BaseFind($map, ['exam_center_id']);
            if (empty($data)) {
                $arrMsg['status'] = -2;
                $arrMsg['msg'] = "登录错误";
                //账号或密码错误
                return $arrMsg;
            } else {
                $info = $this->Sorganize->BaseFind(['id' => $data['exam_center_id']]);
                $sdata = [
                    'id' => $info['id'],
                    'userid' => $data['id'],
                    'username' => $data['username'],
                    'phone' => $data['phone'],
                    'type' => $info['type'],
                    'is_institution' => $info['is_institution'],
                ];
                session('organizeuser', $sdata);
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "登录成功";
                //账号或密码错误
                return $arrMsg;
            }
        }
    }


    /**
     * 添加管理员
     * @param Request $request
     * @return array
     * @throws \think\exception\PDOException
     * @user 李海江 2018/9/19~下午5:32
     */
    public function add()
    {
        $webData = Request::instance()->post();

        $Admindata['exam_center_id'] = $webData['cityId'];
        $Admindata['office_id'] = $webData['office_id'];
        $Admindata['username'] = $webData['username'];
        $Admindata['name'] = $webData['name'];
        $Admindata['phone'] = $webData['phone'];
        $Admindata['password'] = $webData['password'];
        $AdminRoledata = !empty($webData['role_id']) ? $webData['role_id'] : '';
        $Admindata['status'] = (bool)$webData['status'];
        $validate = $this->validate($Admindata, 'User.add');
        if (true !== $validate) {
            return layuiMsg(0, $validate);
        }
        $res = $this->Sadmin->addAdmin($Admindata, $AdminRoledata);
        if ($res === true) {
            return layuiMsg(1, '注册成功');
        } else {
            return layuiMsg(0, '注册失败请重试');
        }
    }


    /**
     * 修改用户信息
     * @return array
     * @user 李海江 2018/9/20~下午1:53
     */
    public function doeditUserInfo()
    {
        //获取数据
        $webData = Request::instance()->only(['name', 'office_id', 'phone', 'role_id', 'id'], 'post');
        //数据验证

        $result = $this->validate($webData, 'User.editUserInfo');
        if (true !== $result) return layuiMsg(1, $result);
        //修改用户表的信息
        $this->Sadmin->editUserInfo($webData);
        //角色 array
        if (!empty($webData['role_id'])) {
            $arrayRoleId = layuiCheckboxToArray($webData, 'role_id');
            //修改关系表
            $this->SadminRole->editAdminRole($arrayRoleId, $webData);
        }
        //只有成功没有失败
        return layuiMsg(1, '操作成功');
    }

    /**
     * 退出登录
     * @return int
     * @user 朱颖 2018/8/29~下午9:32
     */
    public function loginOut()
    {
        session('adminuser', null);
        return 1;
    }

    //修改详细信息

    /**
     * @return mixed
     * @user 李海江 2018/10/10~9:24 AM
     */
//    public function infopage()
//    {
//        if (Request::instance()->isPost()) {
//            $arrData = input('post.');
//            $validata = Validate('app\admin\validate\User');
//
//            //场景应用
//            if (!$validata->scene('updateuserinfo')->check($arrData)) {
//                $arrMsg['status'] = -1;
//                $arrMsg['msg'] = $validata->getError();
//                return $arrMsg;
//            }
//            $arrAdmin = session("adminuser");
//            $map['id'] = $arrAdmin['id'];
//            $currAdminAndRole = $this->Madmin->BaseUpdate($arrData, $map);
//            if ($currAdminAndRole) {
//                session('adminuser.phone', ['phone' => $arrData['phone']]);
//                $arrMsg['status'] = 1;
//                $arrMsg['msg'] = "修改成功";
//                return $arrMsg;
//            } else {
//                $arrMsg['status'] = -2;
//                $arrMsg['msg'] = "修改失败";
//                return $arrMsg;
//            }
//        }
//    }

    //修改密码页面

    /**
     * @return mixed
     * @user 李海江 2018/10/10~9:24 AM
     */
    public function updatepage()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $validata = Validate('app\admin\validate\User');

            //场景应用
            if (!$validata->scene('updatepwd')->check($arrData)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = $validata->getError();
                return $arrMsg;
            }
            if ($arrData['newpwd'] !== $arrData['conpwd']) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "两次密码输入不一致";
                //两次密码输入不一致
                return $arrMsg;
            }
            $map['password'] = md5($arrData['oldpwd'] . "admin");
            $map['status'] = 1;
            $arrDataOne = $this->obj->BaseFind($map);
//            print_r($data);die;
            if (empty($arrDataOne)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "旧密码错误";
                //账号或密码错误
                return $arrMsg;
            } else {
                $upData['update_time'] = date("Y-m-d H:i:s");
                $upData['password'] = md5($arrData['newpwd'] . "admin");;
                $upMap['id'] = $arrDataOne['id'];
                $objUpdate = $this->obj->BaseUpdate($upData, $upMap);
                if ($objUpdate) {
                    $arrMsg['status'] = 1;
                    $arrMsg['msg'] = "修改成功,请重新登录";
//                    session('adminuser', null);
                    //账号或密码错误
                    return $arrMsg;
                } else {
                    $arrMsg['status'] = -1;
                    $arrMsg['msg'] = "修改失败";
                    //账号或密码错误c
                    return $arrMsg;
                }
            }
        }
    }
}