<?php 

$pay_state = [
	1     =>'未支付',
	2    =>'缴费中',
	3    =>'缓缴费',
	4 =>'已支付',
	5 =>'申请缓缴费',
	6 =>'缓缴费失败',

];

$status = [
    -1     =>'撤销',
    0     =>'未提交',
    1     =>'已提交',
    2     =>'已受理',
    3     =>'已开票',
    4     =>'发票邮寄',
];

return ['pay_state'=>$pay_state,'status'=>$status];
 