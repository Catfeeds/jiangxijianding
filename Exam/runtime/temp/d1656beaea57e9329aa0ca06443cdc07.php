<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:103:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\report\detail.html";i:1545017022;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>问题详情</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css">
</head>
<body>

<div class="layui-body">
    <div style="padding: 15px;">
        <form class="layui-form" action="" id="addform">
            <input type="hidden" name="id" value="<?php echo (isset($datas['id']) && ($datas['id'] !== '')?$datas['id']:''); ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">上报者IP</label>
                <div class="layui-input-block">
                    <input type="text" name="user_ip" id="topic_name" value="<?php echo (isset($datas['user_ip']) && ($datas['user_ip'] !== '')?$datas['user_ip']:''); ?>" required  lay-verify="required" placeholder="请输入题目" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">上报内容</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" style="height: 300px" lay-verify="required" name="centent" class="layui-textarea"><?php echo (isset($datas['centent']) && ($datas['centent'] !== '')?$datas['centent']:''); ?></textarea>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;
    });

    layui.use('element', function(){
        var element = layui.element;
    });
    layui.use(['form', 'layer','jquery'], function () {
        var form = layui.form;
        var $ = layui.jquery;
    });

</script>
</body>
</html>