<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:97:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/cms\view\guide\update.html";i:1545183598;}*/ ?>


<link rel="stylesheet" href="/static/layui/css/layui.css">


<body>


        <style type="text/css">
            .layui-form-item{
                margin-left: 15%;

            }
        </style>
      <header></header>
    <form class="layui-form" >

        <div class="layui-form-item" style="margin-top: 4%;>
">
            <label class="layui-form-label">栏目名称<span style="color:red">*</span></label>
            <div class="layui-input-inline">
                <input type="hidden" value="<?php echo $data['id']; ?>" name="id">
                <input type="text" id="guide_name" name="guide_name" value="<?php echo $data['guide_name']; ?>" required lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
            </div>
        </div>

        <!--<div class="layui-form-item">-->
            <!--<label class="layui-form-label">选择关联栏目(可以不选)</label>-->
            <!--<div class="layui-input-block">-->
                <!--<?php if(is_array($info) || $info instanceof \think\Collection || $info instanceof \think\Paginator): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>-->
                <!--<input type="checkbox" name="relation[]" title="<?php echo $v['guide_name']; ?>" value="<?php echo $v['id']; ?>" <?php if((in_array($v['id'],$ids)==true)): ?>checked<?php endif; ?>>-->

                <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
            <!--</div>-->
        <!--</div>-->

        <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="sub">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
    </form>
</body>




<script src="/static/js/jquery.min.js"></script>
<script src="/static/layui/layui.js"></script>


<script type="text/javascript">
    layui.use(['form', 'layer','jquery'], function () {
        var form  = layui.form;
        var $  = layui.jquery;

        //添加表单监听事件
        form.on('submit(sub)',function (data) {
            data = data.field;
            var button = $(this);
            button.addClass('layui-btn-disabled').attr('disabled',true);
            $.post("/api/CmsGuide/update",data,function (data) {

                if (data.code == 1){
                    layer.msg(data.msg, {
                        icon: 1,//提示的样式
                        time: 2000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                        end: function () {
                            //当你在iframe页面关闭自身时
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭
                            parent.location.reload();
                        }
                    })
                }else{
                    layer.msg(data.msg);
                    button.removeClass('layui-btn-disabled').removeAttr('disabled');
                }
            })
            //防止页面跳转
            return false;
        });


    });

</script>
