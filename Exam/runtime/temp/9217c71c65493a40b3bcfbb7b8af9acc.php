<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:99:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/cms\view\examinee\login.html";i:1545017011;s:90:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\cms\view\content\daohang.html";i:1545183598;s:87:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\cms\view\content\foot.html";i:1545017011;}*/ ?>
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
<div class="layui-container jigouliucheng_main"  style=" height:438px;">
    <div class="layui-col-xs8">
        <div class="JiGouLC">
            <h2 class="JiGouLC_title">工作流程图</h2>
            <img src="/static/layui/img/SheKaoLC.png" />
        </div>
    </div>
    <div class="layui-col-xs4">
        <div class="login_conent SKlogin_conent" style=" height:339px;">
            <h2>社会考生登录</h2>
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">证件类型：</label>
                    <div class="layui-input-block">
                        <select  name="id_type" id="type" lay-filter="type" required  lay-verify="required">
                            <option value="1">身份证</option>
                            <option value="2">护照</option>
                            <option value="3">军官证</option>
                            <option value="4" >港澳台证</option>
                            <option value="5" >其他</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">证件号：</label>
                    <div class="layui-input-block">
                        <input type="text"  id="id_no" name="id_no" required  lay-verify="required" placeholder="请输入证件号" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">密&nbsp;&nbsp;码：</label>
                    <div class="layui-input-block">
                        <input type="password" id="name" name="name" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <!--<div class="layui-form-item">-->
                <!--<label class="layui-form-label">验证码：</label>-->
                <!--<div class="layui-input-block">-->
                <!--<input type="text" name="yzm" name="code"   lay-verify="code" autocomplete="off" placeholder="请输入验证码" class="layui-input yzm_input">-->
                <!--<img id="yzmimg" title="点击更换" src="/captcha.html?seed=<?=mt_rand()?>" onclick="this.src='{<:captcha_src()>}?seed='+Math.random()"  />-->
                <!--</div>-->
                <!--</div>-->
                <div class="layui-form-item">
                    <label class="layui-form-label">验证码：</label>
                    <div class="layui-input-block">
                        <!--<img class="yzmimg" src="/static/layui/img/yanzm.png" />-->
                        <input type="text" name="yzm" id="yzm" lay-verify="code" autocomplete="off" placeholder="请输入验证码" class="layui-input yzm_input">

                        <img  id="verifycode_imgq" class="verifycode_imgq" style="width: 80px;height: 37px" onclick="this.src='<?php echo captcha_src(); ?>?'+Math.random()" src="<?php echo captcha_src(); ?>" alt="captcha" />
                    </div>
                </div>


                <div class="denglu_btn" lay-submit lay-filter="sub">登录</div>
                <p><a href="/cms/index/signature" class="fl">注册</a></p>
                <div class="SKzhaomi"><span><a href="/cms/index/forgetpass" class="fr">找回密码</a></span><span class="shu">|</span><span><a href="#baomingxuzhi">报名须知</a></span></div>
            </form>
        </div>
    </div>
</div>
<br>
<br>
<div class="layui-container xuzhi" id="baomingxuzhi" name="baomingxuzhi">
    <h2>报名须知：</h2>
    <p>欢迎您使用江西省职业技能鉴定考试网上报名系统，请务必认真阅读以下事项：</p>
    <p>一、考生不得以他人身份、他人的照片进行报考，否则由此引起的纠纷，由考生承担全部责任。</p>
    <p>二、本系统以身份证号及报名序号作为登录的依据，报考人员请保管好自己的身份证号和报名序号，否则他人误用造成的后果，本网站不承担任何责任。</p>
    <p>三、网上报名基本操作程序如下：</p>
    <p>第一步，考生仔细阅读本须知及相关公告后，点击“注册报名”，进入报名登记系统。</p>
    <p>第二步，请按照报名登记表内容如实、准确填写，填写内容要求能够较全面的反映报考要求。</p>
    <p>第三步，正确上传本人的电子照片。</p>
    <p>照片上传说明:</p>
    <p>①上传电子版照片为近期免冠正面证件照，格式为jpg，大小为40K以下，宽度90像素，高度120像素，分辨率300像素。</p>
    <p>②上传照片必须能反映本人特征。</p>
    <p>③获得电子版照片的途径：</p>
    <p>A.拥有扫描仪的报考人员，可以通过扫描照片（照片原件大小：小2寸，扫描比例：1：1，扫描模式：256色以上）；</p>
    <p>B.拥有数码相机的报考人员，可以通过数码拍摄；</p>
    <p>C.直接到照相馆拍摄电子版照片，并让工作人员按要求帮助处理。</p>
    <p>第四步，提交审查。</p>
    <p>第五步，资格审查合格后，立即缴费。</p>
    <p>四、准考证打印。考生缴费成功后请于规定时间内登录本网站自行打印准考证。打印准考证一律使用A4规格纸张打印。</p>
</div>
<footer class="footer">
    <img class="quanwei" src="/static/img/cms1.0/quanwei.png" />
    <p>主办单位：江西省职业技能鉴定指导中心 技术支持：北京博奥网络教育科技股份有限公司</p>
    <p>网上支持（职业资格问答） 　　您是第 9027267 位访问者</p>
</footer>
</body>

</html>


<script src="/static/layui/layui.js"></script>
<script type="text/javascript">
    layui.use(['form', 'layer','jquery'], function () {
        var form  = layui.form;
        var $  = layui.jquery;

        var name = $("#name").val();
        var id_no = $("#id_no").val();

        $.get('common/view/content/daohang.html', function(data) {
            $(".daohang").html(data);
        });



        // 为密码添加正则验证
        $('#name').blur(function() {
            var reg = /^[\w]{8,18}$/;
            if(!($('#name').val().match(reg))){
                layer.msg('请重新输入密码,密码是8到18位，且不能出现空格!');
            }
            return false;
        });

        //验证证件号
        $('#id_no').blur(function() {
            var data=$("#type").val();
            if(data=='1'){
                var reg =  /(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                if(!($('#id_no').val().match(reg))){
                    layer.msg('请输入合法身份证!');
                }
            }else if(data=='2') {
                var reg = /^[A-Z][0-9]{8}$/;
                if (!($('#id_no').val().match(reg))) {
                    layer.msg('请输入合法护照!');
                }
            }else if(data=='3'){
                var reg = /^[\u4e00-\u9fa5]{2}[0-9]{6,8}$/;
                if(!($('#id_no').val().match(reg))){
                    layer.msg('请输入合法军官证!');
                }
            }else if(data=='4'){
                var reg = /^[A-Z][0-9]{8}$/;
                if(!($('#id_no').val().match(reg))){
                    layer.msg('请输入合法港澳通行证!');
                }
            }
            return false;
        });

        //添加表单监听事件
        form.on('submit(sub)',function () {
            // alert(1);
            var  id_type = $('#type').val();
            var  id_no = $('#id_no').val();
            var  name = $('#name').val();
            var  yzm = $('#yzm').val();
            if (id_no == ''){
                layer.msg('请重新输入证件号');
                return false;
            }
            if (name == ''){
                layer.msg('请重新输入密码');
                return false;
            }
            if (yzm == ''){
                layer.msg('请输入验证码');
                return false;
            }

            $.ajax({
                url:'/api/UserLogin/loginAction',
                type:'post',
                data:{
                    'yzm' : yzm,
                    'id_card':id_no,
                    'password':name,
                    'id_type':id_type,
                },
                dataType:'json',
                //判断登录状态
                success:function (data) {
                    if (data.code==1){
                        layer.msg(data.msg, {
                            icon: 1,//提示的样式
                            time: 2000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                            end: function () {
                                if(location.search.indexOf('back')>-1)
                                {
                                    location.href = decodeURIComponent(location.search.substring(6));
                                }else
                                {
                                    location.href = '/examinee/menu/index';
                                }

                            }
                        })
                    } else if (data.code == -2) {
                        layer.msg(data.msg);
                        $("#verifycode_imgq").click();
                        $('#yzm').val("").focus();
                    }else{
                        layer.msg(data.msg);
                        $("#verifycode_imgq").click();
                        $('#yzm').val("").focus();
                    }

                },error:function () {
                    $("#verifycode_imgq").click();
                    alert('网络错误');
                }
            });
            //防止页面跳转
            return false;
        });
    });

</script>