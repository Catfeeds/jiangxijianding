<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:107:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\selectplan.html";i:1545017019;s:90:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\application\examinee\view\nonelayout.html";i:1545017019;}*/ ?>
<link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
<link rel="stylesheet" type="text/css" href="/static/front/css/lay.css" />
<link rel="stylesheet" type="text/css" href="/static/front/css/all.css" />
<link rel="stylesheet" type="text/css" href="/static/front/css/iconfont.css" />



<style>
    .bor_btm{
        border-bottom: 1px dashed #b1b3b5;height: 55px;这样是么
    }
</style>
<div class="header_title"><span>我的报名>选择鉴定计划</span>
</div>
<div class="WoYaoBaoMing layui-form">
    <p style="margin: 20px 0 10px 80px;">您可以报名以下鉴定计划：</p>
    <div style="margin-left: 100px;">
        <?php if(empty($dataTitle) != true): if(is_array($arrExamPlan) || $arrExamPlan instanceof \think\Collection || $arrExamPlan instanceof \think\Paginator): if( count($arrExamPlan)==0 ) : echo "" ;else: foreach($arrExamPlan as $key=>$p): ?>
        <table border="0">
            <tr class="bor_btm">
                <td width="500px"><input type="radio" name="plan" class="plan" value="<?php echo $p['id']; ?>" title="<?php echo $p['title']; ?>" lay-filter="plan" <?php if($dataTitle['id']==$p['id']): ?>checked <?php endif; ?>></td>
                <td><a href="/cms/index/jump?id=<?php echo $p['id']; ?>" target="_blank" class="detail">查看鉴定公告</a></td>
            </tr>
        </table>
        <?php endforeach; endif; else: echo "" ;endif; else: if(is_array($arrExamPlan) || $arrExamPlan instanceof \think\Collection || $arrExamPlan instanceof \think\Paginator): if( count($arrExamPlan)==0 ) : echo "" ;else: foreach($arrExamPlan as $key=>$v): ?>
        <table border="0">
            <tr class="bor_btm">
                <td width="500px"><input type="radio" name="plan" class="plan" value="<?php echo $p['id']; ?>" title="<?php echo $p['title']; ?>" lay-filter="plan"></td>
                <td><a href="/cms/index/jump?id=<?php echo $p['id']; ?>" target="_blank">查看鉴定公告</a></td>
            </tr>
        </table>
        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </div>

    <div class="double_btn double_xinxi_btn">
        <button  class="dayin_btn payments borderwid " id="formSubmit" lay-submit lay-filter="formSubmit" >填写报名信息</button>
        <!--<button  class="dayin_btn payments borderwid " id="Make" lay-submit lay-filter="Make">补考</button>-->
        <span class="makeinfo"></span>
    </div>

</div>


<script type="text/javascript" src="/static/js/urladdr/urladdr.js"></script>
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/layui/lay/modules/code.js"></script>
<script type="text/javascript" src="/static/layui/layui.all.js"></script>
<script type="text/javascript" src="/static/js/jquery.cookie.js"></script>

<script type="text/javascript">
    layui.use(['form', 'layer','jquery','element'], function () {
        var $ = layui.jquery,
                form = layui.form,
              layer = layui.layer;
         $.cookie('examplan_id','',{path:'/'});
        //添加表单监听事件
            $("#formSubmit").click(function (data) {
              var  plan_id = $('input[name="plan"]:checked').val();
                if( plan_id == null){
                    layer.msg('请选择鉴定计划');
                }else {
                    $.ajax({
                        url:'/Examinee/Center/plancount',
                        type:'post',
                        data:{plan_id:plan_id},
                        dataType:'json',
                        success:function (data) {
                            if (data.code==1){
                                layer.open({
                                    type: 2,
                                    skin: 'layui-layer-rim', //加上边框
                                    area: ['90%', '90%'], //宽高
                                    title: "添加报名信息",
                                    async:false,
                                    content:  '/examinee/Center/addExamEnroll?id=' + plan_id,  //调到新增页面
                                    yes: function(index, layero){
                                        //do something
                                        layer.close(index); //如果设定了yes回调，需进行手工关闭
                                    }
                                });
                                return false;
                            }else{
                                layer.msg(data.msg,{iocn:5});
                            }
                        }
                    });
                    //防止页面跳转
                    return false;

                }
            });


        $('.cancel').on('click', function () {
            layer.msg(data.msg, {
                icon: 1,//提示的样式
                time: 2000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                end: function () {
                    $.get("<?php echo url('/examinee/center/indexcert'); ?>", function (data) {
                        window.parent.$("#iframeContent").html(data); //初始化加载界面
                        //取消遮罩的时候
                        $(".layui-layer-shade").hide();
                    });
                }
            });
        });



        form.on('radio(plan)', function(data){
            var planid = data.value;
            $.ajax({
                url:'/Examinee/Center/make',
                type:'post',
                data:{plan_id:planid},
                dataType:'json',
                success:function (data) {
                    if (data.code==0){

                    }else{
                        var htmldir ="";
                        htmldir += '<button  class="dayin_btn payments borderwid" id="make" lay-submit lay-filter="Make">补考</button>';
                        $('.makeinfo').html(htmldir);
                        form.render();
                        //点击补考
                        $("#make").on('click',function () {
                            var examid = $('input[name="plan"]:checked').val();
                            if( examid == null){
                                layer.msg('请选择鉴定计划');
                            }else{
                                layer.open({
                                    type: 2,
                                    skin: 'layui-layer-rim', //加上边框
                                    area: ['90%', '90%'], //宽高
                                    title: "补考信息",
                                    async:false,
                                    content: "/examinee/Center/makeupexam?examid="+examid,  //调到新增页面
                                    yes: function(index, layero){
                                        //do something
                                        layer.close(index); //如果设定了yes回调，需进行手工关闭
                                    }
                                });
                            }
                            return false;
                        });
                    }
                }
            });
            //防止页面跳转
            return false;
        })
    });
</script>




