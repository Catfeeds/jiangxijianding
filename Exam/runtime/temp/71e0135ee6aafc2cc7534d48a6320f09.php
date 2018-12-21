<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:111:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\printApplyinfo.html";i:1545212679;}*/ ?>
<style>
    .gaiz{
        position: absolute;right: 150px;bottom: 10px;
    }
    .txm_img{width: 160px;margin: auto;}
    .txm_img img{max-width: 160px !important;
        height:100px;}
</style>
<style type="text/css" media=print>
  .noprint{display : none}
  .tdcenter{ text-align:center;}
</style>
<style media="print">
    @page {
        size: auto;
        margin: 0mm;
    }
</style>
<body style="margin-left: 50px;margin-right: 50px">
<!--<!–startprint1–>-->
<!--打印内容开始-->
<div id=sty>
    <h1><span  class="title" style="text-align: center;display:block; margin-top: 23px;" data-value="<?php echo $title; ?>报名表"></span></h1>
    <?php if($examenroll['audit_way'] == '线上审核'): ?>
    <button class="layui-btn auditZi" data-value="<?php echo $examenroll['id']; ?>"><p class="noprint">查看上传审核资料</p></button>
    <?php endif; ?>
    <!--<div style="text-align: center"><?php echo $examenroll['title']; ?></div>-->
<table class="layui-table" width="100px" style=" border:1px solid #000000;">
    <tr class="noprint">
        <td >操作提示 </td>
        <td colspan="4">您已经提交报名，请检查所填信息，无误后可打印报名表</td>
    </tr>
    <tr>
        <input type="hidden" name="hiddenid" id="hiddenid" value="<?php echo $examenroll['id']; ?>" >
        <td class="tdcenter" width="100px">
            <?php if($logininfo['id_type'] == 1): ?>身份证
            <?php elseif($logininfo['id_type'] == 2): ?>护照
            <?php elseif($logininfo['id_type'] == 3): ?>军官证
            <?php elseif($logininfo['id_type'] == 4): ?>港澳台证
            <?php else: ?>其他
            <?php endif; ?>
        </td>
        <td colspan="3"><?php echo !empty($logininfo['userpid'])?$logininfo['userpid']:''; ?></td>
        <td rowspan="6" style="text-align: center">
            <img src="<?php echo !empty($logininfo['avatar'])?$logininfo['avatar']:''; ?>"></br></br>
           <div class="txm_img"> <img src="<?php echo url('examinee/center/barcode_create',array('content'=>$examenroll['bar_code'])); ?>"> </div>
        </td>
    </tr>
    <tr>
        <td class="tdcenter">姓名:</td>
        <td><?php echo !empty($logininfo['username'])?$logininfo['username']:''; ?></td>
        <td class="tdcenter">性别:</td>
        <td><?php echo !empty($logininfo['gender'])?$logininfo['gender']:''; ?></td>
    </tr>
    <tr>
        <td class="tdcenter">出生日期: </td>
        <td><?php echo !empty($logininfo['birthday'])?$logininfo['birthday']:''; ?></td>
        <td class="tdcenter">文化程度:</td>
        <td>
            <?php switch($logininfo['education']): case "1": ?>博士<?php break; case "2": ?>硕士<?php break; case "3": ?>研究生<?php break; case "4": ?>专科<?php break; case "5": ?>本科<?php break; case "6": ?>高职<?php break; case "7": ?>中专<?php break; case "8": ?>技校<?php break; case "9": ?>高中<?php break; case "10": ?>初中<?php break; case "11": ?>小学<?php break; endswitch; ?>
        </td>
    </tr>
    <tr>
        <td class="tdcenter">考生来源:</td>
        <td>
            <?php if($examenroll['source'] == 7): ?>  社会
            <?php elseif($examenroll['source'] == 4): ?>企业
            <?php elseif($examenroll['source'] == 5): ?>学校
            <?php elseif($examenroll['source'] == 6): ?>机关事业单位
            <?php else: ?>其他
            <?php endif; ?>
        </td>
        <td class="tdcenter">籍贯:</td>
        <td>
            <?php echo $logininfo['native_place']; ?>
        </td>
    </tr>
    <tr>
        <td class="tdcenter">所在单位:</td>
        <td colspan="3">
            <?php echo !empty($logininfo['company'])?$logininfo['company']:''; ?>
        </td>
    </tr>
    <tr>
        <td class="tdcenter">联系电话:</td>
        <td ><?php echo !empty($logininfo['mobile'])?$logininfo['mobile']:''; ?></td>
        <td class="tdcenter">邮箱:</td>
        <td><?php echo !empty($logininfo['email'])?$logininfo['email']:''; ?></td>
    </tr>
    <tr>
        <td class="tdcenter">报考职业(工种):</td>
        <td colspan="2" id="workname" data-value="<?php echo $examenroll['wid']; ?>"><?php echo $examenroll['workname']; ?></td>
        <?php if($examenroll['directionname']!==''&&$examenroll['directionname']!==null): ?>
        <td class="tdcenter">报考职业方向:</td>
        <td ><?php echo $examenroll['directionname']; ?></td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if($examenroll->work_level_subject_level!==''): ?>
        <td class="tdcenter">鉴定级别:</td>
        <td colspan="2"><?php echo !empty($examenroll->work_level_subject_level)?$examenroll->work_level_subject_level:''; ?></td>
        <?php endif; if($subjectName['subjectName']!==''): ?>
        <td class="tdcenter">考试科目:</td>
        <td><?php echo $subjectName['subjectName']; ?></td>
        <?php endif; ?>
    </tr>
    <tr>
        <td class="tdcenter">考试类型:</td>
        <td colspan="3">
            <?php if($examenroll['exam_type'] == 1): ?>  新考
            <?php else: ?>补考
            <?php endif; ?>
        </td>

    </tr>
    <tr>
        <?php if($examenroll['audit_way'] == '线下审核'): ?>
        <td class="tdcenter">现场审核地点:</td>
        <td colspan="2">
            <?php echo !empty($examenroll['audit_site'])?$examenroll['audit_site']:''; ?>
        </td>
        <?php endif; ?>
        <td class="tdcenter">参加考试日程:</td>
        <td  >
            <?php echo !empty($examenroll['exam_time'])?$examenroll['exam_time']:''; ?>
        </td>
    </tr>
    <tr>
        <td class="tdcenter">参加考试地点:</td>
        <td colspan="2">
            <?php echo !empty($examenroll['exam_site'])?$examenroll['exam_site']:''; ?>
        </td>
        <td class="tdcenter">审核方式:</td>
        <td>
            <?php echo !empty($examenroll['audit_way'])?$examenroll['audit_way']:''; ?>
        </td>
    </tr>
    <tr style="height: 115px;">
        <td class="tdcenter">诚信声明:</td>
        <td colspan="4">
            &nbsp;&nbsp;&nbsp;本人保证上述填写信息和报考时所提供的学历证、身份证等证件真实有效,如因填写有误
            或提供的证件不实而造成的后果,本人愿意承担一切责任。
            </br></br>
            <div class="gaiz"><p>本人签名:</p></div>
        </td>
    </tr>
    <tr style="height: 115px;">
        <td class="tdcenter">所在单位</br>意见:</td>
        <td colspan="4">
            <div class="gaiz">
                <p>( 单位盖章 )</p>
                <p>年&nbsp;&nbsp;&nbsp;&nbsp;月&nbsp;&nbsp;&nbsp;&nbsp;日</p>
            </div>
        </td>
    </tr>
    <tr style="height: 115px;">
        <td class="tdcenter">主管审查</br>单位意见:</td>
        <td colspan="4" >
            <div class="gaiz">
                <p>( 单位盖章 )</p>
                <p>年&nbsp;&nbsp;&nbsp;&nbsp;月&nbsp;&nbsp;&nbsp;&nbsp;日</p>
            </div>
        </td>
    </tr>

</table>
</div>
<!--打印内容结束-->
<!--<!–endprint1–>-->
<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current_green')))): ?>
<!--<button class="layui-btn" onclick=pre(1)><p class="noprint">打印报名表</p></button>-->
<?php else: ?>
<div style="text-align: center"><button class="layui-btn"  onclick=preview(1)><p class="noprint">打印报名表</p></button></div>
<?php endif; ?>
</body>

<link rel="stylesheet" href="/static/layui/css/layui.css">
<script src="/static/layui/assets/jquery-1.12.4.js"></script>
<script src="/static/layui/layui.js"></script>

    <script language="javascript">
    layui.use(['form', 'layer','jquery'], function () {
        $('.title').html($('.title').data('value'));
    });

    function preview(oper) {
         // 打印html并去掉页眉页脚
        if (!!window.ActiveXObject || "ActiveXObject" in window) {
            remove_ie_header_and_footer();
        }
//        try{
//            print.portrait   =  false    ;//横向打印
//        }catch(e){
//            //alert("不支持此方法");
//        }

        var tata=document.execCommand("print");/* window.print(); 调用打印的功能*/
        var $id= $("#hiddenid").val();
        if(tata){  //点击取消后执行的操作
            $.get("/examinee/Center/examdetail?id="+$id, function(data) {
                parent.$("#iframeContent").html(data); //初始化加载界面
                parent.layer.closeAll();
            });
            layui.use(['form', 'layer','jquery','element'], function () {
                var form = layui.form;
                var $ = layui.jquery;
                var element = layui.element;


                //添加表单监听事件
                    $.ajax({
                        url:'/api/ExamEnroll/printApplyStatus?status='+15,
                        data:{id:$id},
                        dataType:'json',
                        success:function (data) {
                            if (data.code==1) {
                                layer.msg(data.msg,{
                                    icon: 1,//提示的样式
                                    time:  2000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                    end: function () {
                                        $.get("/examinee/Center/examdetail?id="+$id, function(data) {
                                            parent.$("#iframeContent").html(data); //初始化加载界面
                                            parent.layer.closeAll();
                                        });
                                    },
                                });
                            }else{
                                $.get("/examinee/Center/examdetail?id="+$id, function(data) {
                                    parent.$("#iframeContent").html(data); //初始化加载界面
                                    parent.layer.closeAll();
                                });
                            }
                        }
                    });
                    //防止页面跳转
                    return false;
                });
        }else{
            $.get("/examinee/Center/examdetail?id="+$id, function(data) {
                parent.$("#iframeContent").html(data); //初始化加载界面
                parent.layer.closeAll();
            });
        }
    }
    function pre(oper) {
        var tataa = document.execCommand("print");
        /* window.print(); 调用打印的功能*/
        if (tataa) {
            $.get("/examinee/Center/examdetail?id="+$id, function(data) {
                parent.$("#iframeContent").html(data); //初始化加载界面
                parent.layer.closeAll();
            });
        }else{
            $.get("/examinee/Center/examdetail?id="+$id, function(data) {
                parent.$("#iframeContent").html(data); //初始化加载界面
                parent.layer.closeAll();
            });
        }
    }


    function remove_ie_header_and_footer() {
        var hkey_root, hkey_path, hkey_key;
        hkey_path = "HKEY_CURRENT_USER\\Software\\Microsoft\\Internet Explorer\\PageSetup\\";
        try {
            var RegWsh = new ActiveXObject("WScript.Shell");
            RegWsh.RegWrite(hkey_path + "header", "");
            RegWsh.RegWrite(hkey_path + "footer", "");
        } catch (e) {}
    }

    //查看上传审核资料
    $(".auditZi").click(function () {
        var id = $(this).data('value');
        layer.open({
            type: 2,
            skin: 'layui-layer-rim', //加上边框
            area: ['80%', '80%'], //宽高
            title: "查看上传审核资料",
            content: "/examinee/center/fileUpload?id="+id,  //调到新增页面
            yes: function(index, layero){
                //do something
                layer.close(index); //如果设定了yes回调，需进行手工关闭
            }
        });
    });

</script>
