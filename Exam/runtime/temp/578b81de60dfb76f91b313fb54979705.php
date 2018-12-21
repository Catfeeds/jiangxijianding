<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:106:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\indexknow.html";i:1545017019;}*/ ?>
<div>
	<span class="title" data-value="<?php echo $title; ?>"></span>
	<div class="header_title"><span>报名须知</span></div>
	<div class="xuzhi_main">
		<p class="redcolor">欢迎您使用江西省职业技能鉴定考试网上报名系统，请务必认真阅读以下事项：</p>
		<p>一、考生不得以他人身份、他人的照片进行报考，否则由此引起的纠纷，由考生承担全部责任。</p>
		<p>二、网上报名基本操作程序如下：</p>
		<p>第一步，考生仔细阅读本须知及相关公告后，点击“注册报名”，进入报名登记系统。</p>
		<p>第二步，请认真填写个人信息、报名信息确保准确无误，填写内容要求能够较全面的反映报考要求。</p>
		<p class="redcolor">照片上传说明</p>
		<p>①上传电子版照片为近期免冠正面证件照，格式为jpg，大小为40K以下，宽度90像素，高度120像素，分辨率300像素。</p>
		<p>②上传照片必须能反映本人特征。</p>
		<p>③获得电子版照片的途径：</p>
		<p>A.拥有扫描仪的报考人员，可以通过扫描照片（照片原件大小：小2寸，扫描比例：1：1，扫描模式：256色以上）；</p>
		<p>B.拥有数码相机的报考人员，可以通过数码拍摄；</p>
		<p>C.直接到照相馆拍摄电子版照片，并让工作人员按要求帮助处理。</p>
		<p>第三步，确认信息提交审核并打印报名表</p>
		<p>第四步，认真填写报名表并携带相应材料前往现场审核地点提交审核。</p>
		<p>第五步，资格审查合格后，前往我的报名中缴费。</p>
		<p>四、准考证打印。考生缴费成功后请于规定时间内登录本网站自行打印准考证。打印准考证一律使用A4规格纸张打印。</p>
	</div>
</div>
<script>
	layui.use(['form', 'layer','jquery'], function () {
		$('title').html($('.title').data('value'));
	});
</script>