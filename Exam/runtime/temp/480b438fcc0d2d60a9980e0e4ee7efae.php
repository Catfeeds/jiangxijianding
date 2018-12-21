<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:108:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/learning\view\set_volume\details.html";i:1545017021;}*/ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">

<body>

<?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
<ul class="layui-tab-content" style="width: 60%;margin: 0 auto;">
    <?php switch($vo['type']): case "1": ?>
    <div class="test">
        <div class="warp_test">
            <table class="outer_table">
                <tbody>
                <tr>
                    <td><h5> <?php echo $i; ?>、 <?php echo $vo['topic_name']; ?></h5><br>
                        <p>A、<?php echo $vo['option_a']; ?></p><p>B、<?php echo $vo['option_b']; ?></p><p>C、<?php echo $vo['option_c']; ?></p><p>D、<?php echo $vo['option_d']; ?></p><br>
                        <table cellpadding="0" cellspacing="0" class="choice_table">
                            <tbody>
                            <tr>
                                <td>
                                    正确答案为：<?php echo $vo['answer']; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table></td>
                    <td align="right"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div><hr>
    <?php break; case "2": ?>
    <div class="test">
        <div class="warp_test">
            <table class="outer_table">
                <tbody>
                <tr>
                    <td><h5> <?php echo $i; ?>、 <?php echo $vo['topic_name']; ?> </h5><br>
                        <p>A、<?php echo $vo['option_a']; ?></p><p>B、<?php echo $vo['option_b']; ?></p><p>C、<?php echo $vo['option_c']; ?></p><p>D、<?php echo $vo['option_d']; ?></p><br>
                        <table cellpadding="0" cellspacing="0" class="choice_table">
                            <tbody>
                            <tr>
                                <td>
                                    正确答案为：<?php echo $vo['answer']; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table></td>
                    <td align="right"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div><hr>
    <?php break; case "3": ?>
    <div class="test">
        <div class="warp_test">
            <table class="outer_table">
                <tbody>
                <tr>
                    <td><h5> <?php echo $i; ?>、 <?php echo $vo['topic_name']; ?> </h5><br>
                        <table cellpadding="0" cellspacing="0" class="choice_table">
                            <tbody>
                            <tr>
                                <td>
                                    正确答案为：<?php echo $vo['answer']; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table></td>
                    <td align="right"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div><hr>
    <?php break; endswitch; ?>
</ul>
<?php endforeach; endif; else: echo "" ;endif; ?>

</body></html>
<script type="text/javascript" src="/static/layui/layui.js"></script> <script type="text/javascript" src="/static/layui/lay/modules/code.js"></script> <script type="text/javascript" src="/static/js/jquery.min.js"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use(['form', 'layedit', 'laydate'],
    function() {
        var form = layui.form,
            layer = layui.layer,
            layedit = layui.layedit,
            laydate = layui.laydate;
    });

    layui.use('element',
        function() {
            var element = layui.element;
        });
    layui.use(['form', 'layer', 'jquery'],
        function() {
            var form = layui.form;
            var $ = layui.jquery;
        });
</script>