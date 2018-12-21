<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:96:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/cms\view\index\admin.html";i:1545183599;s:90:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\cms\view\content\daohang.html";i:1545183598;s:87:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\cms\view\content\foot.html";i:1545017011;}*/ ?>
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
<body class="login">
<div class="login_main">
    <div class="layui-container">
        <form class="layui-form">
            <div class="login_conent">
                <h2>鉴定工作者登录</h2>
                <div class="layui-form-item">
                    <label class="layui-form-label">组织代码：</label>
                    <div class="layui-input-block">
                        <input type="text" id="username" name="username" lay-verify="required" autocomplete="off"
                               placeholder="请输入组织代码" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号码：</label>
                    <div class="layui-input-block">
                        <input type="text" maxlength="11" name="phone" id="phone" lay-verify="phone" autocomplete="off"
                               placeholder="请输入手机号码" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">附加码：</label>
                    <div class="layui-input-block">
                        <input type="text" id="code" name="code" lay-verify="required" autocomplete="off"
                               placeholder="请输入验证码" class="layui-input yzm_input">
                        <img class="yzmimg" title="点击刷新" onclick="this.src='<?php echo captcha_src(); ?>?'+Math.random()"
                             src="<?php echo captcha_src(); ?>" alt="captcha"/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">短信验证码：</label>
                    <div class="layui-input-block">
                        <input type="text" name="sms" lay-verify="required" autocomplete="off" placeholder="请获取验证码"
                               class="layui-input yzm_input">
                        <button id="getSms" class="layui-btn get-code">获取验证码</button>
                    </div>
                </div>
                <div class="denglu_btn" lay-submit lay-filter="sub">登录</div>
            </div>
        </form>
    </div>
</div>
<footer class="footer">
    <img class="quanwei" src="/static/img/cms1.0/quanwei.png" />
    <p>主办单位：江西省职业技能鉴定指导中心 技术支持：北京博奥网络教育科技股份有限公司</p>
    <p>网上支持（职业资格问答） 　　您是第 9027267 位访问者</p>
</footer>
</body>

<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript">

    //发送验证码


    //====emd=====

    layui.use('form', function () {
        var form = layui.form, $ = layui.jquery, username = $("#username"), phone = $('#phone'), code = $('#code'),
            icon;

        //发送验证码
        $('.get-code').on('click', function () {
            //组织代码
            if ($.trim($(username).val()) === '') {
                layer.msg('组织代码不能为空', {icon: 5});
                $(username).addClass('layui-form-danger').trigger('focus');
                return false;
            }
            //手机号
            if ($.trim($(phone).val()) === '') {
                layer.msg('手机号码不能为空', {icon: 5});
                $(phone).addClass('layui-form-danger').trigger('focus');
                return false;
            } else {
                var pattern = /^1[3456789]\d{9}$/;
                if (pattern.test($.trim($(phone).val())) === false) {
                    layer.msg('手机号码格式不对', {icon: 5});
                    $(phone).addClass('layui-form-danger').trigger('focus');
                    return false;
                }
            }
            //附加码
            if ($.trim($(code).val()) === '') {
                layer.msg('附加码不能为空', {icon: 5});
                $(code).addClass('layui-form-danger').trigger('focus');
                return false;
            }
            //发送代码,手机号,附加码
            $.post(urladdr.sendMsg, {
                username: $(username).val(),
                phone: $(phone).val(),
                code: $(code).val()
            }, function (data) {
                if (data.code > 0) {
                    icon = 1;
                    //----start-------
                    var countdown = 60;
                    var obj = $("#getSms");
                    settime(obj);

                    function settime(obj) { //发送验证码倒计时
                        if (countdown == 0) {
                            obj.attr('disabled', false).removeClass('layui-btn-disabled');
                            //obj.removeattr("disabled");
                            obj.html("获取验证码");
                            countdown = 60;
                            return;
                        } else {
                            obj.attr('disabled', true).addClass('layui-btn-disabled');
                            obj.html("重发(" + countdown + ")");
                            countdown--;
                        }
                        setTimeout(function () {
                                settime(obj)
                            }
                            , 1000)
                    }

                    //----end---
                } else {
                    icon = 5;
                    $('.yzmimg').trigger('click');
                }
                layer.msg(data.msg, {icon: icon})
            });
            //阻止按钮提交
            return false;
        });


        //添加表单监听事件
        form.on('submit(sub)', function (data) {
            data = data.field;
            $.post(urladdr.dologin, data, function (data) {
                if (data.code === 1) {
                    layer.msg(data.msg, {
                        icon: 1, time: 2000, end: function () {
                            location.href = urladdr.index;
                        }
                    });
                } else {
                    layer.msg(data.msg, {icon: 5});
                    $('.yzmimg').trigger('click');
                }
            });
            return false;
        });
    });

</script>