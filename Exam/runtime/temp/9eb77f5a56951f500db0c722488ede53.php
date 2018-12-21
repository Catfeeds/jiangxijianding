<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:107:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\examdetail.html";i:1545017019;}*/ ?>
<style type="text/css">
    .baomingliucheng{position: absolute;width: 96%;;}
    .liuc_top {
        width: 100%;
        height: 30px;
        display: flex;
        justify-content: space-around;
        padding-left: 20px;
    }
    .liuc_top{position: relative;left: -5px;padding-top: 35px;}
    .liuc_top li {

        text-align: center;
        color: #868686;
    }

    .liuc_top>li>i {
        width: 20px;
        height: 20px;
        display: inline-block;
        text-align: center;
        vertical-align: middle;
        background-image: url(/static/layui/img/lv.png);
        background-repeat: no-repeat;
        background-color: none;
    }
    .liuc_active{padding-bottom: 10px;}
    .liuc_active>b{}
    .gerenxinxi_main{
        margin-top: 150px;
    }
    .liuc_top li a,.liuc_top2 li a{color: #868686;cursor: pointer;}
    .this_redlv{background-image: url(/static/layui/img/redlv.png) !important;}
    .this_graylv{background-image: url(/static/layui/img/graylv.png) !important;}
    .baom_title{color: red;font-size: 16px;}
    .baom_title i{display: inline-block;width: 5px;height: 5px;border-radius: 50%;background: red;margin-right: 5px;margin-bottom: 3px;}
    .this_btn_hade{border: 1px solid red;border-radius: 4px;padding: 3px 6px;}
    .this_btn_hade:hover{background: red;color: #fff;}
    .liuc_top li:nth-of-type(odd) span{position: absolute;top: 8px;}
    .liuc_top li:nth-of-type(even) span{position: absolute;top: 60px;}
    .double_qxya {
        width: 70px;
        margin: auto;
        margin-top: 60px;
    }
    .liuc_top li::after{content: "";display: inline-block;background: #ccc;height: 1px;width: 50px;    margin-bottom: 3px;}
    /*.baomingliucheng ul li::before{content: "";display: inline-block;background: #ccc;height: 1px;width: 25px;    margin-bottom: 3px;}*/
    .liuc_top li:last-child::after{content: "";display: inline-block;background: #fff;height: 1px;width: 58px;    margin-bottom: 3px;}
    .harf{position: absolute;top: 10px;left: 24px;}
    .this_btn_hades{border: 1px solid #19ab19;border-radius: 4px;padding: 3px 6px;}
    .this_btn_hades:hover{background: #19ab19;color: white;}
    .liuc_top li:nth-child(1) span{left: 5px;}
    .liuc_top li:nth-child(2) span{left: 70px;}
    .liuc_top li:nth-child(3) span{left: 135px;}
    .liuc_top li:nth-child(4) span{left: 195px;}
    .liuc_top li:nth-child(5) span{left: 295px;}
    .liuc_top li:nth-child(6) span{left: 359px;}
    .liuc_top li:nth-child(7) span{left: 437px;}

    .liuc_top li:nth-child(8) span{left: 518px;}
    .liuc_top li:nth-child(9) span{left: 579px;}
    .liuc_top li:nth-child(10) span{left: 648px;}
    .liuc_top li:nth-child(11) span{left: 710px;}
    .liuc_top li:nth-child(12) span{left: 795px;}
    /*.dayin{min-height:10px;pxpadding-left:30px;padding-top: 20px;}*/
    .liuc_top2>li>i {
        width: 20px;
        height: 20px;
        display: inline-block;
        text-align: center;
        vertical-align: middle;
        background-image: url(/static/layui/img/lv.png);
        background-repeat: no-repeat;
        background-color: none; 这个注册这个写出来 这个可以么
    }
    .liuc_top2 li::after{content: "";display: inline-block;background: #ccc;height: 1px;width: 58px;    margin-bottom: 3px;}
    /*.baomingliucheng ul li::before{content: "";display: inline-block;background: #ccc;height: 1px;width: 25px;    margin-bottom: 3px;}*/
    .liuc_top2 li:last-child::after{content: "";display: inline-block;background: #fff;height: 1px;width: 58px;    margin-bottom: 3px;}
    .liuc_top2 {
        width: 100%;
        height: 30px;
        display: flex;
        justify-content: space-around;
        padding-left: 20px;
    }
    .liuc_top2{position: relative;left: -5px;padding-top: 35px;}
    .liuc_top2 li {

        text-align: center;
        color: #868686;
    }
    .liuc_top2 li:nth-of-type(odd) span{position: absolute;top: 8px;}
    .liuc_top2 li:nth-of-type(even) span{position: absolute;top: 60px;}
    .liuc_top2 li:nth-child(1) span{left: 5px;}
    .liuc_top2 li:nth-child(2) span{left: 70px;}
    .liuc_top2 li:nth-child(3) span{left: 140px;}
    .liuc_top2 li:nth-child(4) span{left: 220px;}
    .liuc_top2 li:nth-child(5) span{left: 315px;}
    .liuc_top2 li:nth-child(6) span{left: 385px;}
    .liuc_top2 li:nth-child(7) span{left: 470px;}

    .liuc_top2 li:nth-child(8) span{left: 560px;}
    .liuc_top2 li:nth-child(9) span{left: 625px;}
    .liuc_top2 li:nth-child(10) span{left: 705px;}
    .liuc_top2 li:nth-child(11) span{left: 785px;}
    .liuc_top2 li:nth-child(12) span{left: 875px;}
</style>
<div class="header_title"><span>查看报名详情</span>
</div>
<div class="gerenxinxi">
    <label class="baom_title"><i></i>您当前的状态是:</label><?php echo $examenroll->status; if(!empty($examJoinDataThesisCount)>0): ?>
    <div style="float: right">
            <?php if($now < strtotime(date('Y-m-d',strtotime($examenroll['exam_time'].'-15day'))) && $now>strtotime(date('Y-m-d',strtotime($examenroll['exam_time'].'-22day')))): ?>
             <button class="looks_btn margin_right_10 borderwid upbth blue_btn" title="请上传以.docx后缀的文档!" thesisId="<?php echo $examenroll['id']; ?>">上传</button>
                <?php if($examJoinDataThesisCount['tid'] != null): ?>
                <button class="looks_btn margin_right_10 borderwid"><a style="color: white;" href="<?php echo $examJoinDataThesisCount['path']; ?>">下载</a></button>
                <?php endif; else: ?>
            <button class="looks_btn margin_right_10 borderwid upbth layui-bg-cyan" onclick="layer.confirm('综合评审需要进行论文答辩的考生，需在距理论考试15个工作日前1周内，提交论文至省职业技能鉴定指导中心鉴定一科（上半年5月1日前下半年11月1日前，在规定时间内未提交论文的考生视为放弃综合评审答辩考试）')" >上传</button>
            <button class="looks_btn margin_right_10 borderwid layui-bg-cyan" > <a style="color: white;">下载</a></button>
            <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="baomingliucheng">
        <p class="baom_title"><i></i>报名流程</p>
        <!--这个是有上传论文的 是全部的-->
        <!--<ul class="liuc_top">-->
            <!--<li><span>注册/登录1</span><i></i></li>-->
            <!--<li><span>完善个人信息</span><i></i></li>-->
            <!--<li><span>选择鉴定计划</span><i></i></li>-->
            <!--<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_edit_green')))): ?>-->
            <!--<li><span>编辑报考信息</span><i></i></li>-->

            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_edit_current_green')))): ?>-->
            <!--<li class="edit" value="<?php echo $examenroll['id']; ?>" ><span ><a class="this_btn_hades">编辑报考信息</a></span><i></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>编辑报考信息</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_audit_green')))): ?>-->
            <!--<li><span>提交审核</span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_audit_current')))): ?>-->
            <!--<li class="auditChick" title="审核截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['audit_endtime']); ?>,请尽快提交审核!" value="<?php echo $examenroll['id']; ?>"><span  ><img class="harf" src="/static/layui/img/harf.gif" /><a class="this_btn_hade"  >提交审核</a></span><i class="this_redlv"></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>提交审核</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_green')))): ?>-->
            <!--<li  class="printApply" value="<?php echo $examenroll['id']; ?>" ><span><a class="this_btn_hades">打印报名表</a></span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current')))): ?>-->
            <!--<li class="printApply" value="<?php echo $examenroll['id']; ?>"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade">打印报名表</a></span><i class="this_redlv"></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current_green')) && $now < strtotime($examenroll['exam_time']))): ?>-->
            <!--<li class="printApply" value="<?php echo $examenroll['id']; ?>" ><span ><a class="this_btn_hades">打印报名表</a></span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current_green')) && $now > strtotime($examenroll['exam_time']))): ?>-->
            <!--<li class="printApply" value="<?php echo $examenroll['id']; ?>" ><span ><a class="this_btn_hades">查看报名表</a></span><i></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>打印报名表</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pass_green')))): ?>-->
            <!--<li><span>审核通过</span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pass_current')))): ?>-->
            <!--<li title="等待审核通过"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >审核通过</a></span><i class="this_redlv"></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>审核通过</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pay_green')))): ?>-->
            <!--<li class="feepayment"  title="缴费截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>,请尽快完成缴费!" data-value="<?php echo $examenroll['id']; ?>" ><span><a class="this_btn_hades">已缴费</a></span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pay_current'))) && $now<$examenroll['pay_endtime']): ?>-->
            <!--<li class="feepayment"  title="缴费截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>,请尽快完成缴费!" data-value="<?php echo $examenroll['id']; ?>" ><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade">未缴费</a></span><i class="this_redlv"></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pay_current'))) && $now>$examenroll['pay_endtime']): ?>-->
            <!--<li  onclick="layer.confirm('缴费截止时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>,请尽快完成缴费!')"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >未缴费</a></span><i class="this_redlv"></i></li>-->
            <!--<?php else: ?>-->
            <!--<li onclick="layer.confirm('缴费截止时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>')" title="缴费截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>!"><span>未缴费</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if((in_array($examenroll['thesis_state'],config('ExamEnrollStatus.apply_thesis_green')))): ?>-->
            <!--<li  class="selthesis" value="<?php echo $examenroll['id']; ?>" ><span><a class="this_btn_hades">上传论文</a></span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_thesis_current')))): ?>-->
            <!--<li class="selthesis" value="<?php echo $examenroll['id']; ?>"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade">上传论文</a></span><i class="this_redlv"></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>上传论文</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_ticket_green')))): ?>-->
            <!--<li><span>下载准考证</span><i></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_ticket_current')) &&  $now > $examenroll['print_starttime'] && $now < $examenroll['print_endtime'] )): ?>-->
            <!--<li class="printExam"  title="下载准考证时间为<?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?>,请在规定时间内下载准考证" data-value="<?php echo $examenroll['id']; ?>"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >下载准考证</a></span><i class="this_redlv"></i></li>-->
            <!--<?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_ticket_current')) &&  $now < $examenroll['print_starttime'] || $now > $examenroll['print_endtime'] )): ?>-->
            <!--<li  onclick="layer.confirm('下载准考证时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?>!')"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >下载准考证</a></span><i class="this_redlv"></i></li>-->
            <!--<?php else: ?>-->
            <!--<li  onclick="layer.confirm('下载准考证时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?>!')"><span>下载准考证</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if($now > strtotime($examenroll['exam_time'])): ?>-->
            <!--<li><span>等待参加考试</span><i></i></li>-->
            <!--<?php elseif(( $now > $examenroll['print_starttime'] && $now < $examenroll['print_endtime'] )): ?>-->
            <!--<li><span><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" href="www.baidu.com">等待参加考试</a></span><i class="this_redlv"></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>等待参加考试</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->
            <!--<?php if($now > strtotime($examenroll['exam_time'])): ?>-->
            <!--<li class="selGrade"><span>查询成绩</span><i></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>查询成绩</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->

            <!--<?php if($now > strtotime($examenroll['exam_time'])): ?>-->
            <!--<li class="selCert"><span>申领证书</span><i></i></li>-->
            <!--<?php else: ?>-->
            <!--<li><span>申领证书</span><i class="this_graylv"></i></li>-->
            <!--<?php endif; ?>-->
        <!--</ul>-->
        <!--这个是没有上传论文的 是全部的-->
        <ul class="liuc_top2">
            <li><span>注册/登录</span><i></i></li>
            <li><span>完善个人信息</span><i></i></li>
            <li><span>选择鉴定计划</span><i></i></li>
            <?php if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_edit_green')))): ?>
            <li><span>编辑报考信息</span><i></i></li>

            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_edit_current_green')))): ?>
            <li class="edit" value="<?php echo $examenroll['id']; ?>" ><span ><a class="this_btn_hades">编辑报考信息</a></span><i></i></li>
            <?php else: ?>
            <li><span>编辑报考信息</span><i class="this_graylv"></i></li>
            <?php endif; if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_audit_green')))): ?>
            <li><span>提交审核</span><i></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_audit_current')))): ?>
            <li class="auditChick" title="审核截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['audit_endtime']); ?>,请尽快提交审核!" value="<?php echo $examenroll['id']; ?>"><span  ><img class="harf" src="/static/layui/img/harf.gif" /><a class="this_btn_hade"  >提交审核</a></span><i class="this_redlv"></i></li>
            <?php else: ?>
            <li><span>提交审核</span><i class="this_graylv"></i></li>
            <?php endif; if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_green')))): ?>
            <li  class="printApply" value="<?php echo $examenroll['id']; ?>" ><span><a class="this_btn_hades">打印报名表</a></span><i></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current')))): ?>
            <li class="printApply" value="<?php echo $examenroll['id']; ?>"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade">打印报名表</a></span><i class="this_redlv"></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current_green')) && $now < strtotime($examenroll['exam_time']))): ?>
            <li class="printApply" value="<?php echo $examenroll['id']; ?>" ><span ><a class="this_btn_hades">打印报名表</a></span><i></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_print_current_green')) && $now >strtotime($examenroll['exam_time']))): ?>
            <li class="printApply" value="<?php echo $examenroll['id']; ?>" ><span ><a class="this_btn_hades">查看报名表</a></span><i></i></li>
            <?php else: ?>
            <li><span>打印报名表</span><i class="this_graylv"></i></li>
            <?php endif; if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pass_green')))): ?>
            <li><span>审核通过</span><i></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pass_current')))): ?>
            <li title="等待审核通过"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >待审核</a></span><i class="this_redlv"></i></li>
            <?php else: ?>
            <li><span>未审核</span><i class="this_graylv"></i></li>
            <?php endif; if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pay_green')))): ?>
            <li class="feepayment"  title="缴费截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>,请尽快完成缴费!" data-value="<?php echo $examenroll['id']; ?>" ><span><a class="this_btn_hades">已缴费</a></span><i></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pay_current'))) && $now<$examenroll['pay_endtime']): ?>
            <li class="feepayment"  title="缴费截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>,请尽快完成缴费!" data-value="<?php echo $examenroll['id']; ?>" ><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade">未缴费</a></span><i class="this_redlv"></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_pay_current'))) && $now>$examenroll['pay_endtime']): ?>
            <li  onclick="layer.confirm('缴费截止时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>,请尽快完成缴费!')"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >待缴费</a></span><i class="this_redlv"></i></li>
            <?php else: ?>
            <li onclick="layer.confirm('缴费截止时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>!')" title="缴费截止时间为<?php echo date('Y-m-d H:i:s',$examenroll['pay_endtime']); ?>!"><span>未缴费</span><i class="this_graylv"></i></li>
            <?php endif; if((in_array($examenroll['status'],config('ExamEnrollStatus.apply_ticket_green')))): ?>
            <li><span>下载准考证</span><i></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_ticket_current')) &&  $now > $examenroll['print_starttime'] && $now < $examenroll['print_endtime'] )): ?>
            <li class="printExam"  title="下载准考证时间为<?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?>,请在规定时间内下载准考证" data-value="<?php echo $examenroll['id']; ?>"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >下载准考证</a></span><i class="this_redlv"></i></li>
            <?php elseif((in_array($examenroll['status'],config('ExamEnrollStatus.apply_ticket_current')) &&  $now < $examenroll['print_starttime'] || $now > $examenroll['print_endtime'] )): ?>
            <li  onclick="layer.confirm('下载准考证时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?>!')"><span ><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" >下载准考证</a></span><i class="this_redlv"></i></li>
            <?php else: ?>
            <li  onclick="layer.confirm('下载准考证时间为</br><?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?>!')"><span>下载准考证</span><i class="this_graylv"></i></li>
            <?php endif; if($now < strtotime(date('Y-m-d',strtotime($examenroll['exam_time'].'+day')))): ?>
            <li><span>等待参加考试</span><i></i></li>
            <?php elseif(( in_array($examenroll['status'],config('ExamEnrollStatus.apply_exam_current')) && $now > strtotime($examenroll['exam_time']) && $now < strtotime(date('Y-m-d',strtotime($examenroll['exam_time'].'-day'))) )): ?>
            <li><span><img class="harf" src="/static/layui/img/harf.gif"/><a class="this_btn_hade" title="等待参加考试">等待参加考试</a></span><i class="this_redlv"></i></li>
            <?php else: ?>
            <li><span>等待参加考试</span><i class="this_graylv"></i></li>
            <?php endif; if($now > strtotime($examenroll['exam_time'])): ?>
            <li class="selGrade"><span>查询成绩</span><i></i></li>
            <?php else: ?>
            <li><span>查询成绩</span><i class="this_graylv"></i></li>
            <?php endif; if($now >strtotime($examenroll['exam_time'])): ?>
            <li class="selCert"><span>申领证书</span><i></i></li>
            <?php else: ?>
            <li><span>申领证书</span><i class="this_graylv"></i></li>
            <?php endif; ?>
        </ul>
        </br>
        <p class="baom_title">打印准考证时间为:<?php echo date('Y-m-d H:i:s',$examenroll['print_starttime']); ?>-<?php echo date('Y-m-d H:i:s',$examenroll['print_endtime']); ?></p>
    </div>

    <div class="gerenxinxi_main">
        <p class="baom_title"><i></i>个人信息</p>
        <div class="layui-col-xs5 layui-col-md5">
            <p>姓名：<?php echo !empty($logininfo['username'])?$logininfo['username']:''; ?></p>
            <p>出生日期：<?php echo !empty($logininfo['birthday'])?$logininfo['birthday']:''; ?></p>
            <p><?php echo $logininfo->id_type; ?>：<?php echo !empty($logininfo['userpid'])?$logininfo['userpid']:''; ?></p>
            <p>联系电话：<?php echo !empty($logininfo['mobile'])?$logininfo['mobile']:''; ?></p>
            <p>通讯地址：<?php echo !empty($logininfo['address'])?$logininfo['address']:''; ?></p>
            <p>备注：<?php echo !empty($examenroll['remark'])?$examenroll['remark']:''; ?></p>
            <p>鉴定名称：<?php echo $examenroll['title']; ?></p>
            <p>报考职工(工种)：<?php echo $examenroll['workname']; ?></p>
            <?php if($examenroll->work_level_subject_level!==''): ?> <p>鉴定级别：<?php echo !empty($examenroll->work_level_subject_level)?$examenroll->work_level_subject_level:''; ?></p><?php endif; ?>
            <p>考试类型：<?php echo $examenroll->exam_type; ?></p>
            <?php if($examenroll['audit_site']!==''): ?>
            <p>现场审核地点：<?php echo !empty($examenroll['audit_site'])?$examenroll['audit_site']:''; ?></p>
            <?php endif; ?>
            <p>参加考试地点：<?php echo !empty($examenroll['exam_site'])?$examenroll['exam_site']:''; ?></p>
        </div>
        <div class="layui-col-xs4 layui-col-md4">
            <p>性别:<?php echo !empty($logininfo['gender'])?$logininfo['gender']:''; ?></p>
            <p>籍贯：<?php echo !empty($logininfo['native_place'])?$logininfo['native_place']:''; ?></p>
            <p>文化程度：<?php echo $logininfo->education; ?> </p>
            <p>邮箱：<?php echo !empty($logininfo['email'])?$logininfo['email']:''; ?></p>
            <p>所在单位：<?php echo !empty($logininfo['company'])?$logininfo['company']:''; ?></p>
            <p>邮政编码：<?php echo $logininfo['zip_code']; ?></p>
            <?php if($subjectName['subjectName']!==''): ?><p>考试科目：<?php echo $subjectName['subjectName']; ?></p><?php endif; if($examenroll['directionname']!==''&&$examenroll['directionname']!==null): ?> <p>报考职业方向：<?php echo $examenroll['directionname']; ?></p><?php endif; ?>
            <p>鉴定类别：<?php echo !empty($examenroll['work_type_name'])?$examenroll['work_type_name']:''; ?></p>
            <p>考生来源：<?php echo $examenroll->source; ?></p>
            <p>参加考试日程：<?php echo !empty($examenroll['exam_time'])?$examenroll['exam_time']:''; ?></p>
            <p>审核方式：<?php echo !empty($examenroll['audit_way'])?$examenroll['audit_way']:''; ?></p>
        </div>
        <div class="layui-col-xs3 layui-col-md3" style="height: 140px;">
            <img style="width: 120px;height: 150px;margin-top: 10px;" src="<?php echo !empty($logininfo['avatar'])?$logininfo['avatar']:''; ?>" />
        </div>

    </div>
</div>
<div class="WoYaoBaoMing layui-form" style="display: none;">
    <h2 class="tanchuang_title">打印准考证时间</h2>
    <p style="color: red;text-align: center;line-height: 30px;margin-top: 40px;">2018-09-09 09:00～2018-09-09 09:00</p>
    <p style="text-align: center;">请在规定时间内打印</p>
    <div class="double_btn double_qxya">
        <div class="dayin_btn payment margin_right_15">确定</div>
    </div>
</div>
<script>
    layui.use(['form', 'laydate', 'upload'], function() {
        var upload = layui.upload;
        var form = layui.form;
        var laydate = layui.laydate;
        $ = layui.jquery;
        form.render();
        laydate.render({
            elem: '#date' //指定元素
        });
        //执行实例
        var uploadInst = upload.render({
            elem: '#shangchuan' //绑定元素
            ,
            url: '/upload/' //上传接口
            ,
            done: function(res) {
                //上传完毕回调
            },
            error: function() {
                //请求异常回调
            }
        });


        //执行实例
        // 上传论文
        $(".blue_btn").click(function () {
           exam_enroll_id = $(this).attr('thesisId');
        });
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
                            $.get("/examinee/Center/examdetail?id="+exam_enroll_id, function(data) {
                                parent.$("#iframeContent").html(data); //初始化加载界面
//                                parent.layer.closeAll();
                            });
                        },
                    });
                } else{
                    layer.msg(data.msg,{icon:5});
                    $.get("/examinee/Center/examdetail?id="+exam_enroll_id, function(data) {
                        parent.$("#iframeContent").html(data); //初始化加载界面
//                        parent.layer.closeAll();
                    });

                }
            }
            ,error: function(data){
                //请求异常回调
                layer.msg(data.msg,{icon:5});
            }
        });

    });


    function WoYaoBaoMing() {
        layer.open({
            type: 1,
            title: false,
            shadeClose: false,
            shade: 0.8,
            area: ['300px', '250px'],
            content: $('.WoYaoBaoMing'),
            cancel: function(index, layero) {
                $(".WoYaoBaoMing").css('display', 'none');
            }
        });
    }

    //添加报考信息
    layui.use(['form', 'layer','jquery'], function () {

        var form  = layui.form;
        var $  = layui.jquery;
        $("#addExamInfo").click(function () {
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['90%', '90%'], //宽高
                title: "添加报考信息",
                content: "/examinee/Center/addExamEnroll",  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });

        //删除报考信息
        $(".delete").click(function () {
            var id = $(this).val();
            layer.confirm("确认删除?", function () {
                $.ajax({
                    url: "/api/ExamEnroll/delete",
                    data: {'id': id},
                    type: "post",
                    dataType: 'json',
                    success: function (data) {
                        if (data.code == 1) {
                            layer.msg(data.msg,{
                                icon: 1,//提示的样式
                                time:  2000, //2秒关闭（如果不配置，默认是3秒）//设置后不需要自己写定时关闭了，单位是毫秒
                                end: function () {
                                    $.get("<?php echo url('/examinee/center/workinfo'); ?>", function(data) {
                                        window.parent.$("#iframeContent").html(data); //初始化加载界面
                                        //取消遮罩的时候
                                        $(".layui-layer-shade").hide();
                                    });
                                }
                            });
                        } else {
                            layer.msg(data.msg,{icon:5});
                        }
                    }
                })
            })
        });

        //修改报考信息
        $(".edit").click(function () {
//			 layer.msg('功能暂未开放',{'icon':5});
//			 return false;
            var id = $(this).val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['60%', '90%'], //宽高
                title: "修改报考信息",
                content: "/examinee/Center/addWorkInfo?id="+id,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });

        //上传审核资料
        $(".audit").click(function () {
            var id = $(this).val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['95%', '90%'], //宽高
                title: "上传审核资料",
                content: "/examinee/Center/fileUpload?id="+id,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });


        //详情
        $(".auditDetail").click(function () {
            var id = $(this).val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['90%', '90%'], //宽高
                title: "查看报名详情",
                content: "/examinee/Center/examdetail?id="+id,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });


        //提交资格审查
        $(".auditChick").click(function () {
            var id = $(this).val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['90%', '90%'], //宽高
                title: "提交资格审查",
                content: "/examinee/Center/verifyEnroll?id="+id,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });



        //打印报名表
        $(".printApply").click(function () {
            var id = $(this).val();
            layer.open({
                type: 2,
                skin: 'layui-layer-rim', //加上边框
                area: ['90%', '90%'], //宽高
                title: "报名表",
                content: "/examinee/center/printApply?id="+id,  //调到新增页面
                yes: function(index, layero){
                    //do something
                    layer.close(index); //如果设定了yes回调，需进行手工关闭
                }
            });
        });
    });
    $('.feepayment').on('click', function () {
        id = $(this).data('value');
        layer.open({
            type: 2,
            area: ['450px', '450px'],
            title: "缴费",
            content: '/examinee/center/pay?id='+id,
        });
    });


    //打印准考证
    $(".printExam").click(function () {
        var id = $(this).data('value');
        layer.open({
            type: 2,
            skin: 'layui-layer-rim', //加上边框
            area: ['600px', '600px'], //宽高
            title: "打印准考证",
            content: "/examinee/center/printExam?id="+id,  //调到新增页面
            yes: function(index, layero){
                //do something
                layer.close(index); //如果设定了yes回调，需进行手工关闭
            }
        });
    });

    //上传论文
    $('.selthesis').click(function() { //点击li加载界面
        $.get('/examinee/center/indexthesis', function(data) {
            $("#iframeContent").html(data);
        });
    });

    //查询成绩
    $('.selGrade').click(function() { //点击li加载界面
        $.get('/examinee/center/gradeindex', function(data) {
            $("#iframeContent").html(data);
        });
    });

    //查询成绩
        $('.selCert').click(function() { //点击li加载界面
            $.get('/examinee/center/indexcert', function(data) {
                $("#iframeContent").html(data);
            });
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