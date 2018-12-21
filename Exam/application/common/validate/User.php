<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['id_card', 'require|max:18', '请输入证件号|证件号长度超出最大长度'],

        ['username', 'require|max:25|min:2', '用户名不能为空|用户名长度不符合|用户名长度不符合'],
        ['password', 'require|max:18|min:8|/^[\w]{8,18}$/', '密码不能为空|密码长度不得超过18位|密码长度必须大于8位|密码不符合规范'],
        ['oldpwd', 'require|max:20|min:6|/^[\w]{8,18}$/', '旧密码不能为空|旧密码长度不符合|旧密码长度不符合|旧密码不符合规范'],
        ['newpwd', 'require|max:20|min:6|/^[\w]{8,18}$/', '新密码不能为空|新密码长度不符合|新密码长度不符合|新密码不符合规范'],
        ['conpwd', 'require|max:20|min:6|/^[\w]{8,18}$/', '确认密码不能为空|确认密码长度不符合|确认密码长度不符合|确认密码不符合规范'],
        ['name', 'require|max:25|min:2', '姓名不能为空|姓名长度不符合|姓名长度不符合'],
        ['phone', 'require|length:11|/^1[3456789][0-9]{9}$/'],
    ];
    /** 场景设置 **/
    protected $scene = [
        //登录
        'login' => ['username', 'phone'],
        'updatepwd' => ['oldpwd', 'newpwd', 'conpwd'],
        'updateuserinfo' => ['name', 'phone'],
        'editUserInfo' => ['name', 'phone'],
        'add' => ['username', 'password', 'name', 'phone', 'password'],
    ];
}