<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:100:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/admin\view\role\province.html";i:1545017009;s:91:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\common\view\layout\layout.html";i:1545212656;s:94:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\common\view\navigation\index.html";i:1545017016;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>角色管理</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">


    <link rel="stylesheet" type="text/css" href="/static/css/common/page.css" />
    <style>
        .layui-input-inline {
            width: 180px !important;
            padding-left: 10px;
        }

        .layui-card-header {
            padding-left: 0;
        }

        .layui-form-label {
            width: auto;
            padding: 9px 0 10px !important;
        }

        .layui-inline {
            margin-right: 0 !important;
        }
    </style>
    
<style>
    .lhj-content {
        width: 23%;
        float: left;
        padding: 10px 0 10px 10px;
    }

    .lhj-content a {
        width: 100%;
    }
</style>

</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">
            <img src="/static/img/logo.png" width="50" alt="">
        </div>
        <?php if(!(empty($count) || (($count instanceof \think\Collection || $count instanceof \think\Paginator ) && $count->isEmpty()))): ?>
        <ul class="layui-nav layui-layout-left" style="margin-left: 1459px;">
            <li class="layui-nav-item"><a href="/cms/notice/index">未读消息<span class="layui-badge"><?php echo $count; ?></span></a>
            </li>
        </ul>
        <?php endif; ?>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:void(0);"><?php echo \think\Session::get('adminuser.username'); ?></a>
                <dl class="layui-nav-child">
                    <!--<dd><a href="javascript:;" id="userInfo">基本信息</a></dd>-->
                    <dd><a href="javascript:;" id="checkOut">退出</a></dd>
                </dl>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree jx-left-nav" lay-filter="test">
                <?php if(is_array($menuData) || $menuData instanceof \think\Collection || $menuData instanceof \think\Paginator): $i = 0; $__LIST__ = $menuData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!(empty($vo['son']) || (($vo['son'] instanceof \think\Collection || $vo['son'] instanceof \think\Paginator ) && $vo['son']->isEmpty()))): ?>
                <li class="layui-nav-item">
                    <a class="" href="<?php echo $vo['url']=='#'?'javascript:void(0);':'/'.$vo['url']; ?>"><?php echo $vo['title']; ?></a>
                    <dl class="layui-nav-child">
                        <?php if(is_array($vo['son']) || $vo['son'] instanceof \think\Collection || $vo['son'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voo): $mod = ($i % 2 );++$i;?>
                        <dd><a class="check"
                               href="<?php echo $voo['url']=='#'?'javascript:void(0);':'/'.$voo['url']; ?>"><?php echo $voo['title']; ?></a></dd>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                </li>
                <?php else: ?>
                <li class="layui-nav-item"><a class="check" href='<?php echo $vo['url']==' #'?'javascript:void(0);':'/'.$vo['url']; ?>'><?php echo $vo['title']; ?></a></li>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>

    <div class="layui-body" style="background: green;">
        <!-- 内容主体区域 -->
        <div class="layui-tab-content">
            <div class="layui-card" style="padding: 15px;">
                <?php if(isset($Navigation)): ?>
                <span class="layui-breadcrumb" lay-separator="">
    <i class="layui-icon layui-icon-home"></i>
        <a href="/admin" class="county">
            首页
        </a>
</span>
<?php if(is_array($Navigation) || $Navigation instanceof \think\Collection || $Navigation instanceof \think\Paginator): $i = 0; $__LIST__ = $Navigation;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
<span class="layui-breadcrumb" lay-separator="">
    <i class="layui-icon">-</i>
        <a href="<?php echo $vo['url']; ?>" class="county">
            &nbsp;      <?php echo $vo['title']; ?>
        </a>
</span>
<?php endforeach; endif; else: echo "" ;endif; endif; ?>
                
<span class="layui-breadcrumb" lay-separator="">
  <a href="/admin" class="county">
    <i class="layui-icon layui-icon-home"></i>
    &nbsp;首页
  </a>
  <a><cite>省科室</cite></a>
</span>

                
<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
    <ul class="layui-tab-title">
        <li class="layui-this">当前</li>
        <li>市列表</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <div class="layui-form layui-card-header layui-inline" style="padding-bottom: 10px;">
                <form action="<?php echo request()->baseurl(); ?>" method="post">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">角色名称</label>
                            <div class="layui-input-inline">
                                <input type="text" name="name" autocomplete="off" placeholder="请输入"
                                       value="<?php echo \think\Session::get('search.name'); ?>" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layuiadmin-btn-useradmin" lay-submit>
                                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <button class="layui-btn role-add" data-value="<?php echo $cityId; ?>" style="float: right;margin-bottom: 5px;">创建角色
            </button>
            <table class="layui-table" lay-even>
                <colgroup>
                    <col width="5%">
                    <col>
                    <col>
                    <col>
                    <col width="185">
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <th>角色名称</th>
                    <th>创建时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $vo['name']; ?></td>
                    <td><?php echo $vo['create_time']; ?></td>
                    <td><?php echo $vo['status']; ?></td>
                    <td>
                        <?php if($vo['dataStatus']==1): ?>
                        <button data-value="<?php echo $vo['id']; ?>" data-status="0"
                                class="layui-btn layui-btn-danger layui-btn-xs role-status">禁用
                        </button>
                        <?php else: ?>
                        <button data-value="<?php echo $vo['id']; ?>" data-status="1" class="layui-btn layui-btn-xs role-status">启用
                        </button>
                        <?php endif; ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-normal layui-btn-xs role-edit">修改权限
                        </button>
                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>

                </tbody>
            </table>
            <div class="layui-fluid tp-pages">
                <?php echo $list->render(); ?>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="layui-tab-item">
            <fieldset class="layui-elem-field layui-field-title site-title">
                <legend><a name="grid">请选择对哪个市进行操作</a></legend>
            </fieldset>
            <div class="city-list layui-row layui-col-space15"></div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        <a href="http://www.etlchina.net">© www.etlchina.net - 博奥教育</a>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>
<script type="text/javascript" src="/static/js/admin/layout.js"></script>
<script type="text/javascript" src="/static/js/common/must.js"></script>
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.cookie.js"></script>
<script type="text/javascript">
    //点亮当前
    $(function () {
        $(".check[href='" + $.cookie('menu') + "']").addClass('layui-this');
    });
    layui.use(['layer', 'element', 'form', 'jquery'], function () {
        var element = layui.element, $ = layui.jquery, form = layui.form;
        $('.check').on('click', function () {
            var menu = $(this).attr('href');
            $.cookie('menu', menu, {expires: 1, path: '/admin'});


        });
        $("#userInfo").click(function () {
            layer.open({
                type: 2,
                anim: 1,
                skin: 'layui-layer-molv',
                area: ['60%', '60%'], //宽高
                title: "基本信息",
                content: "/admin/index/infopage",  //详细信息页面 修改详细信息
                yes: function (index, layero) {
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        $("#checkOut").click(function () {
            layer.open({
                title: ['温馨提示'],
                content: '<div style="color:#767676">确定要退出当前账号吗？</div>',
                btn: ['确定', '取消'],
                shadeClose: true,
                //回调函数
                yes: function (index, layero) {
                    $.ajax({
                        url: '/api/admin/loginOut',
                        dataType: 'json',
                        //判断注册状态
                        success: function (data) {
                            if (data == 1) {
                                layer.msg("退出成功", {
                                    icon: 1,//提示的样式
                                    time: 1000, //1秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                    end: function () {
                                        window.location.href = '/cms/index/admin';
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
    });
</script>

<script>
    layui.use(['layer', 'form'], function () {
        var $ = layui.jquery;
        //市列表
        $.ajax({
            type: "POST",
            url: "/api/office/citylist",
            dataType: "json",
            success: function (data) {
                var html = '';
                $.each(data, function (commentIndex, comment) {
                    html += '<div class="lhj-content layui-col-md3"><a href="/admin/role/city/cityId/' + comment['id'] + '" class="layui-btn">' + comment['name'] + '</a></div>';
                });
                $('.city-list').html(html);
            }
        });

        //禁用
        $('.role-status').on('click', function () {
            var id = $(this).data('value'), status = $(this).data('status');
            layer.confirm('是否确认该操作？', {btn: ['确定', '取消'], title: "提示"}, function () {
                $.post(urladdr.roleDisable, {'roleId': id, 'status': status}, function (res) {
                    if (res.code === 1) {
                        layer.msg(res.msg, {time: 500, icon: 1}, function () {
                            location.reload();
                        });
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                });
            });
        });
        //修改
        $(".role-edit").click(function () {
            var id = $(this).data('value');
            layer.open({
                type: 2,
                anim: 1,
                skin: 'layui-layer-molv',
                area: ['90%', '80%'], //宽高
                title: "修改权限",
                content: urladdr.editRole + id,  //调到新增页面
            });
        });
        //添加
        $(".role-add").click(function () {
            var id = $(this).data('value');
            layer.open({
                type: 2,
                anim: 1,
                skin: 'layui-layer-molv',
                area: ['90%', '80%'], //宽高
                title: "创建角色",
                content: urladdr.addRloe + id,  //调到新增页面
            });
        });
    });
</script>
