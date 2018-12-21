<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/28
 * Time: 15:08
 */

namespace app\organize\validate;

use think\Validate;
class User extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['username', 'require|max:25|min:2|/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u','用户名不能为空|用户名长度不符合|用户名长度不符合|用户名不符合规范'],
        ['password', 'require|max:18|min:8|/^[\w]{8,18}$/','密码不能为空|密码长度不符合|密码长度不符合|密码不符合规范'],
        ['oldpwd', 'require|max:18|min:8|/^[\w]{8,18}$/','旧密码不能为空|旧密码长度不符合|旧密码长度不符合|旧密码不符合规范'],
        ['newpwd', 'require|max:18|min:8|/^[\w]{8,18}$/','新密码不能为空|新密码长度不符合|新密码长度不符合|新密码不符合规范'],
        ['conpwd', 'require|max:18|min:8|/^[\w]{8,18}$/','确认密码不能为空|确认密码长度不符合|确认密码长度不符合|确认密码不符合规范'],
        ['name', 'require|max:25|min:2|/^[a-zA-Z0-9_-]{4,16}$/','昵称不能为空|昵称长度不符合|昵称长度不符合|昵称不符合规范'],
        ['phone', 'require|length:11|/^1[345678][0-9]{9}$/'],
        ['mobile', 'require|length:11|/^1[345678][0-9]{9}$/'],
    ];
    /** 场景设置 **/
    protected $scene = [
        //登录
        'login' => ['username', 'password'],
        'updatepwd' => ['oldpwd','newpwd', 'conpwd'],
        'updateuserinfo' => ['name','phone'],
        'userlogin' => ['mobile','password'],
    ];
}