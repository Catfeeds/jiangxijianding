<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/11
 * Time: 2:06 PM
 */

namespace app\common\validate;


use think\Validate;

class Jury extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['id', 'require', '异常操作'],
        ['name', 'require', '请填写姓名'],
        ['organize_id', 'require', '请选择所属中心'],
        ['phone', 'require|/^1[3456789][0-9]{9}$/', '请输入手机号码|手机号码格式不正确'],
        ['id_number', 'require|length:18', '请输入证件号|身份证号码格式有误'],
        ['status', 'require', '请选择状态'],
    ];
    /** 场景设置 **/
    protected $scene = [
        'add' => ['name', 'phone', 'id_number', 'status', 'organize_id'],
        'edit' => ['id', 'name', 'phone', 'id_number', 'status', 'organize_id'],
    ];
}