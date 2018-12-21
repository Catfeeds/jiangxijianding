<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/31
 * Time: 9:51
 */

namespace app\admin\validate;
use think\Validate;

class Organize extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['username', 'require|max:25|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','用户名不能为空|用户名长度不符合|用户名长度不符合|用户名不符合规范'],
        ['password', 'require|max:12|min:6|/^[\w]{6,12}$/','密码不能为空|密码长度不符合|密码长度不符合|密码不符合规范'],
        ['name', 'require|max:25|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','名称不能为空|名称长度不符合|名称长度不符合|名称不符合规范'],
        ['phone', 'require|length:11|/^1[345678][0-9]{9}$/'],
        ['build_date', 'require'],
        ['linkman', 'require|max:25|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','联系人不能为空|联系人长度不符合|联系人长度不符合|联系人不符合规范'],
        ['dutyer', 'require|max:25|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','负责人不能为空|负责人长度不符合|负责人长度不符合|负责人不符合规范'],
        ['code' , 'require|/^\d{3}$/','代码不能为空|代码不符合规范'],

    ];
    /** 场景设置 **/
    protected $scene = [
        //organize添加
        'addorganize'=> ['username','password','name','phone','build_date','linkman','code','dutyer'],
        'uporganize'=> ['name','phone','build_date','linkman','code','dutyer'],
    ];
}