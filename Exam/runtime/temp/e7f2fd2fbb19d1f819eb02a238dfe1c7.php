<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:102:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\report\index.html";i:1545017022;s:86:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\learning\view\layout.html";i:1545017021;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>考题上报</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/static/css/common/page.css" />
    
<style>
    #centent {
        max-width: 110px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

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
            <form action="/learning/report/index" method="post">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">上报者IP</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_ip" value="<?php echo (isset($search['user_ip']) && ($search['user_ip'] !== '')?$search['user_ip']:''); ?>" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">审核状态</div>
                    <div class="layui-inline">
                        <select name="state" lay-filter="LAY-user-adminrole-type">
                            <option value="">全部状态</option>
                            <option <?php if(!(empty($search['state']) || (($search['state'] instanceof \think\Collection || $search['state'] instanceof \think\Paginator ) && $search['state']->isEmpty()))): if($search['state'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">待解决</option>
                            <option <?php if(!(empty($search['state']) || (($search['state'] instanceof \think\Collection || $search['state'] instanceof \think\Paginator ) && $search['state']->isEmpty()))): if($search['state'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">已解决</option>
                        </select>
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-primary reset">重置</button>
                        <button class="layui-btn" lay-submit>搜索</button>
                    </div>


                </div>
            </form>
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
                    <th>上报者IP</th>
                    <th>上报描述</th>
                    <th>创建时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $vo['user_ip']; ?></td>
                    <td id="centent"><?php echo $vo['centent']; ?></td>
                    <td><?php echo $vo['create_time']; ?></td>
                    <td>
                        <?php if($vo['state'] == '1'): ?>
                        未解决
                        <?php else: ?>
                        已解决
                        <?php endif; ?>
                    </td>
                    <td>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select <?php if($vo['state'] == '2'): ?> layui-disabled <?php else: ?> demo-state <?php endif; ?>">已解决</button>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select details">描述详情</button>
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
        var form = layui.form, $ = layui.jquery;
        $(".details").click(function () {
            var id = $(this).data('value');
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "详情页",
                content: "/learning/report/detail/id/"+id,  //调到新增页面
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }

            });
        });
        $(".demo-state").click(function () {
            var id = $(this).data('value');
            layer.open({
                title: ['温馨提示'],
                content: '<div style="color:#767676">确定要改变当前数据状态吗？</div>',
                btn: ['确定', '取消'],
                shadeClose: true,
                yes: function(index, layero){
                    $.ajax({
                        url:'/api/learningReport/state',
                        type:'post',
                        dataType:'json',
                        data:{
                            id:id,
                            state:'2',
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
        $(".reset").click(function () {
            $.each($('form input'),function (index,item) {
                $(item).val('');
            });
            return false;
        });
    });


</script>

</html>
