<?php 

$applyStatus = [

	'notsub'     =>0,//'未提交',
	'submit'     =>1,//'已提交',
	'onepass'    =>2,//'一审通过',
	'twopass'    =>3,//'二审通过',
	'onenotpass' =>4,//'一审不通过',
	'twonotpass' =>5,//'二审不通过',
		
];

$ExamEnrollFile = [
    'zheng' => 1, //1,身份证正面,4身份证反面 2,学历 3,证书 5其他'
    'fan' => 4,
    'xue' => 2,
    'cert' => 3,
    'qita '=> 5
];

$theory_score = [
    'theory_score' => 60,
    'watch_score' => 60,
    'synthesize_score' => 60,
];

$gradeRes = [
    'pass' => '合格',
    'nopass' => '不合格',
];

$websiteinformation = [
    'url' => 'www.elink.etlchina.com',
    'urlphone' => '0791-88301905',
];


$submit = [
	'submit'=>[
		$applyStatus['notsub']
	],
];

return array_merge($applyStatus,$submit,$theory_score,$gradeRes,$ExamEnrollFile,$websiteinformation);
 ?>