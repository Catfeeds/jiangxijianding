<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/9/30
 * Time: 9:32
 */

namespace app\admin\validate;
use think\Validate;

class ExamPlan extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['title', 'require|max:1000|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','标题不能为空|标题长度不符合|标题长度不符合|标题不符合规范'],
        ['enroll', 'require'],
        ['audit_endtime', 'require'],
        ['pay_endtime', 'require'],
        ['print', 'require'],
        ['exam_time', 'require'],
        ['type', 'require'],
        ['status', 'require'],
        ['work_type', 'require'],
        ['work', 'require'],
        ['linkman', 'require|max:25|min:1|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','联系人不能为空|联系人长度不符合|联系人长度不符合|联系人不符合规范'],

    ];
    /** 场景设置 **/
    protected $scene = [
        //添加
        'addexamplan'=> ['title','enroll','audit_endtime','pay_endtime','print','exam_time','type','status','work_type','work'],
        'updexamplan'=> ['title','enroll','audit_endtime','pay_endtime','print','exam_time','work_type'],
    ];
}