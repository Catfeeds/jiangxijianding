<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/20
 * Time: 下午9:08
 */
namespace app\common\validate;


use think\Validate;

class ExamEnroll extends Validate
{
    protected $rule = [
        ['exam_plan_id', 'require', '鉴定名称不能为空'],
        ['work_id', 'require', '职业工种不能为空'],
        ['work_direction_id', 'require', '职业方向不能为空'],
        ['work_level_subject_level', 'require', '职业级别不能为空'],
        ['work_type_id', 'require', '职业工种类型不能为空'],
        ['exam_type', 'require', '考试类型不能为空'],
        ['source', 'require', '考生来源不能为空'],
        ['audit_site', 'require', '审核地点不能为空'],
        ['exam_site', 'require', '考试地点不能为空'],
        ['mobile', 'length:11|/^1[3456789][0-9]{9}$/','手机号长度为11位|手机号格式不正确'],
        ['phone', 'require|length:11|/^1[3456789][0-9]{9}$/','手机号不能为空|手机号长度为11位|手机号格式不正确'],
        ['password', 'require|max:20|min:8|/^[\w]{8,20}$/', '密码不能为空|密码长度不得超过20位|密码长度必须大于8位|密码不符合规范'],
        ['username', 'require|/^[A-Za-z\x{4e00}-\x{9fa5}]+$/u', '姓名不能为空|姓名仅支持中文与字母'],
        ['email', '/^[_a-z0-9A-Z-]+(\.[_a-z0-9A-Z-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', '邮箱格式不正确，请重新输入！'],
        ['gender', 'require', '性别不能为空'],
        ['birthday', 'require', '出生日期不能为空'],
        ['provid', 'require', '省不能为空'],
        ['cityid', 'require', '市不能为空'],
        ['areaid', 'require', '县不能为空'],
        ['address', 'require', '通讯地址不能为空'],
        ['zip_code', '/^[0-9]{6}$/', '邮编格式不正确,请输入6位数字'],
        ['education', 'require', '文化程度不能为空'],
        ['company', 'require', '工作单位不能为空'],
        ['invoice_name','require|/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', '发票抬头不能为空|发票抬头不符合规则，只支持中文，大写英文，英文小括弧和下划线'],
        ['identification','/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/', '纳税人识别号格式不正确'],
        ['zheng','require','身份证正面未上传'],
        ['fan','require','身份证反面未上传'],
        ['xueli','require','学历未上传'],
        ['cert','require','证书未上传'],
    ];

    protected $scene = [
//        'examEnroll' => ['exam_plan_id','work_id','work_direction_id','work_level_subject_level','exam_type','source','audit_site','exam_site'],
        'examEnroll' => ['exam_plan_id','work_id','exam_site'],
//        'examEnroll' => ['exam_plan_id','work_id','work_direction_id','work_level_subject_level'],
        'forgetPass' => ['mobile','password'],
        'sendmessage'=> ['mobile'],
        'basic' =>['username','gender','birthday','provid','cityid','areaid','address','zip_code','education','company','email'],
        'basicEdit' =>['zip_code','email'],
        'invoiceOne'=>['invoice_name','identification','phone','email'],
        'invoiceTwo'=>['invoice_name','identification','email'],
        'auditData' => ['zheng','fan','xueli','cert']
    ];
}
