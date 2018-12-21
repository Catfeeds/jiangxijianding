<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/20
 * Time: 下午9:08
 */
namespace app\common\validate;

use think\Validate;

class Role extends Validate
{
    protected $rule = [
        ['name', 'require|max:12|min:2', '角色名不能为空|角色名长度不得大于12位|用户名长度不得小于2位'],
    ];

    protected $scene = [
        'editRole' => ['name'],
        'addRole' => ['name'],
    ];
}