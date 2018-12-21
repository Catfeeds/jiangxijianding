<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:122:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\simulation_list_appraisal.html";i:1545017019;}*/ ?>
<div>
    <div class="header_title"><span>模拟考试</span> </div>
    <div class="baoming_main">
        <table class="layui-table myorder_table" lay-skin="line">
            <colgroup>
                <col>
                <col>
                <col>
                <col width="200">
            </colgroup>
            <thead>
            <tr>
                <th>试题标题</th>
                <th>得分</th>
                <th>考试时间</th>
                <th>操作</th>
            </tr>
            </thead>

            <tbody>
            <?php if(is_array($simulationList) || $simulationList instanceof \think\Collection || $simulationList instanceof \think\Paginator): $i = 0; $__LIST__ = $simulationList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$obj): $mod = ($i % 2 );++$i;if(is_array($obj) || $obj instanceof \think\Collection || $obj instanceof \think\Paginator): $i = 0; $__LIST__ = $obj;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['flag']): ?>
            <tr>
                <td><?php echo $vo['learning_paper_history_name']; ?></td>
                <td><?php echo $vo['learning_paper_history_score']; ?></td>
                <td><?php echo $vo->learning_paper_history_start_time; ?></td>
                <td class="double_btn">
                    <div class="blue_btn ajax-linkk" rel="<?php echo url('/examinee/center/simulationDetail'); ?>?id=<?php echo $vo['id']; ?>&learning_paper_history_id=<?php echo $vo['learning_paper_history_id']; ?>&name=<?php echo $vo['learning_topic_paper_name']; ?>">继续考试</div>
                </td>
            </tr>
            <?php else: ?>
            <tr>
                <td><?php echo $vo['learning_topic_paper_name']; ?></td>
                <td>--</td>
                <td>--</td>
                <td class="double_btn">
                    <div class="blue_btn ajax-linkk" rel="<?php echo url('/examinee/center/simulationDetail'); ?>?id=<?php echo $vo['id']; ?>&learning_paper_history_id=<?php echo $vo['learning_paper_history_id']; ?>&name=<?php echo $vo['learning_topic_paper_name']; ?>">开始考试</div>
                </td>
            </tr>
            <?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>

            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    layui.use(['form', 'laypage','laydate'], function() {
        var form = layui.form,
            $ = layui.jquery;
        form.render();
        var laydate = layui.laydate;
        $ = layui.jquery;
        $('.layui-layer-shade').hide();
        form.render();

        var laypage = layui.laypage;

    });
    $(".ajax-linkk").each(function() {
        $(this).click(function() {
            var diZhi = $(this).attr("rel");
            htmlobj = $.ajax({
                url: diZhi,
                async: false
            });
            $(".ajax-Box").html(htmlobj.responseText);
        });
    });
</script>