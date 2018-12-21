<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:96:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/cms\view\index\index.html";i:1545183599;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="Content-Language" content="zh-CN">
		<link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
		<link rel="stylesheet" type="text/css" href="/static/layui/css/main.css" />
		<link rel="stylesheet" type="text/css" href="/static/css/cms1.0.css" />

		<script src="/static/js/jquery.min.js"></script>
		<script src="/static/js/SuperSlide.2.1.1.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/js/cms.js" type="text/javascript" charset="utf-8"></script>

	</head>
	<title>江西鉴定首页</title>
	<div class="header_top">
		<div class="layui-container">
			<div class="layui-col-xs6 jd_header_left"><span id="tt"></span><span id="week"></span><span id="showDate"></span></div>
			<div class="layui-col-xs6 jd_header_right">
				<a onclick="SetHome(this,window.location)" style="cursor:pointer;" >设为首页 </a>
				<a onclick="addFavorite(window.location,document.title)" href="javascript:void(0)">加入收藏</a>

			</div>
		</div>
	</div>
	<body>

		<div class="banner"><img src="/static/img/cms1.0/banner.png" /></div>
		<style type="text/css">
			* {
				margin: 0;
				padding: 0;
				list-style: none;
			}
			
			body {
				background: #fff;
				font: normal 14px/22px 宋体;
			}
			
			img {
				border: 0;
			}
			
			a {
				text-decoration: none;
				color: #333;
			}
			
			a:hover {
				color: #FF8400;
			}
			
			.js {
				width: 90%;
				margin: 10px auto 0 auto;
			}
			
			.js p {
				padding: 5px 0;
				font-weight: bold;
				overflow: hidden;
			}
			
			.js p span {
				float: right;
			}
			
			.js p span a {
				color: #f00;
				text-decoration: underline;
			}
			
			.js textarea {
				height: 50px;
				width: 98%;
				padding: 5px;
				border: 1px solid #ccc;
				border-top: 2px solid #aaa;
				border-left: 2px solid #aaa;
			}
			
			.clearfix:after {
				content: ".";
				display: block;
				height: 0;
				clear: both;
				visibility: hidden;
			}
			
			.navBar {
				position: relative;
				z-index: 1;
				color: #fff;
				background: url("/static/img/cms1.0/nav.png");
				background-size: 100% 100%;
				width: 100%;
				height: 60px;
			}
			
			.nav {
				width: 1200px;
				margin: 0 auto;
				font-family: "\5B8B\4F53", Arial, Helvetica, sans-serif;
			}
			
			.nav h3 {
				font-size: 100%;
				font-weight: normal;
				font-size: 20px;
			}
			
			.nav .m {
				position: relative;
				float: left;
				width: 120px;
				margin: 0 20px;
				display: inline;
				text-align: center;
			}
			
			.nav h3 a {
				zoom: 1;
				height: 48px;
				line-height: 48px;
				padding: 6px 0;
				display: block;
				color: #fff;
				position: relative;
				z-index: 9;
				cursor: pointer;
			}
			.nav .sub li a:hover
			{
				color: #1284bc;
			}
			.nav .sub {
				display: none;
				width: 88px;
				padding: 10px 15px;
				position: absolute;
				left: 1px;
				top: 60px;
				padding-top: 5px;
				background: #fff;
				float: left;
				line-height: 30px;
				border-bottom-left-radius: 4px;
				border-bottom-right-radius: 4px;
				box-shadow: 0px 0px 13px 3px rgba(0,0,0,.1);
			}
			.nav .sub li {
				text-align: center;
				padding: 2px 0px;
			}
			.nav .sub li{
				border-bottom: 1px solid #B0B0B0;
			}
			.nav .sub li:last-child{
				border-bottom: 0px solid #fff;
			}
			.nav .sub li a {
				color: #666;
				display: block;
				zoom: 1;
				font-size: 16px;
			}

			
			.nav .sub dl {
				display: inline-block;
				*display: inline;
				zoom: 1;
				vertical-align: top;
				padding: 15px 29px;
				line-height: 26px;
			}
			
			.nav .sub dl a:hover {
				color: #c00;
			}
			
			.nav .sub dl dt a {
				color: #000;
			}
			
			.nav .sub dl dd a {
				color: #999;
				padding-left: 7px;

			}
			.nav li{
				float: none;
				line-height: 30px;
				margin: 0;
			}
			
			.nav span {
				float: left;
				line-height: 70px;
				color: white;
				font-weight: 100;
				font-size: 22px;
			}

			.nav .m h3 i {
				display: inline-block;
				*display: inline;
				zoom: 1;
				width: 7px;
				height: 4px;
				/*background: url("/static/img/icn.png")  no-repeat;*/
				overflow: hidden;
				margin: -2px 0 0 5px;
				vertical-align: middle;
			}
			.nav li:hover .jbthis{
				background: linear-gradient(white, #E4F1F7);
				color: #0E8ECD;
			}
			.nav li:hover .sanj{
				background: url("/static/img/lanicn.png")  no-repeat;

			}
		</style>

		<div class="navBar">
			<ul class="nav clearfix">
				<li id="m1" class="m">
					<h3><a target="_blank" href="/">首页</a></h3> </li>

								<li id="m2" class="m">
					<h3><a class="jbthis">政策法规<i class="sanj"></i></a></h3>
					<ul class="sub">
												<li>
							<a target="_blank" href="/cms/index/category?id=5">法律法规</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=8">要闻动态</a>
						</li>
					   					</ul>
				</li>

								<li id="m2" class="m">
					<h3><a class="jbthis">十分广泛<i class="sanj"></i></a></h3>
					<ul class="sub">
												<li>
							<a target="_blank" href="/cms/index/category?id=26">图片文章</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=37">鉴定公告</a>
						</li>
					   					</ul>
				</li>

								<li id="m2" class="m">
					<h3><a class="jbthis">鉴定安排<i class="sanj"></i></a></h3>
					<ul class="sub">
												<li>
							<a target="_blank" href="/cms/index/category?id=1">考试计划</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=9">鉴定动态</a>
						</li>
					   					</ul>
				</li>

								<li id="m2" class="m">
					<h3><a class="jbthis">政务公开<i class="sanj"></i></a></h3>
					<ul class="sub">
												<li>
							<a target="_blank" href="/cms/index/category?id=2">通知公告</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=3">中心概况</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=4">办事指南</a>
						</li>
					   					</ul>
				</li>

								<li id="m2" class="m">
					<h3><a class="jbthis">鉴定服务<i class="sanj"></i></a></h3>
					<ul class="sub">
												<li>
							<a target="_blank" href="/cms/index/category?id=19">技能竞赛</a>
						</li>
					   					</ul>
				</li>

								<li id="m2" class="m">
					<h3><a class="jbthis">资讯中心<i class="sanj"></i></a></h3>
					<ul class="sub">
												<li>
							<a target="_blank" href="/cms/index/category?id=10">部省信息</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=11">市县信息</a>
						</li>
					   						<li>
							<a target="_blank" href="/cms/index/category?id=13">支部动态</a>
						</li>
					   					</ul>
				</li>

							</ul>
		</div>
		


<script type="text/javascript">
	jQuery(".nav").slide({
		type: "menu",
		titCell: ".m",
		targetCell: ".sub",
		effect: "slideDown",
		delayTime: 300,
		triggerTime: 0,
		returnDefault: true
	});
</script>
<style type="text/css">

	.layui-carousel img {
		width: 100%;
		height: 100%;
	}
  .jd_btn2
  {
	  height: 55px;
	  line-height: 55px;
  }
	.jxjd_box{width: 1200px;margin: auto;}
</style>


<div class="jxjd_box content">
			<div class="jieshao_main">
				<div class="layui-col-xs4">
					<div class="layui-carousel" id="test1">
						<div carousel-item>
														<div>
								<a href="/html/article/263.html" target="_blank"><img src="/uploads/cms/picture/20181127/ad4f3aa009528b8cb808aab237978457.jpg" /></a>
								<div class="lunbo_txt"><a href="/html/article/263.html" target="_blank"><span style="color: white;">省职鉴中心开展“夏日送清凉 爱心降酷暑”志愿者服务活动</span></a></div>
							</div>
														<div>
								<a href="/html/article/250.html" target="_blank"><img src="/uploads/cms/picture/20181122/e75b8147f51c444dd5b1465dfdfcdcc7.jpg" /></a>
								<div class="lunbo_txt"><a href="/html/article/250.html" target="_blank"><span style="color: white;">关于对何磊等970人颁发国家职业资格证书的通知</span></a></div>
							</div>
														<div>
								<a href="/html/article/245.html" target="_blank"><img src="/uploads/cms/picture/20181116/7558e8ef72474bc5bd5b027f374a0533.jpg" /></a>
								<div class="lunbo_txt"><a href="/html/article/245.html" target="_blank"><span style="color: white;">刘三秋厅长巡视省直机关事业单位工勤人员岗位等级考核鉴定考点</span></a></div>
							</div>
														<div>
								<a href="/html/article/243.html" target="_blank"><img src="/uploads/cms/picture/20181116/e8c13fdb1e82ab9dd21cda906e20e515.jpg" /></a>
								<div class="lunbo_txt"><a href="/html/article/243.html" target="_blank"><span style="color: white;">省职鉴中心召开座谈会征求意见建议</span></a></div>
							</div>
														<div>
								<a href="/html/article/241.html" target="_blank"><img src="/uploads/cms/picture/20181116/68d03bba08f10891a6f5e618cac462c1.jpg" /></a>
								<div class="lunbo_txt"><a href="/html/article/241.html" target="_blank"><span style="color: white;">省职鉴中心组织干部职工赴井冈山开展学习教育活动</span></a></div>
							</div>
													</div>
					</div>
				</div>
				<div class="layui-col-xs5">
					<div class="yaowen">
						<div class="jieshao_header">
														<span class="yaowen_header_left" name="yao" value="8">要闻动态</span>
							 							<span class="header_right"><a id="a" href="/cms/index/category?id=8" target="_blank">更多<i class="layui-icon layui-icon-right"></i></a></span>
						</div>
												<div name="yao8" >
						<ul>
														<li>
								<a href="/html/article/205.html" target="_blank" class="yaowan_txt" title="省职鉴中心工会开展“不忘初心 永跟党走” 环青山湖竞走活动">省职鉴中心工会开展“不忘初心 永跟党走” 环青山湖竞走活动</a><span>2018-11-11</span>
							</li>
                             							<li>
								<a href="/html/article/204.html" target="_blank" class="yaowan_txt" title="省职鉴中心召开专题民主生活会">省职鉴中心召开专题民主生活会</a><span>2018-11-11</span>
							</li>
                             							<li>
								<a href="/html/article/203.html" target="_blank" class="yaowan_txt" title="全省职业技能鉴定管理人员政策业务培训班在南昌举办">全省职业技能鉴定管理人员政策业务培训班在南昌举办</a><span>2018-11-11</span>
							</li>
                             							<li>
								<a href="/html/article/167.html" target="_blank" class="yaowan_txt" title="省职鉴中心工会开展“不忘初心 永跟党走” 环青山湖竞走活动">省职鉴中心工会开展“不忘初心 永跟党走” 环青山湖竞走活动</a><span>2018-11-11</span>
							</li>
                             							<li>
								<a href="/html/article/166.html" target="_blank" class="yaowan_txt" title="省职鉴中心召开专题民主生活会">省职鉴中心召开专题民主生活会</a><span>2018-11-11</span>
							</li>
                             							<li>
								<a href="/html/article/129.html" target="_blank" class="yaowan_txt" title="省职鉴中心工会开展“不忘初心 永跟党走” 环青山湖竞走活动">省职鉴中心工会开展“不忘初心 永跟党走” 环青山湖竞走活动</a><span>2018-11-11</span>
							</li>
                             						</ul>
						</div>

										</div>

				</div>
				<div class="layui-col-xs3 jieshao">
					<div class="jieshao_header">
						<span class="jieshao_header_left">中心介绍</span>
						<span class="header_right"><a href="/cms/index/centre?id=11" target="_blank">更多<i class="layui-icon layui-icon-right"></i></a></span>
					</div>
					<div class="btn_main">
						<a href="/cms/index/centre?id=29" target="_blank">
							<div class="jieshao_btn"><span><img src="/static/img/cms1.0/map.png"/></span>中心地图<i class="layui-icon layui-icon-right blueright"></i></div>
						</a>
						<a href="/cms/index/centre?id=36" target="_blank">
							<div class="jieshao_btn"><span><img src="/static/img/cms1.0/xinxi.png"/></span>中心简介<i class="layui-icon layui-icon-right blueright"></i></div>
						</a>
						<a href="/cms/index/centre?id=35" target="_blank">
							<div class="jieshao_btn"><span><img src="/static/img/cms1.0/phone.png"/></span>联系电话<i class="layui-icon layui-icon-right blueright"></i></div>
						</a>
					</div>
				</div>
			</div>
			<div class="tongzhi_main">
				<div class="layui-col-xs7">
					<div class="tongzhi">
						<div class="jieshao_header">
														<span class="jieshao_header_left" name="yao" value="2">通知公告</span>
														<span class="header_right"><a id="aa" href="/cms/index/category?id=2" target="_blank">更多<i class="layui-icon layui-icon-right"></i></a></span>
						</div>
												<div name="yao2" >
						<ul>
														<li>
								<a href="/html/article/268.html" class="yaowan_txt2" title="江西省人力资源和社会保障厅" target="_blank">江西省人力资源和社会保障厅</a><span>2018-11-30</span>
							</li>
														<li>
								<a href="/html/article/262.html" class="yaowan_txt2" title="江西省人力资源和社会保障厅关于征集江西省职业技能鉴定专家委员会专家和各专业委员会依托单位名单的通知" target="_blank">江西省人力资源和社会保障厅关于征集江西省职业技能鉴定专家委员会专家和各专业委员会依托单位名单的通知</a><span>2018-11-27</span>
							</li>
														<li>
								<a href="/html/article/251.html" class="yaowan_txt2" title="关于对何磊等970人颁发国家职业资格证书的通知" target="_blank">关于对何磊等970人颁发国家职业资格证书的通知</a><span>2018-11-22</span>
							</li>
														<li>
								<a href="/html/article/197.html" class="yaowan_txt2" title="关于《2018年江西省职业技能鉴定公告》有关具体操作事项的通知" target="_blank">关于《2018年江西省职业技能鉴定公告》有关具体操作事项的通知</a><span>2018-11-11</span>
							</li>
														<li>
								<a href="/html/article/192.html" class="yaowan_txt2" title="2018年10月27日江西省技师、高级技师实操考核安排" target="_blank">2018年10月27日江西省技师、高级技师实操考核安排</a><span>2018-11-11</span>
							</li>
														<li>
								<a href="/html/article/191.html" class="yaowan_txt2" title="关于在全省开展国家职业资格证书查询数据合规性自查的通知" target="_blank">关于在全省开展国家职业资格证书查询数据合规性自查的通知</a><span>2018-10-19</span>
							</li>
														<li>
								<a href="/html/article/159.html" class="yaowan_txt2" title="关于《2018年江西省职业技能鉴定公告》有关具体操作事项的通知" target="_blank">关于《2018年江西省职业技能鉴定公告》有关具体操作事项的通知</a><span>2018-11-11</span>
							</li>
														<li>
								<a href="/html/article/154.html" class="yaowan_txt2" title="2018年10月27日江西省技师、高级技师实操考核安排" target="_blank">2018年10月27日江西省技师、高级技师实操考核安排</a><span>2018-11-11</span>
							</li>
														<li>
								<a href="/html/article/153.html" class="yaowan_txt2" title="关于在全省开展国家职业资格证书查询数据合规性自查的通知" target="_blank">关于在全省开展国家职业资格证书查询数据合规性自查的通知</a><span>2018-10-19</span>
							</li>
														<li>
								<a href="/html/article/121.html" class="yaowan_txt2" title="关于《2018年江西省职业技能鉴定公告》有关具体操作事项的通知" target="_blank">关于《2018年江西省职业技能鉴定公告》有关具体操作事项的通知</a><span>2018-11-11</span>
							</li>
													</ul>
					</div>
										</div>
				</div>
				<div class="layui-col-xs5 jieshao" >
					<div class="xuanxiang">
						<div class="jieshao_header">
							<span class="jieshao_header_left">网上服务</span>
						</div>
						<div class="layui-tab layui-tab-card">
							<ul class="layui-tab-title">
																<li  class="layui-tab-this layui-this" >技能鉴定</li>
																<li >网上服务</li>
																<li >加上对抗疗法克里斯蒂</li>
															</ul>
							<div class="layui-tab-content" style="height: 252px;">
																<div class="layui-tab-item layui-show" >
																<a href='/cms/index/jian?guide=12'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/2d95ed060221f098c7832b957409edbd.png" alt=""></span>网上报名</div>
								</a>
																<a href='/cms/index/indexgrade'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/a7fe5cac3a318945669e6f2d8c2d6aa3.png" alt=""></span>成绩查询</div>
								</a>
																<a href='/cms/index/jian?guide=37'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/6616f1b5b537b95074338d32b6292b19.png" alt=""></span>鉴定公告</div>
								</a>
																<a href='/cms/index/indexticket'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/0a056d9f5722a07bd9c2069d758091aa.png" alt=""></span>准考证查询</div>
								</a>
																<a href='/cms/index/work'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/9957c5083fee12e2760a6e88eccf229c.png" alt=""></span>鉴定机构查询</div>
								</a>
																<a href='cms/index/indexcondition'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/56cee361a77857f6ee8d8ceb8ebe4533.png" alt=""></span>报考条件查询</div>
								</a>
																</div>
																<div class="layui-tab-item" >
																<a href='/cms/index/studenlogin'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/412c9a486fbc0a4cdbbfb550bc862224.png" alt=""></span>社会考生登录</div>
								</a>
																<a href='/cms/index/admin'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/c76b9e07be0a5918e3ca501e0067d2b5.png" alt=""></span>鉴定工作者登录</div>
								</a>
																<a href='/cms/index/login'>
									<div class="jd_btn2" style="height: 55px;"><span><img src="/uploads/cms/picture/20181203/be26c83a328318d9fb1735264c4be517.png" alt=""></span>机构登录</div>
								</a>
																</div>
																<div class="layui-tab-item" >
																</div>
																<div class="layui-tab-item" >
																</div>
															</div>
					</div>

					</div>
				</div>
			</div>
			<div><img class="hengfu" src="/static/img/cms1.0/hengfu.png" /></div>
			<div class="tongzhi_main">
				<div class="layui-col-xs7">
					<div class="tongzhi">
						<div class="jieshao_header">
														<span class="jieshao_header_left" name="yao" value="20">相关下载</span>
														<span class="header_right"><a id="aaa" href="/cms/index/category?id=20">更多<i class="layui-icon layui-icon-right"></i></a></span>
						</div>
												<div name="yao20" >
						<ul>
														<li>
								<a href="/html/article/227.html" class="yaowan_txt2" title="江西省职业技能鉴定指导中心信息修改申请表" target="_blank">江西省职业技能鉴定指导中心信息修改申请表</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/190.html" class="yaowan_txt2" title="江西省职业技能鉴定相关报表下载" target="_blank">江西省职业技能鉴定相关报表下载</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/189.html" class="yaowan_txt2" title="江西省职业技能鉴定指导中心信息修改申请表" target="_blank">江西省职业技能鉴定指导中心信息修改申请表</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/152.html" class="yaowan_txt2" title="江西省职业技能鉴定相关报表下载" target="_blank">江西省职业技能鉴定相关报表下载</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/151.html" class="yaowan_txt2" title="江西省职业技能鉴定指导中心信息修改申请表" target="_blank">江西省职业技能鉴定指导中心信息修改申请表</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/114.html" class="yaowan_txt2" title="江西省职业技能鉴定相关报表下载" target="_blank">江西省职业技能鉴定相关报表下载</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/113.html" class="yaowan_txt2" title="江西省职业技能鉴定指导中心信息修改申请表" target="_blank">江西省职业技能鉴定指导中心信息修改申请表</a><span>2018-11-11</span>
							</li>
                            							<li>
								<a href="/html/article/76.html" class="yaowan_txt2" title="江西省职业技能鉴定相关报表下载" target="_blank">江西省职业技能鉴定相关报表下载</a><span>2018-11-11</span>
							</li>
                            						</ul>
					</div>
										</div>
				</div>
				<div class="layui-col-xs5">
					<a href="/cms/index/admin">
						<div class="admin_btn"><span><img src="/static/img/cms1.0/admin.png"/></span>管理员登录</div>
					</a>
					<div class="jieshao">
						<div class="jieshao_header">
							<span class="jieshao_header_left">快速搜索</span>
						</div>
						<!--<form action="/cms/index/seek" method="get"><button class="layui-btn  layui-btn-radius" id="sou"></button>-->
						<div class="sousuo">
							<input type="text" name="sousuo"  value="" id="sou" placeholder="请输入搜索内容" /><span id="bbbb" ></span>
						</div>
						<!--</form>-->
						<div class="jubaodianhua">
							<img src="/static/img/cms1.0/bigphone.png" />
							<div>
								<p class="dianhua_txt">举报电话</p>
								<p class="dianhua_num">0791-12333</p>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="youlian">
				<div class="layui-col-xs6">
					<span>友情链接</span>
					<div class="layui-input-inline">
						<select class="xialaxuanze" name="quiz">
							<option value="">江西省政府</option>
							<option value="你工作的第一个城市">你工作的第一个城市</option>
							<option value="你的工号">你的工号</option>
							<option value="你最喜欢的老师">你最喜欢的老师</option>
						</select>
					</div>
				</div>
				<div class="layui-col-xs6">
					<span>地方链接</span>
					<div class="layui-input-inline">
						<select class="xialaxuanze" name="quiz">
							<option value="">江西省政府</option>
							<option value="你工作的第一个城市">你工作的第一个城市</option>
							<option value="你的工号">你的工号</option>
							<option value="你最喜欢的老师">你最喜欢的老师</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<footer>
			<img class="quanwei" src="/static/img/cms1.0/quanwei.png" />
			<p>主办单位：江西省职业技能鉴定指导中心 技术支持：北京博奥网络教育科技股份有限公司</p>
			<p>网上支持（职业资格问答） 　　您是第 9027267 位访问者</p>
		</footer>
	</body>

</html>

<script src="/static/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<!--<script src="/static/layui/layui.all.js" type="text/javascript" charset="utf-8"></script>-->
<script src="/static/layui/layui.all.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/js/cms.js" type="text/javascript" charset="utf-8"></script>
<script>

	layui.use('carousel', function() {
		var carousel = layui.carousel;
		//建造实例
		carousel.render({
			elem: '#test1',
			width: '100%',
			height: '284',
			arrow: 'none', //始终显示箭头
			indicator: 'none'
			//,anim: 'updown' //切换动画方式
		});
	});


    layui.use(['form','jquery'],function()
    {
        var $ = layui.jquery;
        $('#bbbb').click(function()
        {
			var data = $('#sou').val();
			location.href = "/cms/index/seek?sousuo="+data;


        })
    });
</script>