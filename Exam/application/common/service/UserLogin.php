<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午5:35
 */

namespace app\common\service;

use app\common\model\UserLogin as MUserLogin;
use think\Loader;

/**
 * Class UserLogin
 * @package app\common\service
 */
class UserLogin extends MUserLogin
{
    /**
     * @var ExamEnroll
     */
    private $SExamEnroll;

    /**
     * UserLogin constructor.
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->SExamEnroll = new ExamEnroll();
    }

    /**
     * @return mixed
     * @user {2018/10/26}~{18:13}
     */
    public function getselect()
    {
        return model('userlogin')
            ->alias('u')
            ->field('i.*,u.username as userpid,u.mobile,u.id_type')
            ->join('userinfo i', 'u.id=i.user_login_id')
            ->find();
    }

    /** 获取当前用户的所有信息
     * @return mixed
     */
    public function getUserLoginCurrent($map = [])
    {
        $field = 'i.*,u.id_card as userpid,u.mobile,u.id_type';
        $join = array(
            ["__USERINFO__ i", "u.id=i.user_login_id",'left'],

        );
        //获取当前用户的所有信息
        $userLoginId = $this->BaseJoinFind('u', $join, ['u.id' => $map], [$field]);
        return $userLoginId;
    }


    /**
     * @param $data
     * @return mixed|string
     */
    public function insertUserinfoOne($data)
    {
        return $this->BaseSave($data);
    }

    /**
     * 查询记录条数
     * @param $param
     * @return int|string
     * @user 李海江 2018/10/25~10:54 AM
     */
    public function showCount($param)
    {
        $count = $this->BaseSelectCount($param);
        return $count;
    }

    /**
     * 查询指定的一条数据
     * @param $param
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/10/25~11:49 AM
     */
    public function show($param)
    {
        return $this->BaseFind($param);
    }


    /**
     * APP登录
     * @param $data
     * @param $token
     * @return array
     *  -1 token失效
     *  -2 冻结
     *  -3 用户不存在
     *  -4 密码错误
     *  -5 登录失败
     * @user 李海江 2018/10/25~6:15 PM
     */
    public function login($data, $token)
    {
        if (!empty($token)) {
            //验证token
            $res = $this->show(['token' => $token]);
            //如果没有这个token或者token不存在返回
            if (!$res) return modelReturn(-1);
            if ($res->getData('status') == -1) return modelReturn(-2);
            $uid = $res->id;
            $arr['avatar'] = $res->info->avatar;
            $arr['name'] = $res->info->username;
        } else {
            $validate = Loader::validate('App');
            if (!$validate->scene('login')->check($data)) {
                result($validate->getError());
            }
            $user = $this->show(['id_card' => $data['id_card']]);
            if (empty($user)) return modelReturn(-3);
            if ($data['password'] != $user->password) return modelReturn(-4);
            if ($user->getData('status') != 1) return modelReturn(-2);
            /** 正常登陆 **/
            //基础信息
            $basic = $user->info->toArray();
            $token = create_token();
            $arr['token'] = $token;
            $arr['name'] = $basic['username'];
            $arr['avatar'] = $basic['avatar'];
            //更新token
            $updateRes = $this->BaseUpdate($arr, ['id_card' => $data['id_card']]);
            if (!$updateRes) return modelReturn(-5);
            $uid = $basic['user_login_id'];
        }

        //登录的角色
        $role = $this->SExamEnroll->getUserRole($uid);
        //获取当前角色有没有审核被通过的角色 .
        $roleRes = [];
        if (!empty($role)) {
            foreach ($role as $k => $v) {
                $roleRes[] = array_values($v->toArray());
            }
        } else {
            return modelReturn(-6);
        }
        $arr['role'] = $roleRes;
        //查鉴定计划存在什么身份
        $arr = addPath($arr, 'avatar', 'odd');
        return modelReturn('', true, $arr);
    }

    /**
     * @param $param
     * @return mixed
     * @throws \think\exception\DbException
     * @user 李海江 2018/10/25~11:55 AM
     */
    public static function getUid($param)
    {
        $res = self::get($param);
        return $res->id;
    }

    /**
     * 修改数据
     * @param $data
     * @param $param
     * @return false|int|string
     * @user 李海江 2018/11/13~5:11 PM
     */
    public function edit($data, $param)
    {
        return $this->BaseUpdate($data, $param);
    }

    /** 注册
     * @param $loginData
     * @param $infoData
     * @return bool|string
     * @user xuweiqi
     */
    public function addLogin($loginData, $infoData)
    {
        $this->startTrans();
        try {
            $login_id = $this->BaseSave($loginData, true);
            if (!$login_id) {
                throw new \Exception('新建用户失败');
            }
            $infoData['user_login_id'] = $login_id;
            $res = model('userinfo')->BaseSave($infoData);
            if (!$res) {
                throw new \Exception('新建用户关系失败');
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

}