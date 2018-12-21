<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:105:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\topic\one_chone.html";i:1545032190;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>标题</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/static/css/common/page.css" />
    
</head>
<body class="layui-layout-body">
<div class="layui-card">
    <form class="layui-form" action="" lay-filter="component-form-group">
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
        <div class="layui-input-block">
            <button type="submit" class="layui-btn layui-carousel-right" lay-submit lay-filter="formSubmit">确定</button>
        </div>
    </form>
</div>
<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>

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
                        for(var i in data.data) {
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
                url:'/api/LearningTopicOfficial/workLevelDirectionInsert',
                type:'post',
                data:data,
                dataType:'json',
                success:function (data) {
                    if (data.code == 1){
                        layer.msg(data.msg,{
                            icon: 1,
                            time:  2000,
                            end: function () {
                                //当你在iframe页面关闭自身时
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.layer.close(index); //再执行关闭
                                parent.location.reload();
                            }
                        });
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

