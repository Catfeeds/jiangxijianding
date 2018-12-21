<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:106:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\materials.html";i:1545017019;}*/ ?>
<div class="header_title"><span>学习资料</span>
</div>
<style>
    #demoList a:hover{color: white;}
</style>
<div class="baoming_main">
    <table class="layui-table myorder_table">
        <thead>
            <th>标题</th>
            <th>类型</th>
            <th>大小</th>
            <th>操作</th>
        </tr></thead>
        <tbody id="demoList">
        <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <tr>
            <td><?php echo $vo['file_name']; ?></td>
            <td><?php echo $vo['file_type']; ?></td>
            <td><?php echo $vo['file_size']; ?></td>
            <td width="82px">
                <a class="layui-btn layui-btn-normal"  target="_blank" style="margin:0 auto;" href="<?php echo $vo['file_address']; ?>">在线学习</a>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    layui.use(['form', 'laypage','laydate'], function() {
        var form = layui.form,
            $ = layui.jquery;
        form.render();
        var laydate = layui.laydate;
        $ = layui.jquery;
        form.render();

        var laypage = layui.laypage;


    });
</script>