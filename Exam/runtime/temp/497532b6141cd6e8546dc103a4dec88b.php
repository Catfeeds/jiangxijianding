<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:102:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/admin\view\exam_plan\index.html";i:1545183594;s:91:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\common\view\layout\layout.html";i:1545212656;s:94:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\common\view\navigation\index.html";i:1545017016;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php if($pagetpye == 1): ?>职业资格鉴定<?php endif; if($pagetpye == 2): ?>竞赛<?php endif; if($pagetpye == 3): ?>考评人员<?php endif; if($pagetpye == 4): ?>技师&高级技师<?php endif; ?></title>
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
    .layui-form-label {
        width: 70px !important;
        padding: 9px 15px 10px 0 !important;
    }
    .layui-inline {
        margin-right: 0 !important;
    }
    .title {
        display: block;
        width: 200px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
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
  <a><cite>鉴定计划管理</cite></a>
    <?php if($pagetpye == 1): ?><a><cite>职业资格鉴定</cite></a><?php endif; if($pagetpye == 2): ?><a><cite>竞赛</cite></a><?php endif; if($pagetpye == 3): ?><a><cite>考评人员</cite></a><?php endif; if($pagetpye == 4): ?><a><cite>技师&高级技师</cite></a><?php endif; ?>
</span>

                
        <div class="layui-form layui-card-header layuiadmin-card-header-auto" style="padding-top: 15px;height: auto;">
            <div class="layui-form-item">
                <div class="layui-inline" style="float: right;">
                    <input type="hidden" id="pagetpye" value="<?php echo $pagetpye; ?>">
                    <button class="layui-btn" id="addExamplan">添加鉴定计划</button>
                </div>

                <form action="/admin/ExamPlan/<?php if($pagetpye==1): ?>qualification<?php endif; if($pagetpye==2): ?>competition<?php endif; if($pagetpye==3): ?>juryStaff<?php endif; if($pagetpye==4): ?>technician<?php endif; ?>" method="post">
                <div class="layui-inline">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" id="titleSea" sub="<?php echo $map['title']; ?>" value="<?php echo $map['title']; ?>" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                    <input type="hidden" name="pagetpye" value="<?php echo $pagetpye; ?>">
                </div>
                    <div class="layui-inline">
                    <label class="layui-form-label">工种类型</label>
                    <div class="layui-input-block">
                        <select name="worktype" id="workTypeSea" lay-search>
                            <option value="">全部</option>
                            <?php if(is_array($type) || $type instanceof \think\Collection || $type instanceof \think\Paginator): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['id']; ?>" <?php if($map['worktype']== $vo['id']): ?> selected <?php endif; ?> ><?php echo $vo['name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        <!--<input type="text" name="worktype" id="workTypeSea" sub="<?php echo $map['worktype']; ?>" value="<?php echo $map['worktype']; ?>" placeholder="请输入" autocomplete="off" class="layui-input">-->
                    </div>
                </div>
                <div class="layui-inline">
                    <input type="submit" class="layui-btn layuiadmin-btn-order" value="搜索" id="searchBtn">
            </div>
                </form>
            </div>
        </div>
            <table class="layui-table">
                <thead>
                <tr>
                    <th width="40px">编号</th>
                    <th width="200px">标题</th>
                    <th width="50px">工种类型</th>
                    <th width="100px">考试时间</th>
                    <th width="50px">状态</th>
                    <th width="240px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($arrExamPlan) || $arrExamPlan instanceof \think\Collection || $arrExamPlan instanceof \think\Paginator): $i = 0; $__LIST__ = $arrExamPlan;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td id="id"><?php echo $i; ?></td>
                    <td><a  class="title" href="#" title="<?php echo $vo['title']; ?>"><?php echo $vo['title']; ?></a></td>
                    <td><?php echo $vo['work_type_name']; ?></td>
                    <td><?php echo $vo['exam_time']; ?></td>
                    <td><?php if(($vo['status']==1)): ?>已发布<?php else: ?>
                        <button exam_plan_id="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-sm release">
                            发布
                        </button>
                        <?php endif; ?></td>
                    <td>
                        <button exam_plan_id="<?php echo $vo['id']; ?>" <?php echo $now; ?> class="layui-btn layui-btn-sm <?php if(($vo['exam_time']>=$now || $vo['status']!=1)): ?>edit<?php endif; ?>"<?php if(($vo['exam_time']<$now && $vo['status']==1)): ?> style='background-color: #ff5722' <?php endif; ?>>
                            <i class="layui-icon">&#xe642;</i>
                        </button>
                        <button exam_plan_id="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-sm <?php if(($vo['status']==1)): ?> layui-btn-disabled <?php else: ?>editWork<?php endif; ?>"<?php if(($vo['status']==1)): ?> style='background-color: #ff5722' <?php endif; ?>>
                            工种
                        </button>
                         <button exam_plan_id="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-sm <?php if(($vo['status']==1)): ?> layui-btn-disabled <?php else: ?> delete <?php endif; ?>"  <?php if(($vo['status']==1)): ?> style='background-color: #ff5722' <?php endif; ?>><i class="layui-icon">&#xe640;</i>
                        </button>
                         <button exam_plan_id="<?php echo $vo['id']; ?>" {if ($vo.status==1)} class="layui-btn layui-btn-sm detil">
                            详情
                        </button>
                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
    <div class="layui-form-item">

    <div class="layui-fluid tp-pages">
                <?php echo $arrExamPlan->render(); ?>
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

    layui.use(['form', 'layer','jquery'], function () {
        var form = layui.form;
        var $ = layui.jquery;

        $(".pager li a").click(function () {
            var title = $("#titleSea").attr('sub');
            var worktype = $("#workTypeSea").attr('sub');
            if (title == '' && worktype == '') {

            } else {
                var a = $(this).attr("href");
                var url = a + "&title=" + title + "&worktype=" + worktype;
                $(this).attr("href", url);
            }
        });

        $(".detil").click(function () {
            var id =$(this).attr("exam_plan_id");
            // alert(urladdr.detailsExamPlan+"?id="+id);
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "鉴定计划详情",
                content: urladdr.detailsExamPlan+"?id="+id,  //调到详情页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        $(".release").click(function () {
            var id =$(this).attr("exam_plan_id");
            layer.open({
                title: ['温馨提示'],
                content: '<div style="color:#767676">确定要发布此鉴定计划吗？</div>',
                btn: ['确定', '取消'],
                shadeClose: true,
                //回调函数
                yes: function(index, layero){
                    $.ajax({
                        url:urladdr.releaseExamPlan,
                        type:'post',
                        dataType:'json',
                        data:{
                            id:id,
                        },
                        //判断注册状态
                        success:function (data) {
                            // console.log(data)
                            if (data.code==1){
                                layer.msg(data.msg, {
                                    icon: 1,//提示的样式
                                    time: 1000, //1秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                    end: function () {
                                        location.reload();
                                    }
                                })
                            }else{
                                layer.msg(data.msg)
                            }
                        }
                    });
                    //防止页面跳转
                    return false;
                }
            });
        });

        $(".delete").click(function () {
            var id =$(this).attr("exam_plan_id");
            // alert(id);
            layer.open({
                title: ['温馨提示'],
                content: '<div style="color:#767676">确定要删除当前数据吗？</div>',
                btn: ['确定', '取消'],
                shadeClose: true,
                //回调函数
                yes: function(index, layero){
                    $.ajax({
                        url:urladdr.deleteExamPlan,
                        type:'post',
                        dataType:'json',
                        data:{
                            id:id,
                        },
                        //判断注册状态
                        success:function (data) {
                            if (data.code==1){
                                layer.msg(data.msg, {
                                    icon: 1,//提示的样式
                                    time: 1000, //1秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                    end: function () {
                                        location.reload();
                                    }
                                })
                            }else{
                                layer.msg(data.msg)
                            }
                        }
                    });
                    //防止页面跳转
                    return false;
                }
            });
        });

        $(".edit").click(function () {
            var id =$(this).attr("exam_plan_id");
            var pagetpye = $("#pagetpye").val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "修改鉴定计划",
                content: urladdr.editExamPlan+"?id="+id+"&pagetpye="+pagetpye,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        $(".editWork").click(function () {
            var id =$(this).attr("exam_plan_id");
            var pagetpye = $("#pagetpye").val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "修改鉴定计划工种级别",
                content: urladdr.editExamPlanWork+"?id="+id+"&examtype="+pagetpye,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });

        $("#addExamplan").click(function () {
            var pagetpye = $("#pagetpye").val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "添加鉴定计划",
                content: urladdr.addPageExamPlan+"?pagetpye="+pagetpye,  //调到添加页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });

    });
</script>
