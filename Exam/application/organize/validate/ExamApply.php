<?php  

namespace app\organize\validate;

use think\Validate;
class ExamApply extends Validate
{
	/** 规则 **/
    protected $rule = [
        ['title', 'require|max:100|min:5|/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','标题不能为空|标题不得超过100字|标题不得少于5个字|标题不符合规范'],
        ['exam_num', 'require|/^[1-9]\d*$/','参考人数不能为空|参考人数不正确'],
        ['exam_time', 'require','考试时间不能为空'],
        ['work_type', 'require','类型不能为空'],
        ['work', 'require','工种不能为空'],
        ['level', 'require','级别不能为空'],
        ['reason','require|min:6|max:100','申请原因不能为空|原因不得少于6个字|原因不得超过100字|'],

    ];
    /** 场景设置 **/
    protected $scene = [
        //添加
        'addexamapply'=> ['title','exam_num','exam_time','work_type','work','level','reason'],
        'updexamplan'=> ['title','enroll','audit_endtime','pay_endtime','print','exam_time','type','status','work_type'],
    ];
}