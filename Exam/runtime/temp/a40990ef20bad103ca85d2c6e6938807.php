<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:108:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\indexthesis.html";i:1545017019;}*/ ?>
<div>
    <span class="title" data-value="<?php echo $title; ?>"></span>
    <div class="header_title"><span>论文上传</span> </div>
    <div class="baoming_main">
        <table class="layui-table myorder_table" lay-skin="line">
            <colgroup>
                <col width="300">
                <col>
                <col>
                <col>
                <col width="140">
            </colgroup>
            <thead>
            <tr>
                <!--<th>ID</th>-->
                <th>鉴定计划</th>
                <th>工种</th>
                <!--<th>方向</th>-->
                <th>等级</th>
                <th>状态</th>
                <!--<th>考试时间</th>-->
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($examData) || $examData instanceof \think\Collection || $examData instanceof \think\Paginator): $i = 0; $__LIST__ = $examData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <tr>
                <!--<td><?php echo $vo['id']; ?></td>-->
                <td><?php echo $vo['title']; ?></td>
                <td><?php echo $vo['workname']; ?></td>
                <!--<td><?php echo $vo['directionname']; ?></td>-->
                <td><?php echo $vo->work_level_subject_level; ?></td>
                <td><?php echo $vo->thesis_state; ?></td>
                <!--<td><?php echo $vo['exam_time']; ?></td>-->
                <td style="text-align: left;">
                    <!--{if $now < $vo.exam_time && $now < date($vo.exam_time,strtotime("-15 day"))}-->

                    <?php if($now < strtotime(date($vo['exam_time'],strtotime("-15 day")))): ?>
                        <button class="looks_btn margin_right_10 borderwid upbth blue_btn"  thesisId="<?php echo $vo['id']; ?>">上传</button>
                        <?php if($vo['tid'] != null): ?>
                        <button class="looks_btn margin_right_10 borderwid"><a style="color: white;" href="<?php echo $vo['path']; ?>">下载</a></button>
                        <?php else: endif; else: ?>
                        <button class="looks_btn margin_right_10 borderwid upbth layui-bg-cyan" disabled="disabled" title="请在开始考试10日前上传答辩论文，在规定时间内未提交论文的考生视为放弃综合评审答辩考试" >上传</button>
                        <button class="looks_btn margin_right_10 borderwid layui-bg-cyan" disabled="disabled"> <a style="color: white;">下载</a></button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <p class="tishi"><span><img src="/static/front/img/happy.png"/></span> 温馨提示：请在开始考试15日前上传答辩论文，在规定时间内未提交论文的考生视为放弃综合评审答辩考试</p>
    </div>
</div>
<div class="thesis layui-form" style="display: none;">
    <h2>论文信息</h2>
    <div class="layui-form-item">
        <label class="layui-form-label">名称</label>
        <div class="layui-input-block">
            <label></label>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">地址</label>
        <div class="layui-input-block">
            <label></label>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript">
    layui.use('element', function(){
        var element = layui.element;
    });

    layui.use(['form', 'upload','jquery','laypage','laydate'], function() {
        var form = layui.form,
                $ = layui.jquery,
                upload = layui.upload;
        form.render();
        var laydate = layui.laydate;
        $ = layui.jquery;
        form.render();
        var laypage = layui.laypage;
        $('title').html($('.title').data('value'));

            //执行实例

        $(".blue_btn").click(function () {
            exam_enroll_id = $(this).attr('thesisId');
        });
            // 上传
            var uploadInst = upload.render({
                elem: '.blue_btn' //绑定元素
                , url: '/api/Thesis/yourUrl'
                , accept: 'file'
                ,before: function (input) {
                    this.data={ //额外参数
                        'exam_enroll_id':exam_enroll_id,
                    };
                }
                ,done: function(data){
                    //上传完毕回调
                    if (data.code == 1){
                        //上传成功
                        layer.msg(data.msg,{
                            icon: 1,//提示的样式
                            time:  2000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                            end: function () {
                                $.get("<?php echo url('/examinee/center/indexthesis'); ?>", function(data) {
                                    window.parent.$("#iframeContent").html(data); //初始化加载界面
                                    //取消遮罩的时候
                                    $(".layui-layer-shade").hide();
                                });
                            }
                        });
                    } else{
                        layer.msg(data.msg,{icon:5});
                    }
                }
                ,error: function(data){
                    //请求异常回调
                    layer.msg(data.msg,{icon:5});
                }
            });

    });
</script>