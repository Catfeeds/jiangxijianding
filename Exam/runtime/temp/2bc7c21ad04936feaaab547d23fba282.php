<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:100:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\menu\index.html";i:1545276578;}*/ ?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<link rel="stylesheet" type="text/css" href="/static/center/css/lay.css" />
		<link rel="stylesheet" type="text/css" href="/static/center/css/all.css" />
		<link rel="stylesheet" type="text/css" href="/static/center/css/iconfont.css" />
		<link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
		<title>报名须知</title>

		<style>
			.dati{
				/*height:20px;*/
				text-align:center;
				line-height:40px
			}
		</style>
	</head>

	<body class="login">
		<div class="header_top">
			<div class="layui-container">
				<div class="layui-col-xs6 jd_header_left"><img src="/static/front/img/personalCenterlogo.png" /></div>
				<div class="layui-col-xs6 jd_header_right">
					<a class="ajax-linkk" rel="<?php echo url('/examinee/Menu/my_center'); ?>"><span><i class="layui-icon layui-icon-password xiumi_icon"></i>修改密码</span></a>
					<a href="javascript:;" class="checkOut"><span><i class="layui-icon layui-icon-auz antui_icon"></i>安全退出</span></a>
				</div>
			</div>
		</div>
		<div class="personalCenterbanner"><img src="/static/front/img/personalCenter.png" /></div>

		<div class="content">
			<div class="geren_content">
				<div class="geren">
					<div class="headPortrait"><img class="avatarimg" src="<?php echo $avatar==''?'/static/front/img/timg.jpg':$avatar; ?>"/></div>
					<p><?php echo \think\Session::get('user.username'); ?></p>
					<div class="zhengshu_double">
						<div class="cert">
							<span><?php echo \think\Session::get('user.countcert'); ?></span>
							<p><a target="<?php echo url('/examinee/center/indexcert'); ?>" >证书</a></p>
						</div>
						<div class="daikaoshi_one cert">
							<span><?php echo \think\Session::get('user.examJoinData'); ?></span>
							<p><a target="<?php echo url('/examinee/center/workinfo'); ?>">待考试</a></p>
						</div>
					</div>
				</div>
			</div>

			<div class="layui-jxjdmain">
				<div class="layui-col-xs2 layui-col-md2 jiangxi-container-left">

					<!-- 左侧导航区域（可配合layui已有的垂直导航） -->
					<ul class="jxjd_nav">
						<li class="jxjd_nav-item jxjd-this">
							<a target="<?php echo url('/examinee/center/indexknow'); ?>"><i class="iconfont icon-navicon-wgxzsz"></i>报名须知</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/indexbase'); ?>"><i class="iconfont icon-gerenzhongxinkaobei"></i>个人信息</a>
						</li>
						<!--<li class="jxjd_nav-item">-->
							<!--<a target="<?php echo url('/examinee/center/addExamEnroll'); ?>"><i class="iconfont icon-tianxie"></i>填写报名</a>-->
						<!--</li>-->
						<li class="jxjd_nav-item wodebaoming">
							<a target="<?php echo url('/examinee/center/workinfo'); ?>"><i class="iconfont icon-sign"></i>我的报名</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/indexthesis'); ?>"><i class="iconfont icon-icon--"></i>论文上传</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/questionList'); ?>"><i class="iconfont icon-icon--"></i>考生-在线答题</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/simulationList'); ?>"><i class="iconfont icon-icon--"></i>考生-模拟考试</a>
						</li>


						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/questionListAppraisal'); ?>"><i class="iconfont icon-icon--"></i>考评-在线答题</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/simulationListAppraisal'); ?>"><i class="iconfont icon-icon--"></i>考评-模拟考试</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/materials'); ?>"><i class="iconfont icon-icon--"></i>考评-学习资料</a>
						</li>


						<li class=" style_item" style="width: 120%">
							<i class="layui-icon layui-icon-read"></i>在线学习-考生<i class="layui-icon zhuan layui-icon-right"></i>
						</li>
						<ol class="study_list_main">
							<a class="ajax-linkk" rel="<?php echo url('/examinee/center/questionList'); ?>">
								<li class="study_list">在线答题</li>
							</a>
							<a class="ajax-linkk" rel="<?php echo url('/examinee/center/simulationList'); ?>">
								<li class="study_list">模拟考试</li>
							</a>
						</ol>

						<li class=" style_item" style="width: 120%">
							<i class="layui-icon layui-icon-read"></i>在线学习-考评人员<i class="layui-icon zhuan layui-icon-right"></i>
						</li>
						<ol class="study_list_main">
							<a class="ajax-linkk" rel="<?php echo url('/examinee/center/simulationListAppraisal'); ?>">
								<li class="study_list">在线答题</li>
							</a>
							<a class="ajax-linkk" rel="<?php echo url('/examinee/center/simulationListAppraisal'); ?>">
								<li class="study_list">模拟考试</li>
							</a>
						</ol>

						<!--<li class="jxjd_nav-item">-->
							<!--<a target="<?php echo url('/examinee/center/indexpay'); ?>"><i class="iconfont icon-icon&#45;&#45;"></i>线下缴费</a>-->
						<!--</li>-->

						<!--<li class="jxjd_nav-item">-->
							<!--<a target="<?php echo url('/examinee/center/indexBill'); ?>"><i class="iconfont icon-fapiao"></i>发票管理</a>-->
						<!--</li>-->
						
						<!--<li class=" style_item">-->
							<!--<i class="layui-icon layui-icon-read learn"></i>在线学习<i class="layui-icon zhuan layui-icon-right"></i>-->
						<!--</li>-->

						<!--<ol class="study_list_main">-->
							<!--<li class="jxjd_nav-item dati">-->
								<!--<a class="ajax-linkk" target="<?php echo url('/examinee/center/questionList'); ?>">在线答题</a>-->
							<!--</li>-->

						  <!--<div class="jxjd_nav-item">-->
								<!--<a class="ajax-linkk" target="<?php echo url('/examinee/center/randomTopic'); ?>">-->
									<!--<li class="study_list">试题列表</li>-->
								<!--</a>-->
						  <!--</div>-->

							<!--<li class="jxjd_nav-item dati">-->
								<!--<a target="<?php echo url('/examinee/center/topicLog'); ?>">学时记录</a>-->
							<!--</li>-->
							<!--<li class="jxjd_nav-item dati">-->
								<!--<a target="<?php echo url('/examinee/center/testTopic'); ?>">模拟考试</a>-->
							<!--</li>-->
							<!--<li class="jxjd_nav-item dati">-->
								<!--<a target="<?php echo url('/examinee/center/userHistory'); ?>">学习记录</a>-->
							<!--</li>-->
							<!--<li class="jxjd_nav-item dati">-->
								<!--<a target="<?php echo url('/examinee/center/materials'); ?>">学习资料</a>-->
							<!--</li>-->
						<!--</ol>-->
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/gradeindex'); ?>"><i class="iconfont icon-chengjiguanli"></i>我的成绩</a>
						</li>
						<li class="jxjd_nav-item">
							<a target="<?php echo url('/examinee/center/indexcert'); ?>"><i class="iconfont icon-SSLshuzizhengshu"></i>我的证书</a>
						</li>
					</ul>

				</div>
				<div class="layui-col-xs10 layui-col-md10 jiangxi-container-right">
					<div class="ajax-Box" id="iframeContent"></div>
				</div>
			</div>
		</div>
		<footer class="footer">
			<img class="quanwei" src="/static/front/img/quanwei.png" />
			<p>主办单位：江西省职业技能鉴定指导中心 技术支持：北京博奥网络教育科技股份有限公司</p>
			<p>网上支持（职业资格问答） 　　您是第 9027267 位访问者</p>
		</footer>
	</body>

</html>
<script type="text/javascript" src="/static/layui/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/layui/js/layui.js"></script>
<script type="text/javascript" src="/static/layui/js/layui.all.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>
<script type="text/javascript" src="/static/js/jquery.cookie.js"></script>

<script>
	$("a[target='"+document.location.pathname+"']").css('background','black').parents("li").find("a:first").click();

	//JavaScript代码区域
	layui.use(['layer', 'form','jquery', 'element'], function() {
		var layer = layui.layer,
			form = layui.form,
			element = layui.element,
		    $ = layui.jquery;

		function aaaa(current,url) {
			$('.jxjd-this').removeClass('jxjd-this');
			$(current).addClass('jxjd-this');
			$(".zhuan").removeClass('iconzhuan');

			var	target = url==undefined ? current.find('a').attr('target'):url; // 找到链接a中的targer的值
			$.get(target, function(data) {
				if (data == "error"){
					layer.msg('请先完善信息!',{icon:5});
					$.get('/examinee/Center/indexbase', function(data) {
						$("#iframeContent").html(data);
					});
				}else{
					$(".avatarimg").attr('src')
					$("#iframeContent").html(data);

				}
			});
		}


		//……
		//你的代码都应该写在这里面
		$(function() {
			if($.cookie('examplan_id') !==undefined){
				aaaa($(".wodebaoming"),'/examinee/center/selectplan?id='+$.cookie('examplan_id'));

			}else{
				$.get("<?php echo url('/examinee/center/indexknow'); ?>", function(data) {
					$("#iframeContent").html(data); //初始化加载界面
				});
			}

			$('.jxjd_nav-item').click(function() { //点击li加载界面
                aaaa($(this));
			});



			$('.cert').click(function() { //点击li加载界面
				var currentcert = $(this),
						targetcert = currentcert.find('a').attr('target'); // 找到链接a中的targer的值
				$.get(targetcert, function(data) {
					if (data == "error"){
						layer.msg('请先完善信息!',{icon:5});
						$.get('/examinee/Center/indexbase', function(data) {
							$("#iframeContent").html(data);
						});
					}else{
						$("#iframeContent").html(data);
					}
				});
			});

		});
		// $(".ajax-linkk").each(function() {
		// 	$(this).click(function() {
		// 		var diZhi = $(this).attr("rel");
		// 		htmlobj = $.ajax({
		// 			url: diZhi,
		// 			async: false
		// 		});
		// 		$(".ajax-Box").html(htmlobj.responseText);
		// 	});
		// });
//		$('.jxjd_nav-item').bind('click', function() {
//			$('.jxjd-this').removeClass('jxjd-this');
//			$(this).addClass('jxjd-this');
//			$(".zhuan").removeClass('iconzhuan');
//		});

		$('.learn').bind('click', function() {
			$(".study_list_main").hide(50);
		});

			$(".checkOut").click(function () {
				layer.open({
					title: ['温馨提示'],
					content: '<div style="color:#767676">确定要退出当前账号吗？</div>',
					btn: ['确定', '取消'],
					shadeClose: true,
					//回调函数
					yes: function (index, layero) {
						// self.location='http://www.baidu.com';//立即退出
						$.ajax({
							url: '/api/UserLogin/loginOut',
							dataType: 'json',
							//判断注册状态
							success: function (data) {
								if (data == 1) {
									layer.msg("退出成功", {
										icon: 1,//提示的样式
										time: 1000, //1秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
										end: function () {
											window.location.href = '/cms/index/studenlogin';
										}
									})
								} else {
									layer.msg(data)
								}
							}
						});
						//防止页面跳转
						return false;
					},
					btn2: function (index, layero) {
						layer.close(index);
					},
					cancel: function (index, layero) { //按右上角“X”按钮
						layer.close(index);
					},

				});
			});
		
		$('.editpass').click(function () {
				$(location).attr('href', '/examinee/center/my_center');
		})

	});
	$(document).ready(function() {
		$(".style_item").click(function() {
			$(".study_list_main").slideToggle();
			$('.jxjd-this').removeClass('jxjd-this');
			$(this).addClass('jxjd-this');
			$(".zhuan").toggleClass('iconzhuan');

		});
	});
//	$('.study_list').bind('click', function() {
//			$('.redcolor').removeClass('redcolor');
//			$(this).addClass('redcolor');
//		});
</script>