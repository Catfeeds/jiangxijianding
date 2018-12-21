<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:104:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\topic\official.html";i:1545036031;s:86:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\learning\view\layout.html";i:1545017021;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>正式题库</title>
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
            <form action="/learning/topic/official?work=<?=isset($_GET['work']) ? $_GET['work'] : ''?>&level=<?=isset($_GET['level']) ? $_GET['level'] : ''?>" method="post">
                <div class="layui-form-item">
                    <div class="layui-inline">题型</div>
                    <div class="layui-inline">
                        <select name="type" lay-filter="LAY-user-adminrole-type">
                            <option value="">全部题型</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 0): ?> selected="selected" <?php endif; endif; ?> value="0"></option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">单选</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">多选</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 3): ?> selected="selected" <?php endif; endif; ?> value="3">判断</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 4): ?> selected="selected" <?php endif; endif; ?> value="4">填空</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 5): ?> selected="selected" <?php endif; endif; ?> value="5">简答</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 6): ?> selected="selected" <?php endif; endif; ?> value="6">作文</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 7): ?> selected="selected" <?php endif; endif; ?> value="7">论述</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 8): ?> selected="selected" <?php endif; endif; ?> value="8">分析</option>
                            <option <?php if(!(empty($search['type']) || (($search['type'] instanceof \think\Collection || $search['type'] instanceof \think\Paginator ) && $search['type']->isEmpty()))): if($search['type'] == 9): ?> selected="selected" <?php endif; endif; ?> value="9">操作题</option>
                        </select>
                    </div>

                    <div class="layui-inline">归属题库</div>
                     <div class="layui-inline">
                        <select name="range" lay-filter="LAY-user-adminrole-type">
                            <option value="">全部题库</option>
                            <option <?php if(!(empty($search['range']) || (($search['range'] instanceof \think\Collection || $search['range'] instanceof \think\Paginator ) && $search['range']->isEmpty()))): if($search['range'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">在线练习</option>
                            <option <?php if(!(empty($search['range']) || (($search['range'] instanceof \think\Collection || $search['range'] instanceof \think\Paginator ) && $search['range']->isEmpty()))): if($search['range'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">模拟考试</option>
                        </select>
                    </div>

                    <div class="layui-inline">难易程度</div>
                    <div class="layui-inline">
                        <select name="topic_level" lay-filter="LAY-user-adminrole-type">
                            <option value="">全部程度</option>
                            <option <?php if(!(empty($search['topic_level']) || (($search['topic_level'] instanceof \think\Collection || $search['topic_level'] instanceof \think\Paginator ) && $search['topic_level']->isEmpty()))): if($search['topic_level'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">易</option>
                            <option <?php if(!(empty($search['topic_level']) || (($search['topic_level'] instanceof \think\Collection || $search['topic_level'] instanceof \think\Paginator ) && $search['topic_level']->isEmpty()))): if($search['topic_level'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">偏易</option>
                            <option <?php if(!(empty($search['topic_level']) || (($search['topic_level'] instanceof \think\Collection || $search['topic_level'] instanceof \think\Paginator ) && $search['topic_level']->isEmpty()))): if($search['topic_level'] == 3): ?> selected="selected" <?php endif; endif; ?> value="3">适中</option>
                            <option <?php if(!(empty($search['topic_level']) || (($search['topic_level'] instanceof \think\Collection || $search['topic_level'] instanceof \think\Paginator ) && $search['topic_level']->isEmpty()))): if($search['topic_level'] == 4): ?> selected="selected" <?php endif; endif; ?> value="4">偏难</option>
                            <option <?php if(!(empty($search['topic_level']) || (($search['topic_level'] instanceof \think\Collection || $search['topic_level'] instanceof \think\Paginator ) && $search['topic_level']->isEmpty()))): if($search['topic_level'] == 5): ?> selected="selected" <?php endif; endif; ?> value="5">难</option>
                        </select>
                    </div>
                    <!--<div class="layui-inline">审核状态</div>-->
                    <!--<div class="layui-inline">-->
                        <!--<select name="topic_state" lay-filter="LAY-user-adminrole-type">-->
                            <!--<option value="">全部状态</option>-->
                            <!--<option <?php if(!(empty($search['topic_state']) || (($search['topic_state'] instanceof \think\Collection || $search['topic_state'] instanceof \think\Paginator ) && $search['topic_state']->isEmpty()))): if($search['topic_state'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">禁用</option>-->
                            <!--<option <?php if(!(empty($search['topic_state']) || (($search['topic_state'] instanceof \think\Collection || $search['topic_state'] instanceof \think\Paginator ) && $search['topic_state']->isEmpty()))): if($search['topic_state'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">启用</option>-->
                        <!--</select>-->
                    <!--</div>-->

                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-primary reset">重置</button>
                        <button class="layui-btn" lay-submit>搜索</button>
                    </div>
                </div>
            </form>
        </div><br><br><br>

        <div class="layui-card-body">
            <div style="padding-bottom: 9px;">
                <button class="layui-btn layui-btn-xs addChoose">
                    <i class="layui-icon">&#xe608;</i> 添加选项题
                </button>
                <button class="layui-btn layui-btn-xs addJudge">
                    <i class="layui-icon">&#xe608;</i> 添加判断题
                </button>
                <!--<button class="layui-btn layui-btn-xs addEmpty">-->
                    <!--<i class="layui-icon">&#xe608;</i> 添加填空题-->
                <!--</button>-->
                <button class="layui-btn layui-btn-xs addBrief">
                    <i class="layui-icon">&#xe608;</i> 添加简答题
                </button>
                <!--<button class="layui-btn layui-btn-xs addComposition">-->
                    <!--<i class="layui-icon">&#xe608;</i> 添加作文题-->
                <!--</button>-->
                <button class="layui-btn layui-btn-xs addDescribe">
                    <i class="layui-icon">&#xe608;</i> 添加论述题
                </button>
                <button class="layui-btn layui-btn-xs topicImport">
                <i class="layui-icon">&#xe608;</i> 批量导入
                </button>
                <!--<button class="layui-btn layui-btn-xs addAnalyze">-->
                    <!--<i class="layui-icon">&#xe608;</i> 添加分析题-->
                <!--</button>-->
                <!--<button class="layui-btn layui-btn-xs addOperation">-->
                    <!--<i class="layui-icon">&#xe608;</i> 添加操作题-->
                <!--</button>-->
                <div style="padding-bottom: 10px;float:right;" id="prompt"></div>
            </div>
            <table class="layui-table" style="table-layout:fixed;">
                <colgroup>
                    <col width="150">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th width="5%"></th>
                    <th>题目</th>
                    <th>题型</th>
                    <th>归属题库</th>
                    <th>难易程度</th>
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
                   <td style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;"><?php echo $vo['topic_name']; ?></td>
                    <td><?php echo $vo->type; ?></td>
                    <td><?php echo $vo->range; ?></td>
                    <td><?php echo $vo['topic_level']; ?></td>
                    <td><?php echo $vo['work_name']; ?></td>
                    <td><?php echo $vo->level_id; ?></td>
                    <td><?php echo $vo['work_direction_name']; ?></td>
                    <td><?php echo $vo['create_time']; ?></td>
                    <td>
                        <?php switch($vo['type']): case "1": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addChoose">修改</button>
                        <?php break; case "2": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addChoose">修改</button>
                        <?php break; case "3": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addJudge">修改</button>
                        <?php break; case "4": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addEmpty">修改</button>
                        <?php break; ?>rief
                        <?php case "5": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addBrief">修改</button>
                        <?php break; case "6": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addComposition">修改</button>
                        <?php break; case "7": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addDescribe">修改</button>
                        <?php break; case "8": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addAnalyze">修改</button>
                        <?php break; case "9": ?>
                        <button data-value="<?php echo $vo['id']; ?>" class="layui-btn layui-btn-xs select addOperation">修改</button>
                        <?php break; endswitch; ?>

                        <button data-value="<?php echo $vo['id']; ?>" data-name="<?php echo $vo['topic_name']; ?>" class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>
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

    //cookie公共函数
    function get_cookie(Name) {
        var search = Name + "="//查询检索的值
        var returnvalue = "";//返回值
        if (document.cookie.length > 0) {
            sd = document.cookie.indexOf(search);
            if (sd != -1) {
                sd += search.length;
                end = document.cookie.indexOf(";", sd);
                if (end == -1)
                    end = document.cookie.length;
                //unescape() 函数可对通过 escape() 编码的字符串进行解码。
                returnvalue = unescape(document.cookie.substring(sd, end))
            }
        }
        return returnvalue;
    }

    function getQueryString(key){
        var reg = new RegExp("(^|&)"+key+"=([^&]*)(&|$)");
        var result = window.location.search.substr(1).match(reg);
        return result?decodeURIComponent(result[2]):null;
    }

    layui.use(['form', 'layer','jquery'], function () {
        var form  = layui.form, $  = layui.jquery;

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

        //选择工种、级别和方向
        $(function () {

            if (getQueryString('work') && getQueryString('level')) {
                if (getQueryString('work') == 0){
                    $('#prompt').append('<span class="layui-badge"> 考评人员 </span>&nbsp;');
                } else {
                    $('#prompt').append('<span class="layui-badge">'+getQueryString("work")+'</span>&nbsp;');
                }
                if (getQueryString('level') == 0){
                    //考评人员无
                } else {
                    $('#prompt').append('<span class="layui-badge">'+getQueryString("level")+'</span>&nbsp;');
                }
                if (getQueryString('direction') == 0){
                    //考评人员无
                } else {
                    $('#prompt').append('<span class="layui-badge">'+getQueryString("direction")+'</span>&nbsp;');
                }
            } else {
                window.parent.location.href='/learning/topic/officialChoose';
                return false;
            }

        });
        //选项题
        $(".addChoose").click(function () {
            var id = $(this).data('value');
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "选项题",
                content: "/learning/topic/officialAddChoose/id/"+id,  //调到新增页面
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }

            });
        });
        //判断题
        $(".addJudge").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "判断题",
                content: "/learning/topic/officialAddJudge/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //填空题
        $(".addEmpty").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "填空题",
                content: "/learning/topic/officialAddEmpty/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //简答题
        $(".addBrief").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "简答题",
                content: "/learning/topic/officialAddBrief/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //作文题
        $(".addComposition").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "作文题",
                content: "/learning/topic/officialAddComposition/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        $(".topicImport").click(function () {
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "批量导入",
                content: "/learning/topic/import",
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //论述题
        $(".addDescribe").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "论述题",
                content: "/learning/topic/officialAddDescribe/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //分析题
        $(".addAnalyze").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "分析题",
                content: "/learning/topic/officialAddAnalyze/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //操作题
        $(".addOperation").click(function () {
            var id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '90%'], //宽高
                title: "简答题",
                content: "/learning/topic/officialAddOperation/id/"+id,
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
        //删除题
        $(".demo-delete").click(function () {
            var id = $(this).data('value');
            var name = $(this).data('name');
            layer.open({
                title: ['温馨提示'],
                content: '确定要删除试题: <span style="color: red;">'+ name +' </span>吗？',
                btn: ['确定', '取消'],
                shadeClose: true,
                yes: function(index, layero){
                    $.ajax({
                        url:'/api/LearningTopicOfficial/delOffical',
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
        //重置
        $(".reset").click(function () {
            $.each($('form input'),function (index,item) {
                $(item).val('');
            });
            return false;
        });
    });
</script>

</html>
