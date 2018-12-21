<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:105:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\set_volume\show.html";i:1545026984;s:86:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\learning\view\layout.html";i:1545017021;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>试卷列表</title>
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
        
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form">
                <div class="layui-form-item">
                    <form action="/learning/set_volume/show" method="post">
                        <div class="layui-form-item">
                            <label class="layui-form-label">搜索条件</label>
                            <div class="layui-input-block">
                                <div class="layui-inline">
                                    <select name="work" id="work" lay-filter="work">
                                        <option value="">请选择工种</option>
                                        <?php if(is_array($work) || $work instanceof \think\Collection || $work instanceof \think\Paginator): $i = 0; $__LIST__ = $work;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option <?php if(!(empty($search['work']) || (($search['work'] instanceof \think\Collection || $search['work'] instanceof \think\Paginator ) && $search['work']->isEmpty()))): if($search['work'] == $vo['id']): ?> selected="selected" <?php endif; endif; ?> value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <select name="direction" id="direction" lay-filter="direction">
                                        <option value="">请选择方向</option>
                                        <?php if(is_array($direction) || $direction instanceof \think\Collection || $direction instanceof \think\Paginator): $i = 0; $__LIST__ = $direction;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option <?php if(!(empty($search['direction']) || (($search['direction'] instanceof \think\Collection || $search['direction'] instanceof \think\Paginator ) && $search['direction']->isEmpty()))): if($search['direction'] == $vo['id']): ?> selected="selected" <?php endif; endif; ?> value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <select name="level" id="level" lay-filter="level">
                                        <option value="">请选择级别</option>
                                        <option <?php if(!(empty($search['level']) || (($search['level'] instanceof \think\Collection || $search['level'] instanceof \think\Paginator ) && $search['level']->isEmpty()))): if($search['level'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">高级技师</option>
                                        <option <?php if(!(empty($search['level']) || (($search['level'] instanceof \think\Collection || $search['level'] instanceof \think\Paginator ) && $search['level']->isEmpty()))): if($search['level'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">技师</option>
                                        <option <?php if(!(empty($search['level']) || (($search['level'] instanceof \think\Collection || $search['level'] instanceof \think\Paginator ) && $search['level']->isEmpty()))): if($search['level'] == 3): ?> selected="selected" <?php endif; endif; ?> value="3">高级</option>
                                        <option <?php if(!(empty($search['level']) || (($search['level'] instanceof \think\Collection || $search['level'] instanceof \think\Paginator ) && $search['level']->isEmpty()))): if($search['level'] == 4): ?> selected="selected" <?php endif; endif; ?> value="4">中级</option>
                                        <option <?php if(!(empty($search['level']) || (($search['level'] instanceof \think\Collection || $search['level'] instanceof \think\Paginator ) && $search['level']->isEmpty()))): if($search['level'] == 5): ?> selected="selected" <?php endif; endif; ?> value="5">初级</option>
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn" lay-submit>搜索</button>
                                    <button class="layui-btn layui-btn-danger volume">组卷</button>
                                    <button class="layui-btn layui-btn-danger oneChone">选择组卷</button>
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th width="5%"></th>
                    <th>工种</th>
                    <th>级别</th>
                    <th>方向</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $vo['work_name']; ?></td>
                    <td><?php echo $vo->level_id; ?></td>
                    <td><?php echo $vo['work_direction_name']; ?></td>
                    <td><?php echo $vo['create_time']; ?></td>
                    <td>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select details">详情</button>
                        <button data-value="<?php echo $vo['id']; ?>" data-name="<?php echo $vo['work_name']; ?>-<?php echo $vo->level_id; ?>-<?php echo $vo['work_name']; ?>" class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>
                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
            <?php if(count($data)<=0): ?>
            <div style="margin: 0 auto;width: 120px;font-size: 20px;">
                <div class="layui-form-mid layui-word-aux">暂无数据！</div>
                <?php else: ?>
                <div class="layui-fluid tp-pages">
                    <?php echo $data->render(); ?>
                </div>
                <?php endif; ?>
        </div>
        </div>
    </div>
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

<script type="text/javascript">

    layui.use(['form', 'layer','jquery'], function () {
        var form  = layui.form, $  = layui.jquery;

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

        //一键组卷
        $(".volume").click(function () {
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "模考组卷",
                content: "/learning/topic/volume",  //调到新增页面
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }

            });
            return false;
        });
        //选择组卷
        $(".oneChone").click(function () {
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "选择组卷",
                content: "/learning/topic/oneChone",  //调到新增页面
                yes: function(index){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
            return false;
        });
        //删除
        $(".demo-delete").click(function () {
            var $=layui.jquery,id = $(this).data('value'),name = $(this).data('name');
            layer.open({
                title: ['温馨提示'],
                content: '确定要删除: <span style="color: red;">'+ name +'</span>的试卷吗？',
                btn: ['确定', '取消'],
                shadeClose: true,
                yes: function(index, layero){
                    $.ajax({
                        url:'/api/LearningSetVolume/delete',
                        type:'post',
                        dataType:'json',
                        data:{
                            id:id,
                        },
                        success:function (data) {
                            if (data.status==1){
                                layer.msg(data.msg, {
                                    icon: 1,
                                    time: 1000,
                                    end: function () {
                                        location.reload();
                                    }
                                })
                            }else{
                                layer.msg(data.msg)
                            }
                        }
                    });
                    return false;
                },
                btn2: function(index, layero){
                    layer.close(index);
                },
                cancel: function(index,layero){
                    layer.close(index);
                },

            });
        });
        //试卷详情页
        $(".details").click(function () {
            var $=layui.jquery,id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "选项题",
                content: "/learning/set_volume/details?id=" + id,  //调到新增页面
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }

            });
        });
        //重置按钮
        $(".reset").click(function () {
            $.each($('form input'),function (index,item) {
                $(item).val('');
            });
            return false;
        });
    });


</script>

</html>
