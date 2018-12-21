<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:102:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\topic\import.html";i:1545205449;}*/ ?>
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
<div style="padding-top: 48px">
    <div>
        <div style="text-align: center;font-size: 20px;line-height: 50px;">请先下载导入模板，并按模板文件填写试题信息后进行导入！
            (<a href="/static/topic.xlsx" style="text-decoration: underline;color: skyblue;">模板下载</a>)
        </div>
    </div>
    <br>
    <br>
    <form method="post" enctype="multipart/form-data" id="form" class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label" style="width: 55%">上传xlsx文件：</label>
                <div class="layui-input-block" style="width: 40%; margin-left: 70%;">

                    <input type="file" name="file" id="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                           style="border: 1px solid #ccc;"/>
                </div>
            </div>
        </div>
        <br>
        <div class="layui-form-item">
            <div class="layui-input-block" style="width: 40%; margin-left: 280px;">
                <button class="layui-btn"  id="submit" value="批量上传" style="display: inline;" class="nav1_left_shangchuan">批量上传</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>

<script>
    layui.use(['form', 'layer','jquery'], function () {
        var form = layui.form;
        var $ = layui.jquery;

        $('#submit').click(function(){

            var file = document.getElementById("file").value;

            if (file==""){
                layer.msg('上传文件不能为空',{time:1000});
                return false;
            }else{
                form = new FormData($("#form")[0]).value;
                console.log(form);
                return false;
                $.ajax({
                    url:'/api/LearningTopicOfficial/topicImport',
                    type:'post',
                    data:form,
                    dataType:'json',
                    success:function (data) {
                        console.log(data);
                    }
                });

                return false;

                // $.ajax({
                //     url:'/api/LearningTopicOfficial/topicImport',
                //     type:'POST',
                //     data:form,
                //     dataType:'json',
                //     processData:false,
                //     contentType:false,
                //     success:function (data) {
                //         if (data.code == 1){
                //             layer.msg(data.msg,{
                //                 icon: 1,
                //                 end: function () {
                //                     //当你在iframe页面关闭自身时
                //                     var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                //                     parent.layer.close(index); //再执行关闭
                //                     parent.location.reload();
                //                 }
                //             });
                //         }else if(data.code== -1){
                //             layer.msg(data.msg);
                //         }else{
                //             layer.open({
                //                 title: ['温馨提示'],
                //                 content: data.msg,
                //                 btn: ['确定'],
                //                 shadeClose: true,
                //                 //回调函数
                //                 yes: function(index, layero){
                //                     layer.close(index);
                //                     //防止页面跳转
                //                     return false;
                //                 }
                //
                //             });
                //         }
                //     }
                // });
                return false;
            }
        })
    });

</script>
