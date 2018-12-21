<?php 

$ExamOrderStatus = [

	'nopay'     =>1,//'未支付',
	'paycenter'    =>2,//'缴费中',
	'deferpay'    =>3,//'缓缴费',
	'paysuccess' =>4,//'已支付',
	'defer' =>5,//'申请缓交费',
	'deferfail'=>6//缓缴费失败
];

$submit = [

	'invoice'=>[
		$ExamOrderStatus['paysuccess']
	],
];

return array_merge($ExamOrderStatus,$submit);
 ?>