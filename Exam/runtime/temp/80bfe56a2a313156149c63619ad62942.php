<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:111:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\topic\official_choose.html";i:1545017021;s:86:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\learning\view\layout.html";i:1545017021;}*/ ?>
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
        
<div class="layui-card">
    <form class="layui-form" action="" lay-filter="component-form-group">
        <div class="layui-card-header">题库管理 > 筛选条件</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label">工种：</label>
                <div class="layui-input-block">
                    <select name="work" id="work" lay-filter="work">
                        <option value="">请选择工种</option>
                        <?php if(is_array($work) || $work instanceof \think\Collection || $work instanceof \think\Paginator): $i = 0; $__LIST__ = $work;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">方向：</label>
                <div class="layui-input-block">
                    <select name="direction" id="direction" lay-filter="direction">
                        <option value="">请选择方向</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">级别：</label>
                <div class="layui-input-block">
                    <select name="level" id="level" lay-filter="level">
                        <option value="">请选择级别</option>
                        <option value="1">高级技师</option>
                        <option value="2">技师</option>
                        <option value="3">高级</option>
                        <option value="4">中级</option>
                        <option value="5">初级</option>
                    </select>
                </div>
            </div>

        </div>
            <div class="layui-input-block" style="margin-left: 974px;">
                <button type="submit" class="layui-btn layui-carousel-right" lay-submit lay-filter="formSubmit">下一步</button>
            </div>
    </form>
</div>

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

        //根据工种展示方向
        form.on('select(work)', function(data){
            var work_type = data.value;

            $.ajax({
                url:'/api/workDirection/selectWorkId',
                type:'post',
                dataType:'json',
                data:{
                    id:work_type,
                },
                success:function (data) {

                    if (data.code==1){
                        //1.清空已设置标签
                        $("#direction").html("");
                        //2.获取select标签
                        var work = document.getElementById ("direction");
                        for (var i=0;i<data.data.length;i++){
                            //3.创建option标签
                            var option = document.createElement("option");
                            //4.设置option属性，值
                            option.value = data.data[i]['id'];
                            option.innerText = data.data[i]['name'];
                            //5.select内部插入option
                            work.append(option);
                            //6.渲染标签
                            form.render();
                        }
                    }else{
                        $("#direction").html("");
                        form.render();
                        layer.msg(data.msg)
                    }
                }
            });
            //防止页面跳转
            return false;
        });

        //添加表单监听事件
        form.on('submit(formSubmit)', function (data) {
            data=data.field;

            //验证规则
            if (data.work == '') {
                layer.msg('请至少选择一个工种!');
                return false;
            }
            if (data.level == '') {
                layer.msg('请至少选择一个级别!');
                return false;
            }

            $.ajax({
                url:'/api/LearningTopicOfficial/selectLevel',
                type:'post',
                data:data,
                dataType:'json',
                success:function (data) {

                    if (data.code==1){
                        window.parent.location.href='/learning/topic/official?work=' + encodeURI(data.data.work_name) + '&level='+ encodeURI(data.data.level_name)+ '&direction='+ encodeURI(data.data.direction_name);
                    }else{
                        layer.msg(data.msg);
                    }
                }
            });
            //防止页面跳转
            return false;
        });
    });
</script>

</html>
