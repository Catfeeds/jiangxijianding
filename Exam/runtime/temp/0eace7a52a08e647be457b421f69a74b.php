<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:100:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/cms\view\index\category1.html";i:1545017013;s:90:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\cms\view\content\daohang.html";i:1545183598;s:87:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\cms\view\content\foot.html";i:1545017011;}*/ ?>
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

<div class="layui-main WangShangBaoMing">
    <style type="text/css">
        .wen{
            overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: inline-block; width: 700px;

        }
    </style>
    <!--报名左tab边栏-->
    <div class="layui-col-xs3">
        <div class="WangShangBaoMing_left">
            <div class="jieshao_header">
                <span class="jieshao_header_left marginleft" ><?php echo $data['title']; ?></span>
            </div>
            <ul class="wangbao_list">
                <li class="baoming" name="dian"  value="<?php echo $data['id']; ?>" >
                   <?php echo $data['guide_name']; ?>
                </li>
                <?php if(is_array($info) || $info instanceof \think\Collection || $info instanceof \think\Paginator): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li name="dian" value="<?php echo $v['id']; ?>" class="dianji<?php echo $v['id']; ?>">
                    <?php echo $v['guide_name']; ?>
                </li>
              <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <div class="layui-col-xs9">
        <!--报名右边tab内容-->

        <div class="WangShangBaoMing_right">
            <ul class="wangbao_mains">
                <li>
                    <div class="wangbao_main">
                        <div class="wangbao_breadcrumb">
                            <span class="wangbao_header" id="dian"><?php echo $data['guide_name']; ?></span>
                            <span class="layui-breadcrumb" lay-separator=">">
								  		<a href="/">首页</a>
								  		<a><cite class="dian"><?php echo $data['guide_name']; ?></cite></a>
									</span>
                        </div>
                        <ul id="aaa" style="height: auto;">

                        </ul>
                    </div>
                </li>








</ul>
            <div id="fen">     <input type="hidden" id="count" value="<?php echo $infos['count']; ?>">
                <input type="hidden" id="id" value="<?php echo $infos['id']; ?>"></div>
            <div id="fenye" class="wangbao_fenye">

            </div>
        </div>
    </div>
</div>


<footer class="footer">
    <img class="quanwei" src="/static/img/cms1.0/quanwei.png" />
    <p>主办单位：江西省职业技能鉴定指导中心 技术支持：北京博奥网络教育科技股份有限公司</p>
    <p>网上支持（职业资格问答） 　　您是第 9027267 位访问者</p>
</footer>
</body>

</html>

<script src="/static/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/layui/layui.all.js" type="text/javascript" charset="utf-8"></script>


<script type="text/javascript">

    layui.use(['laypage', 'layer','jquery'], function() {

        var laypage = layui.laypage,
            layer = layui.layer;
        var $ = layui.jquery;
        var count = $('#count').val();
        var id = $('#id').val();

        laypage.render({
            elem: 'fenye',
            count:count,
            theme: '#CD2D36',
            layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
            jump: function(obj) {
                var pag = obj.curr;
                var limit=obj.limit;
                //console.log(pag);console.log(limit)
                $.post('category',{id:id,pag:pag,limit:limit},function(data)
                {
                    var html ='';
                    for(var i=0;i<data.length;i++)
                    {
                        html+='<li><a class="wen" href="'+data[i].url+'">';
                        html+='<i></i>'+data[i].title+'</a><span class="rightfloat">'+data[i].time+'</span>';
                        html+='</li>'
                    }
                    $('#aaa').html(html);
                })


            }
        });
    });

    function paged(data)
    {
        layui.use(['laypage', 'layer','jquery'], function() {
            var laypage = layui.laypage,
                layer = layui.layer;
            //
            var count = data.count;
            var di = data.id;

            laypage.render({
                elem: 'fenye',
                count:count,
                theme: '#CD2D36',
                layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
                jump: function(obj) {
                    var pag = obj.curr;
                    var limit=obj.limit;
                  //  console.log(pag);console.log(limit)
                    $.post('category',{id:di,pag:pag,limit:limit},function(data)
                    {
                        var html ='';
                        for(var i=0;i<data.length;i++)
                        {
                            html+='<li><a class="wen"  href="'+data[i].url+'">';
                            html+='<i></i>'+data[i].title+'</a><span class="rightfloat">'+data[i].time+'</span>';
                            html+='</li>'
                        }
                        $('#aaa').html(html);
                    })
                }
            });
        });
    }
    layui.use('jquery',function(){
        var $ = layui.jquery;
        $("li[name=dian]").click(function(){
            var id = $(this).attr('value');
            console.log(id);
            var title = $(this).text();
            $('.dian').html(title);
            $('#dian').html(title);
          $.post('about',{id:id},function(data)
            {

                paged(data);
            })
        });


    });


    $(function() {
        var wangbao_list = $('.wangbao_list li');
        var wangbao_mains = $('.wangbao_mains>li');
        var ZhuanJiaImg = $('.zhuanjiaImg')
        wangbao_list.click(function() {
            $(this).addClass('baoming').siblings().removeClass('baoming');
            var h = $(this).index();
            wangbao_mains.eq(h).show().siblings().hide();
        });
    });
</script>