<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/28
 * Time: 15:08
 */

namespace app\examinee\validate;

use think\Validate;
class Sign extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['mobile', 'require|max:11|min:11|/^1[3456789]\d{9}$/','手机号不能为空|手机号长度不符合|手机号长度不符合|手机号格式不正确'],
        ['password', 'require|max:12|min:6|/^[\w]{6,12}$/','密码不能为空|密码长度不符合|密码长度不符合|密码不符合规范'],
        ['passwordRes', 'require|max:12|min:6|/^[\w]{6,12}$/','确认密码不能为空|确认密码长度不符合|确认密码长度不符合|确认密码不符合规范'],
        ['sf', '/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/','身份证不符合规范'],
        ['hz', '/^[a-zA-Z0-9]{3,21}$/','护照不符合规范'],
        ['jg', '/^[a-zA-Z0-9]{7,21}$/','军官证号不符合规范'],
        ['ga', '/^[a-zA-Z0-9]{5,21}$/','港澳通行证号不符合规范'],
    ];
    /** 场景设置 **/
    protected $scene = [
        //注册
        'sf' => ['mobile', 'password','sf'],
        'hz' => ['mobile', 'password','hz'],
        'jg' => ['mobile', 'password','jg'],
        'ga' => ['mobile', 'password','ga'],

        //登录
        'entersf' => ['password','sf'],
        'enterhz' => ['password','hz'],
        'enterjg' => ['password','jg'],
        'enterga' => ['password','ga'],

        //修改密码
        'updatepwd' => ['password','passwordRes'],

        //忘记密码
        'sendmessage'=>['mobile'],

    ];
}