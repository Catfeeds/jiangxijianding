<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/17
 * Time: 10:41 AM
 */
$totalStatus = [

    'delete'=>-1, //删除(冻结/解冻)
    'init' => 1,  //正常
    'cancel'=>5,//撤销
    'uploadfile'=>6,//已提交
    'submit'=>10,//确认提交审查
    'print'=>15,//打印报名表
    'nopass'=>20,//审核不通过
    'reject'=>25,  //驳回
    'checkpass'=>30,//审核通过
    'huan'  =>46,//申请缓缴费中
    'huanfalse'=>47, //缓缴费失败
    'payost' => 48,//缴费中
    'paydelayed' => 49,//缓缴费成功
    'paypass'=>50,//已缴费
    'printticket'=>55,//打印准考证
    'checkresults '=>60,//查询成绩
    'applycertificate'=>65,//申领证书
];

$thesisState = [
    'noupload' => 0, //未上传
    'needupload' => 1, //需要上传未上传
    'haveupload' =>2, //已上传
];

$cooper = [
    'cooper'=>'本人保证上述填写信息和报考时所提供的学历证、身份证等证件真实有效，  如有误或提供的证件不实而造成的后果，本人愿意承担一切责任。'
];


/**
 * 鉴定报名页面
 */
$apply = [
    'apply'=>[   //打印报名表
        $totalStatus['submit'],  //10
        $totalStatus['checkpass'], //20
//        $totalStatus['print'],   //15
        $totalStatus['payost'],  //48
        $totalStatus['paypass'],  //50
        $totalStatus['paydelayed'], //51
        $totalStatus['printticket'], //55
    ]
];

$audit = [
    'audit'=>[
        $totalStatus['init'],  //1
    ]
];

$pay=[
  'pay'=>[
      $totalStatus['checkpass'], //20
      $totalStatus['payost'],   //48
      $totalStatus['paypass'],   //50
      $totalStatus['printticket'], //55
  ]
];

$ticket=[
    'ticket'=>[
        $totalStatus['paypass'], //50
        $totalStatus['printticket'], //55
    ]
];

$update=[
    'update'=>[
        $totalStatus['init'],   //1
        $totalStatus['delete'],  //-1
        $totalStatus['reject'],  //30
        $totalStatus['cancel'],  //45
    ]
];

$delete=[
    'delete'=>[
        $totalStatus['init'],  //1
        $totalStatus['delete'],  //-1
        $totalStatus['reject'],  //30
        $totalStatus['nopass'],  //40
        $totalStatus['cancel'],  //45
    ]
];

$cancel=[
    'revoke'=>[
        $totalStatus['submit'], //10
        $totalStatus['checkpass'], //20
        $totalStatus['print'],  //15
        $totalStatus['huanfalse'],
    ],

    'twocancel'=>[
        $totalStatus['cancel'], //45
    ]

];

$huanpay=[
    'huanpay'=>[
        $totalStatus['checkpass'],
        $totalStatus['huanfalse'],
    ],
    'offline'=>[
        $totalStatus['checkpass'],
        $totalStatus['huanfalse'],
        $totalStatus['paydelayed'],
    ]
];


/**
 * 第三版状态
 */
$apply_states = [
    //报名编辑
    'apply_edit_green'=>[
        $totalStatus['submit'],  //10
        $totalStatus['print'],   //15
//        $totalStatus['nopass'],   //20
        $totalStatus['checkpass'],   //30
        $totalStatus['paypass'],  //50
        $totalStatus['printticket'], //55
    ],
    'apply_edit_current_green'=>[
        $totalStatus['init'],  //1
        $totalStatus['nopass'],   //20
//        $totalStatus['reject'],  //25
    ],
    //提交审核
    'apply_audit_green'=>[
        $totalStatus['submit'],  //10
        $totalStatus['print'],   //15
        $totalStatus['checkpass'],   //20
        $totalStatus['paypass'],  //50
        $totalStatus['printticket'], //55
    ],
    'apply_audit_current'=>[
        $totalStatus['init'],  //1
        $totalStatus['nopass'],   //20

    ],
    //打印报名表
    'apply_print_green'=>[
        $totalStatus['print'],   //15
    ],
    'apply_print_current'=>[
        $totalStatus['submit'],  //10
    ],
    'apply_print_current_green'=>[
        $totalStatus['checkpass'], //20
        $totalStatus['payost'],  //49
        $totalStatus['paypass'],  //50
        $totalStatus['paydelayed'], //48
        $totalStatus['printticket'], //55
    ],
    //审核通过
    'apply_pass_green'=>[
        $totalStatus['checkpass'],   //30
        $totalStatus['paypass'],  //50
        $totalStatus['printticket'], //55
    ],
    'apply_pass_current'=>[
//        $totalStatus['submit'],  //10
        $totalStatus['print'],   //15
    ],
    //缴费
    'apply_pay_green'=>[
        $totalStatus['paypass'],   //50
        $totalStatus['printticket'], //55
    ],
    'apply_pay_current'=>[
        $totalStatus['checkpass'], //30
        $totalStatus['payost'],  //49
    ],
//    //上传论文
    'apply_thesis_green' => [
        $thesisState['haveupload'] //已上传
    ],
     'apply_thesis_current'=>[
         $totalStatus['paypass'], //50
     ],

    //下载准考证
    'apply_ticket_green'=>[
        $totalStatus['printticket'],   //55
    ],
    'apply_ticket_current'=>[

        $totalStatus['paypass'],  //50
    ],

    //等待参加考试
    'apply_exam_current'=>[
        $totalStatus['paypass'],  //50
        $totalStatus['printticket'],  //55
    ],



];




return array_merge($totalStatus,$apply,$audit,$pay,$ticket,$delete,$update,$cancel,$huanpay,$apply_states,$cooper);

