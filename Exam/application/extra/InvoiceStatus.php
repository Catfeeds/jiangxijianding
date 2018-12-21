<?php 

$InvoiceStatus = [

	'nosub'     =>0,//'未提交',
	'submit'    =>1,//'已提交',
	'receive'    =>2,//'已受理',
	'open' =>3,//'已开票',
	'express' =>4,//'纸质邮寄',
	'send'=>5//电子邮件,
];

$submit = [

	'invoice'=>[
		$InvoiceStatus['nosub'],
	],
	'type'=>[
		$InvoiceStatus['receive'],
	],
	'open'=>[
		$InvoiceStatus['nosub'],
	],
];

return array_merge($InvoiceStatus,$submit);
 ?>