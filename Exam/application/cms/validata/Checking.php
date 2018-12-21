<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/20
 * Time: 下午9:08
 */
namespace app\cms\validate;

use think\Validate;

class Checking extends Validate
{
    protected $rule = [
    ['guide_name', 'max:100|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u', '栏目长度不符合|栏目长度不符合|栏目不符合规范'],
    ['title','max:1|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u', '栏目长度不符合|栏目长度不符合|栏目不符合规范'],
    ['postcode','/^[0-9]{6}$/', '邮编格式不正确,请输入6位数字'],
    ['phone','/^([0-9]{3,4}-)?[0-9]{7,8}$/','电话号码格式不正确，请输入xxxx-xxxxxxxx']
];
    /**场景**/
    protected $scene = [
//        'examEnroll' => ['exam_plan_id','work_id','work_direction_id','work_level_subject_level','exam_type','source','audit_site','exam_site'],
        'column' => ['guide_name'],
        'serve'  => ['title'],
        'phone' =>['postcode','phone']

    ];
}
