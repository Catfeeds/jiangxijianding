<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:103:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\topic\options.html";i:1545017021;s:86:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\learning\view\layout.html";i:1545017021;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title></title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/static/css/common/page.css" />
    

</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">在线学习平台</div>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item"><a href="/learning/index/index">控制台</a></li>
        </ul>
        <ul class="layui-nav layui-layout-right">

            <li class="layui-nav-item">
                <a href="">个人中心<span class="layui-badge-dot"></span></a>
            </li>
            <li class="layui-nav-item">
                <a href=""><?php echo \think\Session::get('adminuser.username'); ?></a>
                <dl class="layui-nav-child">
                    <dd><a href="/learning/index/my_center" id="updatePwd">修改密码</a></dd>
                    <dd><a href="javascript:;" id="userInfo">基本信息</a></dd>
                    <dd><a href="javascript:;" id="checkOut">退出</a></dd>
                </dl>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <li class="layui-nav-item">

                    <a class="" href="<?php echo url('/learning/media/index'); ?>"><i class="layui-icon layui-icon-read"></i> 课件管理</a>
                </li>
                <li class="layui-nav-item">
                    <a class="" href="<?php echo url('/learning/topic/options'); ?>"><i class="layui-icon layui-icon-file-b"></i> 题库管理</a>
                </li>
                <!--<li class="layui-nav-item">-->
                    <!--<a class="" href="<?php echo url('/learning/set_volume/index?type=1'); ?>"><i class="layui-icon layui-icon-survey"></i> 在线组卷</a>-->
                <!--</li>-->
                <li class="layui-nav-item">
                    <a class="" href="<?php echo url('/learning/set_volume/show'); ?>"><i class="layui-icon layui-icon-list"></i> 试卷列表</a>
                </li>
                <li class="layui-nav-item">
                    <a class="" href="<?php echo url('/learning/report/index'); ?>"><i class="layui-icon layui-icon-help"></i> 问题上报</a>
                </li>
                <li class="layui-nav-item">
                    <a class="" href="<?php echo url('/learning/history/index?role=0'); ?>"><i class="layui-icon layui-icon-log">考试记录</i></a>
                </li>
                <!--<li class="layui-nav-item">-->
                    <!--<a class="" href="<?php echo url('/learning/jury/index'); ?>"><i class="layui-icon layui-icon-friends"></i>考评员考核</a>-->
                <!--</li>-->
                <!--<li class="layui-nav-item">-->
                    <!--<a class="" href="<?php echo url('/learning/record/index'); ?>"><i class="layui-icon layui-icon-log"></i>考评员学习记录</a>-->
                <!--</li>-->

            </ul>
        </div>
    </div>

    <div class="layui-body">
        <!-- 内容主体区域 -->
        
<p class="alone-download-btn">
    <a href="<?php echo url('/learning/topic/officialChoose'); ?>" class="layui-btn layui-btn-primary alone-download-right" style="margin-top: 20%;margin-left: 33%;">
        考&nbsp;&nbsp;&nbsp;&nbsp;生
    </a>
    <button class="layui-btn layui-btn-primary alone-download-right reset" style="margin-top: 20%;margin-left: 18%;">
        考评人员
    </button>
</p>

        <!--<div style="padding: 15px;">内容主体区域</div>-->
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        <!--© layui.com - 底部固定区域-->
    </div>
</div>
</body>
<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>
<script>

    layui.use('element', function(){
        var element = layui.element;
    });
</script>

<script>
    layui.use(['form', 'layer', 'jquery'], function () {
        var form = layui.form, $ = layui.jquery;
        $(".reset").click(function () {

            $.ajax({
                url:'/api/LearningTopicOfficial/selectLevel',
                type:'post',
                data:{
                    work: 0,
                    direction: 0,
                    level: 0
                },
                dataType:'json',
                success:function (data) {

                    if (data.code==1){
                        window.parent.location.href='/learning/topic/official?work=0&level=0&direction=0';
                    }else{
                        layer.msg(data.msg);
                    }
                }
            });
            return false;
        });
        });
</script>

</html>
