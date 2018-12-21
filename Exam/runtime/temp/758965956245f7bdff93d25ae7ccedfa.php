<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:100:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/admin\view\menu\editmenu.html";i:1545017008;s:95:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\common\view\layout\nonelayout.html";i:1545212656;}*/ ?>
<link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
<link rel="stylesheet" type="text/css" href="/static/css/common/page.css" />
<link rel="stylesheet" type="text/css" href="/static/layui/css/multiSelect.css" />
<link rel="stylesheet" type="text/css" href="/static/layui/css/zoomify.css" />
<link rel="stylesheet" type="text/css" href="/static/layui/css/con_main.css" />


<div class="site-text site-block">
    <form class="layui-form" action="" style="width: 450px;">
        <div class="layui-form-item"></div>
        <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
                <input type="text" name="title" required="" lay-verify="required" placeholder="请输入名称" autocomplete="off"
                       value="<?php echo $data['title']; ?>" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">地址</label>
            <div class="layui-input-block">
                <input type="text" name="url" required="" lay-verify="required" placeholder="请输入地址" autocomplete="off"
                       value="<?php echo $data['url']; ?>" class="layui-input">
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="formSubmit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/js/zoomify.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>
<script type="text/javascript" src="/static/js/common/must.js"></script>

<script>
    layui.use('form', function () {
        var form = layui.form;
        //监听提交
        form.on('submit(formSubmit)', function (data) {
            var data = data.field, button = $(this);
            button.addClass('layui-btn-disabled').attr('disabled', true);
            $.post(urladdr.doeditMenu, data, function (data) {
                if (data.code === 1) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function () {
                        layer.close(layer.index);
                        window.parent.location.reload();
                    });
                } else {
                    layer.msg(data.msg, {icon: 5}, function () {
                        button.removeClass('layui-btn-disabled').removeAttr('disabled');
                    });
                }
            });
            return false;
        });
    });
</script>
