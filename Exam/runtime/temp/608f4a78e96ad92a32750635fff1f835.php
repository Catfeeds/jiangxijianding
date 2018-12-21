<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:101:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\media\index.html";i:1545017021;s:86:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\learning\view\layout.html";i:1545017021;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>课件管理</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/static/css/common/page.css" />
    
<style>
    .button-success {
        display: none;
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
            <form action="/learning/media/index" method="post">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">文件名</label>
                        <div class="layui-input-block">
                            <input type="text" name="file_name" placeholder="请输入" value="<?php echo (isset($search['file_name']) && ($search['file_name'] !== '')?$search['file_name']:''); ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">类型</div>
                    <div class="layui-inline">
                        <select name="file_type" lay-filter="LAY-user-adminrole-type">
                            <option value="">全部类型</option>

                            <option <?php if(!(empty($search['file_type']) || (($search['file_type'] instanceof \think\Collection || $search['file_type'] instanceof \think\Paginator ) && $search['file_type']->isEmpty()))): if($search['file_type'] == 1): ?> selected="selected" <?php endif; endif; ?> value="1">视频格式</option>
                            <option <?php if(!(empty($search['file_type']) || (($search['file_type'] instanceof \think\Collection || $search['file_type'] instanceof \think\Paginator ) && $search['file_type']->isEmpty()))): if($search['file_type'] == 2): ?> selected="selected" <?php endif; endif; ?> value="2">Docx文档格式</option>
                            <option <?php if(!(empty($search['file_type']) || (($search['file_type'] instanceof \think\Collection || $search['file_type'] instanceof \think\Paginator ) && $search['file_type']->isEmpty()))): if($search['file_type'] == 3): ?> selected="selected" <?php endif; endif; ?> value="3">PDF文件格式</option>
                            <option <?php if(!(empty($search['file_type']) || (($search['file_type'] instanceof \think\Collection || $search['file_type'] instanceof \think\Paginator ) && $search['file_type']->isEmpty()))): if($search['file_type'] == 4): ?> selected="selected" <?php endif; endif; ?> value="4">PPT文件格式</option>
                            <option <?php if(!(empty($search['file_type']) || (($search['file_type'] instanceof \think\Collection || $search['file_type'] instanceof \think\Paginator ) && $search['file_type']->isEmpty()))): if($search['file_type'] == 5): ?> selected="selected" <?php endif; endif; ?> value="5">Flash动画文件</option>
                        </select>
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-primary reset">重置</button>
                        <button class="layui-btn" lay-submit>搜索</button>
                    </div>

                </div>
            </form>
        </div>
        <div class="layui-upload-list">
        <div style="padding-bottom: 10px;">
            <button type="button" class="layui-btn layui-btn-normal" id="testList">选择多文件</button>
            <button type="button" class="layui-btn release" id="testListAction">开始上传</button>
        </div>
        <table class="layui-table">
            <thead>
            <tr><th width="5%">ID</th>
                <th>文件名</th>
                <th>文件类型</th>
                <th>文件大小</th>
                <th>上传状态</th>
                <th>上传时间</th>
                <th>操作</th>
            </tr></thead>
            <tbody id="demoList">
            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $vo['file_name']; ?></td>
                <td><?php echo $vo['file_type']; ?></td>
                <td><?php echo $vo['file_size']; ?></td>
                <td style="color: green;">上传成功</td>
                <td><?php echo $vo['create_time']; ?></td>
                <td width="82px">
                    <a class="layui-btn layui-btn-xs" target="_blank" style="margin:0 auto;" href="<?php echo $vo['file_address']; ?>">在线学习</a>
                    <button data-value="<?php echo $vo['id']; ?>" data-name="<?php echo $vo['file_name']; ?>" class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>
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

    layui.use(['form', 'layer','jquery','upload'], function () {
        var form  = layui.form, $  = layui.jquery,upload = layui.upload;

        var demoListView = $('#demoList')
            ,uploadListIns = upload.render({
                elem: '#testList'
                ,url: '/api/learningMedia/add'
                ,accept: 'file'
                ,multiple: true
                ,auto: false
                ,bindAction: '#testListAction'
                ,choose: function(obj){
                    var layerMsg = layer.load(1,{
                        icon: 0,
                        shade: [0.5,'white']
                    });

                    var files = this.files = obj.pushFile();
                    var myDate = new Date();
                    var ourMonth = myDate.getMonth()+1;
                    obj.preview(function(index, file, result){
                        fileData = file;
                        var fileSuffix = file.name.split('.')[1];
                        switch(fileSuffix)
                        {
                            case 'mp4':
                                var fileType = '视频格式';
                                break;
                            case 'docx':
                                var fileType = 'Docx文档格式';
                                break;
                            case 'pdf':
                                var fileType = 'PDF文件格式';
                                break;
                            case 'pptx':
                                var fileType = 'PPT文件格式';
                                break;
                            case 'swf':
                                var fileType = 'Flash动画文件';
                                break;
                            default:
                                var fileType = '不支持文件格式';
                        }
                        var tr = $(['<tr id="upload-'+ index +'" style="color: red">'
                            ,'<td style="font-size: 29px;padding-top: 22px;">*</td>'
                            ,'<td>'+ file.name +'</td>'
                            ,'<td>'+ fileType +'</td>'
                            ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                            ,'<td>等待上传</td>'
                            ,'<td>等待上传</td>'
                            ,'<td>'
                            ,'<button class="layui-btn layui-btn-xs select layui-btn-disabled" value="">查看</button>'
                            ,'<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                            ,'</td>'
                            ,'</tr>'].join(''));
                        tr.find('.demo-delete').on('click', function(){
                            delete files[index];
                            tr.remove();
                            uploadListIns.config.elem.next()[0].value = '';
                        });

                        demoListView.prepend(tr);
                        layer.close(layerMsg);
                    });

                }
                ,before: function (input) {
                    var layerMsg = layer.load(1,{
                        icon: 0,
                        shade: [0.5,'white']
                    });
                }
                ,done: function(res, index, upload){
                    var layerMsg = layer.load(1,{
                        icon: 0,
                        shade: [0.5,'white']
                    });
                    if(res.code == 0){
                        return layer.msg(res.msg, {
                            icon: 1,
                            time: 1000,
                            end: function () {
                                location.reload();
                            }
                        })
                    } else {
                        layer.close(layerMsg);
                        layer.open({
                            title: '提示'
                            ,content: res.msg
                        });
                    }
                    this.error(index, upload);
                }
                ,error: function(index, upload){
                    var tr = demoListView.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                    tds.eq(3).find('.demo-reload').removeClass('layui-hide');
                }
            });

        $(".demo-delete").click(function () {
            var $=layui.jquery,id = $(this).data('value'),name = $(this).data('name');
            layer.open({
                title: ['温馨提示'],
                content: '确定要删除文件: <span style="color: red;">'+ name +'</span>吗？',
                btn: ['确定', '取消'],
                shadeClose: true,
                yes: function(index, layero){
                    $.ajax({
                        url:'/api/learningMedia/delete',
                        type:'post',
                        dataType:'json',
                        data:{
                            id:id
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

        //选项题
        $(".addChoose").click(function () {
            var $=layui.jquery,id = $(this).data('value');

            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '900px'], //宽高
                title: "选项题",
                content: "/learning/topic/officialAddChoose/id/"+id,  //调到新增页面
                yes: function(index, layero){
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }

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
