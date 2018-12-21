<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:105:"D:\phpStudy\PHPTutorial\WWW\JiangXiJianDing\Exam\public/../application/examinee\view\center\examinfo.html";i:1545017019;}*/ ?>
<style type="text/css">
	.header_title a:hover {
		color: #ffffff;
	}
	.layui-form-radio{    margin: 0px 10px 0 0;}
	.double_qx2 {
		width: 115px;
		margin: auto;
		margin-top: 30px;
	}
	.layui-tab-brief>.layui-tab-title .layui-this{
		color: red;
	}
	.baom_titles{
		width: 192px;
		left: 37%;
	}
</style>
<div>
	<span class="title" data-value="<?php echo $title; ?>"></span>
	<div class="header_title"><span>报名信息</span>
		<!--<a class="dayin_btn margin_right rightfloat" onclick="WoYaoBaoMing();"><i class="layui-icon layui-icon-add-circle"></i>我要报名</a>-->
		<a class="dayin_btn margin_right rightfloat ajax-linkk" rel="/examinee/center/selectplan"><i class="layui-icon layui-icon-add-circle" ></i>我要报名</a>
		<!--<a class="dayin_btn margin_right rightfloat" id="addExamInfo">  <i class="layui-icon layui-icon-add-circle"></i>要报名</a>-->
	</div>
	<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
		<ul class="layui-tab-title baom_titles">
			<li class="layui-this">报名中</li>
			<li>历史报名</li>

		</ul>
		<div class="layui-tab-content" style="height: 100px;">
			<div class="layui-tab-item layui-show">
				<div class="baoming_main">
					<table class="layui-table myorder_table" lay-skin="line">
						<colgroup>
							<col>
							<col>
							<col>
							<col>
							<col>
							<col width="150px">
						</colgroup>
						<thead>
						<tr>
							<th>鉴定计划</th>
							<th>工种</th>
							<th>级别</th>
							<th>报名时间</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($examData) || $examData instanceof \think\Collection || $examData instanceof \think\Paginator): $i = 0; $__LIST__ = $examData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<tr>
							<td><?php echo $vo['title']; ?></td>
							<td><?php echo $vo['workname']; ?></td>
							<td><?php echo $vo->work_level_subject_level; ?></td>
							<td><?php echo $vo['create_time']; ?></td>
							<td><?php echo $vo->status; ?></td>
							<td class="double_btn">
								<div class="look_btn margin_right_10 ajax-linkk" rel="/examinee/Center/examdetail?id=<?php echo $vo['id']; ?>">详情</div>
							</td>
						</tr>

						<?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="layui-tab-item">
				<div class="baoming_main">
					<table class="layui-table myorder_table" lay-skin="line">
						<colgroup>
							<col>
							<col>
							<col>
							<col>
							<col>
							<col width="150px">
						</colgroup>
						<thead>
						<tr>
							<th>鉴定计划</th>
							<th>工种</th>
							<th>级别</th>
							<th>报名时间</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($examJoinDataPast) || $examJoinDataPast instanceof \think\Collection || $examJoinDataPast instanceof \think\Paginator): $i = 0; $__LIST__ = $examJoinDataPast;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<tr >
							<td><?php echo $v['title']; ?></td>
							<td><?php echo $v['workname']; ?></td>
							<td><?php echo $v->work_level_subject_level; ?></td>
							<td><?php echo $v['create_time']; ?></td>
							<td><?php echo $v->status; ?></td>
							<td class="double_btn">
								<div class="look_btn margin_right_10 ajax-linkk" rel="/examinee/Center/examdetail?id=<?php echo $v['id']; ?>">详情</div>
							</td>
						</tr>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>
<div class="JiaoFei_main" style="display: none;">
	<h2 class="tanchuang_title">报考费明细</h2>
	<div class="mingxi_main">
		<div class="layui-col-xs6 layui-col-md6">
			<p>报考工种：焊工</p>
			<p>等 级：二级</p>
			<p>理 论：100元</p>
			<p>综 合：100元</p>
		</div>
		<div class="layui-col-xs6 layui-col-md6">
			<p>方 向：电焊</p>
			<br />
			<p>实 操：50元</p>
			<p>论文评审：50元</p>
		</div>
	</div>
	<div class="double_btn double_qx">
		<div class="dayin_btn payment margin_right_15">确定</div>
		<div class="gray_btn payment">取消</div>
	</div>
</div>

<script type="text/javascript">
	layui.use(['form', 'laypage', 'laydate','jquery'], function() {
		var form = layui.form,
				$ = layui.jquery;
		form.render();
		var laydate = layui.laydate;
		$ = layui.jquery;
		form.render();

		var laypage = layui.laypage;

		$('title').html($('.title').data('value'));

		$('#baoming').on('click', function () {
			layer.open({
				type: 2,
				area: ['800px', '800px'],
				title: "选择计划",
				content: '/examinee/center/selectplan',
				success: function(index, layero) {
					layer.close(index);
				}
			});
		});



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
				title: "打印报名表格",
				content: "/examinee/center/printApply?id="+id,  //调到新增页面
				yes: function(index, layero){
					//do something
					layer.close(index); //如果设定了yes回调，需进行手工关闭
				}
			});
		});

	});

	function JiaoFei() {
		layer.open({
			type: 1,
			title: false,
			shadeClose: false,
			shade: 0.8,
			area: ['350px', '280px'],
			content: $('.JiaoFei_main'),
			cancel: function(index, layero) {
				$(".JiaoFei_main").css('display', 'none');
			}
		});

	}

	function WoYaoBaoMing() {
		layer.open({
			type: 1,
			title: false,
			shadeClose: false,
			shade: 0.8,
			area: ['400px', '350px'],
			content: $('.WoYaoBaoMing'),
		});
	}
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


	$('.feepayment').on('click', function () {
		id = $(this).data('value');
		layer.open({
			type: 2,
			area: ['500px', '400px'],
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

</script>