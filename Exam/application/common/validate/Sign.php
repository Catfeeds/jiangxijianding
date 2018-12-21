<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/28
 * Time: 15:08
 */

namespace app\common\validate;

use think\Validate;
class Sign extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['mobile', 'max:11|min:11|/^1[3456789]\d{9}$/','手机号长度不符合|手机号长度不符合|手机号格式不正确'],
        ['password', 'max:18|min:8|/^[\S]{8,18}$/','密码长度不符合|密码长度不符合|密码必须8到18位，且不能出现空格!'],
        ['passwordRes', 'max:18|min:8|/^[\w]{8,18}$/','确认密码长度不符合|确认密码长度不符合|确认密码必须8到18位，且不能出现空格!'],
        ['sf', '/(^\d{18}$)|(^\d{17}(\d|X|x)$)/','请输入合法身份证!'],
        ['hz', '/^[A-Z][0-9]{8}$/','请输入合法护照!'],
        ['jg', '/^[\u4e00-\u9fa5]{2}[0-9]{6,8}$/','请输入合法军官证!'],
        ['ga', '/^[A-Z][0-9]{8}$/','请输入合法港澳通行证!'],
    ];
    /** 场景设置 **/
    protected $scene = [
        //注册
        'sf' => ['sf','password','mobile'],
        'hz' => ['hz','password','mobile'],
        'jg' => ['jg','password','mobile'],
        'ga' => ['ga','password','mobile'],

        //登录
        'entersf' => ['sf','password'],
        'enterhz' => ['hz','password'],
        'enterjg' => ['jg','password'],
        'enterga' => ['ga','password'],

        //修改密码
        'updatepwd' => ['password','passwordRes'],

        //忘记密码
        'sendmessage'=>['mobile'],

    ];
}