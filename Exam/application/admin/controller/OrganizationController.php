<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/11/21
 * Time: 9:07
 */
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Organization;
use app\common\service\ExamEnroll;
use app\common\service\Organize;
use app\common\service\ExamPlan;
use app\common\service\WorkLevelSubject;
use app\common\service\ExamRoom;
use app\common\service\Work;
use app\common\service\ExamSeat;
use app\common\service\ExamCard;
use app\common\service\ExamStaffLog;
use think\Request;
use think\Db;

class OrganizationController extends AdminBase
{
    private $Organization;
    private $Enroll;
    private $Organize;
    private $plan;
    private $workLevelSubject;
    private $ExamRoom;
    private $Work;
    private $Seat;
    private $ExamCard;
    private $level_name = array('1'=>'高级技师','2'=>'技师','3'=>'高级工','4'=>'中级','5'=>'初级','00'=>'考评员','01'=>'高级考评员');
    private $endFirstPage = array('0'=>'主考由考区选派，是考点的第一责任人，负责领导和组织实施本考点的全貌工作，并认真履行以下主要责任：','1'=>'一、严格按照国家和省有关职业技能鉴定考试及相关政策规定执行。熟系和掌握职业技能鉴定考试的考务工作，严格管理，终于值守，坚持原则，严格执行考试、考务各项要求的程序操作；管理考点的监考人员、流动监考人员和其他考务工作人员；落实本考点考务工作制度和岗位职责。','2'=>'二、检查考点考场布置情况，做好考前准备。','3'=>'三、考试前的一天组织考点监考人员和其他考务工作人员进行考务操作培训，明确各自职责和要求。','4'=>'四、考试前40分钟再次召集监考人员、流动监考人员进一步交代有关事宜，对每一次考试，都必须随机安排考场监考人员和流动监考人员；考试前30分钟，主持接收试卷，只会试卷管理人员向监考人员分发试卷袋、答题纸（卡）袋、座次表、草稿纸及其他考务用品。','5'=>'五、掌握考试时间和考点的考试情况，发布考试预备、开始和终止的命令。','6'=>'六、处理考试期间发生的重大问题，检查督促监考人员、流动监考人员和其他考务工作人员履行职责，执行考试纪律。对违反纪律的监考人员、流动监考人员及其它考务工作人员有权进行撤换，并提出处理意见；有权取消违反考场规则、寻衅闹事的应试人员的考试资格，责令其离开考场。对考试期间发生的异常事件或难以处理的问题，应及时向考区值班室报告。','7'=>'七、批准调换或替换因出现缺考、错装或有质量问题的考试、答题纸（卡），并和有关考场监考人员、流动监考人员在《考场情况记录但》上注明原因、签字，并向考区值班室报告。有权处置考点内未按考务规定执行的实操考核工作，但不得擅自超越权限处置考务事物。','8'=>'八、组织指导监考人员做好试卷、答题纸（卡）的装订、密封、回收工作，防止错装、漏收试卷和答题纸（卡）。','9'=>'九、在《考场情况记录单》上签名。','10'=>'十、考点副主考协助主考工作，并按分工履行职责。');
    private $endSecondPage = array('1'=>'一、熟悉流动监考人员主要职责、监考人员职责、监考规程、考场规则、违纪违规行为处理方法、组织册等规章制度。','2'=>'二、负责考场秩序及相关事宜，配合各考场监考人员处理各种突发情况。','3'=>'三、代表主考监督检查责任考场的监考人员工作情况，并负责将相关情况报告主考。','4'=>'四、流动监考人员如发现监考人员监考不严、工作不负责，应当场批评指正，情况严重者须立即报告考点主考处理。工作中如发现应试人员替考、作弊等违纪情况，应当场制止，并通知监考人员如实记录。','5'=>'五、流动监考人员认为监考人员对考试中发生的问题处理不当时，可根据有关规定，向所在考点主考反应情况，并及时提出处理意见或建议。','6'=>'六、流动监考人员发现考试过程中的重大问题应立即向考点主考报告。','7'=>'七、流动监考人员必须按时到达责任区域，监督监考人员、试卷交接、发放、回收情况；开考后应对应试人员身份，在试卷装订前负责清点试卷，并在试卷袋和《考场情况记录单》上签名。','8'=>'八、流动监考人员工作时应佩戴流动监考人员胸卡，严格执行考场纪律，不得离开和擅自变动责任区域，不在所监管的区域固定停留。','9'=>'九、流动监考人员应严格履行职责，做到“十不”：不看书报、不接打手机、不走神开小差、不滞留一处、不做题、不聊天、不睡觉、不做老好人、不将试卷带出考场、不离开责任区域。','10'=>'十、流动监考人员必须廉洁自律，不得接受与监考工作有关的宴请个馈赠。');
    private $endThirdPage = array('1'=>'一、职业技能鉴定期间、凡违反《江西省职业技能鉴定考场 规则》的行为均属违纪违规行为。','2'=>'二、应试人员在考试过程中有下列行为之一的，由鉴定机构给予该科目考试成绩无效的处理：','3'=>'（一）将规定以外的物品带入考场且未按要求放在指定位置的；','4'=>'（二）未在规定座位参加考试，或未经工作人员允许擅自离开座位或者考场的；','5'=>'（三）经提醒仍不按照规定填写（填涂）本人信息的；','6'=>'（四）未用规定的纸、笔作答的；','7'=>'（五）故意损毁试卷、答题纸、答题卡，或者将试卷、答题纸、答题卡带出考场的；','8'=>'（六）在答卷（答题卡）上做特殊标记的；','9'=>'（七）未在答题卡（纸）规定位置上答题的，或者未用现代汉语作答的（试卷中特别指明的除外）；','10'=>'（八）以旁窥、交头接耳、打手势等方式传递信息的；','11'=>'（九）其他一般违纪违规行为。','12'=>'三、应试人员在考试过程中有下列行为之一的，由鉴定机构给予取消考试资格，且当次全部科目成绩无效的处理，并将违纪违规情况予以通报，触犯国家法律的应移交司法、公安部门处理：','13'=>'（一）伪造、涂改证件、证明或以不正当手段获取考试资格的；','14'=>'（二）违反规定翻阅资料，或使用手机等规定以外工具的；','15'=>'（三）互相交换试卷、答题纸、答题卡、草稿纸等的；','16'=>'（四）抄袭、协助他人抄袭试题答题纸与考试内容相关资料的；','17'=>'（五）让他人冒名顶替参加考试的；','18'=>'（六）在考试未结束前，出卖数据答案的；','19'=>'（七）与考试工作人员串通作弊或参与有组织作弊的；','20'=>'（八）将无线耳机、无线接收器等高科技作弊设备带入座位并使用的；','21'=>'（九）不服从监考人员管理，无理取闹；扰乱考场秩序，辱骂监考人员，威胁他人安全的；','22'=>'（十）其他严重违纪违规行为的。');

    public function __construct()
    {
        parent::__construct();
        $this->Organization = new Organization();
        $this->Enroll = new ExamEnroll();
        $this->Organize = new Organize();
        $this->plan = new ExamPlan();
        $this->workLevelSubject = new WorkLevelSubject();
        $this->ExamRoom = new ExamRoom();
        $this->Work = new work();
        $this->Seat = new ExamSeat();
        $this->ExamCard = new ExamCard();
        $this->ExamStaffLog = new ExamStaffLog();
    }

    public function index(Request $request)
    {
        $message['type'] = '组织册管理';
        $message['code'] = '4';
        $list = action('CardController/getPlanMessage');
        foreach ($list as $k=>$v)
        {
            if(file_exists('download/plan/'.$v['id'].'.doc'))
            {
                $list[$k]['code'] = 402;
                $list[$k]['message'] = '下载组织册';
                $list[$k]['url'] = '/download/plan/'.$request->param('plan').'.doc';
            }else{
                $list[$k]['code'] = 401;
                $list[$k]['message'] = '生成组织册';
                $list[$k]['url'] = '/admin/organization/generate';
            }
        }
        return $this->thisView('card/index',['list'=>$list,'message'=>$message,'request'=>$request]);
    }

    /**
     * @param Request $request
     * @return \think\response\View
     * 已废弃
     */
    public function PlanProductEdit(Request $request)
    {
        $result = $this->Organization->getBase(array('exam_plan_id'=>$request->param('plan')));
        return view('index',['request'=>$request,'result'=>$result]);
    }

    /**
     * @param Request $request
     * 已废弃
     */
    public function saveOrganization(Request $request)
    {
        $data['prompt'] = $request->param('prompt');
        $data['patrol_leader'] = $request->param('patrol_leader');
        $data['exam_director'] = $request->param('exam_director');
        $data['exam_deputy_director'] = $request->param('exam_deputy_director');
        $data['supervision'] = $request->param('supervision');
        $data['on_duty'] = $request->param('on_duty');
        $data['on_duty_name'] = $request->param('on_duty_name');
        $data['staff_member'] = $request->param('staff_member');
        $data['exam_plan_id'] = $request->param('plan');
        if($request->param('id'))
        {
            $result = $this->Organization->baseUpdateInfo($data,array('id'=>$request->param('id')));
        }else{
            $result = $this->Organization->saveAllInfo($data);
        }
        if($result)
        {
            $this->success('提交成功',url('/admin/organization/PlanProductEdit?plan='.$request->param('plan')));
        }else{
            $this->error('提交失败');
        }
    }

    public function illegalHandling(Request $request)
    {
        $this->postHtml($request);
    }

    public function generate(Request $request)
    {
        return $this->postHtml($request);
    }

    /**
     * @param $request
     * @return string
     * 考核鉴定岗位人员安排
     */
    public function postHtml($request)
    {

        $html = $this->firstPage($request);
        $this->start();
        $dir = 'download/plan';
        if (!file_exists($dir)){
            mkdir ($dir,0777,true);
        }
        $wordname = $dir.'/'.$request->param('plan').'.doc';
        echo $html;
        if($this->saveWord($wordname))
        {
            $data['code'] = 200;
        }else{
            $data['code'] = 100;
        }
        return $data;
    }

    public function start()
    {
        ob_start();
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
              <head>
                   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                   <xml><w:WordDocument><w:View>Print</w:View></xml>
            </head><body>';
    }

    public function saveWord($path)
    {
        echo "</body></html>";
        $data = ob_get_contents();
        ob_end_clean();
        return $this->wirtefile ($path,$data);
    }

    public function wirtefile($fn,$data)
    {
        $fp=fopen($fn,"wd");
        fwrite($fp,$data);
        return fclose($fp);
    }

    public function firstPage($request)
    {
        $list = $this->ClanOrganizeCount($request->param('plan'));
        $organizeCount = count($list['list']);
        $total = 0;
        $str = 0;
        foreach($list['list'] as $k=>$v)
        {
            $str = $str.$v['organize']['name'].$v['count']['cut'].'人';
            $total = $total+$v['count']['cut'];
        }
        $html='
        <table width=300 cellpadding="6" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse"> 
        <tr > 
            <td style="border-left:1px solid #000;border-right: 1px solid #000;border-top: 1px solid #000;">
                温馨提示：
            </td> 
        </tr> 
        <tr> 
            <td style="border-left:1px solid #000;border-right: 1px solid #000;border-bottom: 1px solid #000;">
                提示内容<br>
                提示内容<br>
                提示内容
            </td> 
        </tr> 
        </table> 
        <h1 style="text-align: center;color: #ffffff;">组</h1>
        <h1 style="text-align: center">组织册标题<br>组织册标题</h1>
        <h3 style="text-align: center">鉴定计划时间类型说明</h3>
        <h1 style="text-align: center;color: #ffffff;">组</h1>
        <h1 style="text-align: center;color: #ffffff;">组</h1>
        <h1 style="text-align: center">组</h1>
        <h1 style="text-align: center">织</h1>
        <h1 style="text-align: center">册</h1>
        <h1 style="text-align: center;color: #ffffff;">组</h1>
        <h1 style="text-align: center;color: #ffffff;">组</h1>
        <h3 style="text-align: center">首页页脚<br>首页页脚</h3>
        <h1 style="text-align: center;color: #ffffff;">组</h1>
        <p style="page-break-after: always;">&nbsp;</p>
        <table width=100% cellpadding="3" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 16">
            <tr>
                <td style="text-align: left;">
                    <span><span style="text-align: center;color: #ffffff;"></span><strong>一、理论考核鉴定时间：</strong><span>'.date('m年d日 H:i:s',$list['plan']['theory_start_time']).'-'.date('H:i:s',$list['plan']['theory_end_time']).'</span></p>
                </td>
            </tr>
            <tr>
                <td style="text-align: left">
                    <span><span style="text-align: center;color: #ffffff;">一、</span><strong>综合考核鉴定时间：</strong><span>'.date('m年d日 H:i:s',$list['plan']['comprehen_start_time']).'-'.date('H:i:s',$list['plan']['comprehen_end_time']).'</span></p>
                </td>
            </tr>
            <tr>
                <td style="text-align: left">
                    <span><span style="text-align: center;color: #ffffff;">一、</span><strong>实操考核鉴定时间：</strong><span>'.date('m年d日 H:i:s',$list['plan']['operation_start_time']).'-'.date('H:i:s',$list['plan']['operation_end_time']).'</span></p>
                </td>
            </tr>
            <tr>
                <td style="text-align: left">
                    <span style="text-align: center;color: #ffffff;">一、</span>具体安排详见附件一《理论考核鉴定时间安排表》
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <strong>二、考点安排:(共'.count($organizeCount).'个考点)</strong>'.$str.'
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <strong>三、巡视领导:</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <strong>四、考区主任:</strong>
                </td>
            </tr>
            <tr>    
                <td style="text-align: left;">
                    <strong><span style="text-align: center;color: #ffffff;">一、</span>考副区主任:</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <strong>六、参考人数：</strong>共计'.$total.'人
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <strong>七、相关工作安排及责任人：</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <h2>岗位人员工作要求</h2>
                </td>
            </tr>
        </table>
        <table width=100% cellpadding="6" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 14">
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center"><strong>工作岗位</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: center">时间安排及要求</td>
            </tr>
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center;"><strong>总值班<br>具体人员</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: left;font-size: 12">负责汇总、解答鉴定组织实施中的问题。值班时间：开考前1小时至考试结束。联系电话（）</td>
            </tr>
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center;"><strong>主考<br>副主考</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: left;font-size: 12">
                    <table style="font-size: 14">
                        <tr>
                            <td valign="top">1、</td>
                            <td>开考前60分钟到达考点并按照要求组织召开考务人员会议，提出要求和注意事项。</td>
                        </tr>
                        <tr>
                            <td valign="top">2、</td>
                            <td>按照《考核鉴定时间安排表》规定的时限组织实施考核鉴定。</td>
                        </tr>
                        <tr>
                            <td valign="top">3、</td>
                            <td>按《江西省职业技能鉴定管理制度》要求，履行相关职责。</td>
                        </tr>
                        <tr>
                            <td valign="top">4、</td>
                            <td>考核鉴定结束后，在考务办清点核查答题卡、试卷数量，并在情况记录单、试卷袋和答题卡袋上签名（如考场较多，由流动监考代理主考清点试卷、答题卡）。</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center;"><strong>流动监考</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: left;font-size: 12">
                    <table style="font-size: 12">
                        <tr>
                            <td valign="top">1、</td>
                            <td>按照要求准确及时将试卷分发至所在考点</td>
                        </tr>
                        <tr>
                            <td valign="top">2、</td>
                            <td>按《江西省职业技能鉴定管理制度》要求履行相关职责，具体监考考场由主考临时安排</td>
                        </tr>
                        <tr>
                            <td valign="top">3、</td>
                            <td>考核鉴定结束后，协助主考清点试卷、答题卡</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center;"><strong>监考</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: left;font-size: 12">
                    <table style="font-size: 12">
                        <tr>
                            <td valign="top">1、</td>
                            <td>开考前60分钟到达考点参加考务人员会议，领取试卷。</td>
                        </tr>
                        <tr>
                            <td valign="top">2、</td>
                            <td>按《江西省职业技能鉴定管理制度》要求，履行相关职责；具体监考考场由主考临时安排。</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center;"><strong>考点门卫</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: left;font-size: 12">
                    <table style="font-size: 12">
                        <tr>
                            <td valign="top" style="color: #ffffff">1、</td>
                            <td>考点门卫早上7：50前到岗，负责考核鉴定期间考点内清场，处理突发事件，审查考生凭准考证进入考点，工作人员和工作车辆凭省鉴定指导中心制法的专用证牌进入考点，无关人员一律不得进入。</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 18%;border: 1px solid #000;text-align: center;"><strong>试卷工作人员</strong></td>
                <td style="width: 82%;border: 1px solid #000;text-align: left;font-size: 12">
                   <table style="font-size: 12">
                        <tr>
                            <td valign="top" style="color: #ffffff">1、</td>
                            <td>开考前2小时在省中心负责分发各考点试卷，考核鉴定结束后将各考点试卷统一回收保管。（联系电话：88313540、8333310、13767019801、13133916527）</td>
                        </tr>
                    </table> 
                </td>
            </tr>
        </table>';

        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: center">理论考核鉴定岗位人员安排</h2>
        <table width=100% cellpadding="6" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed">
            <tr>
                <td style="width: 2%;border: 1px solid #000">序号</td>
                <td style="width: 18%;border: 1px solid #000;word-wrap:break-word;">考点名称<br>（参考人数）</td>
                <td style="width: 10%;border: 1px solid #000">主考</td>
                <td style="width: 10%;border: 1px solid #000">副主考</td>
                <td style="width: 10%;border: 1px solid #000">质量<br>督导员</td>
                <td style="width: 10%;border: 1px solid #000">流动<br>监考</td>
                <td style="width: 10%;border: 1px solid #000">试卷接<br>送人员</td>
                <td style="width: 10%;border: 1px solid #000">监考人数</td>
                <td style="width: 20%;border: 1px solid #000">考点联系人<br>及电话</td>
            </tr>
';
        foreach($list['list'] as $k=>$v)
        {
            $main = '';
            $deputy = '';
            $supervision = '';
            $flow = '';
            $transfer = '';
            $contact = '';

            $staff = Db::table('exam_staff_log')->where(array('exam_plan_id'=>$request->param('plan'),'site_id'=>$v['site_id']))->select();
//            $strff_m = $this->ExamStaffLog->getall(array('exam_plan_id'=>$request->param('plan'),'site_id'=>$v['exam_site_id']));
//            foreach ($strff_m as $kst=>$vst)
//            {
//                echo $this->ExamStaffLog->getData('type');
//            }
//            die;
            foreach($staff as $kst=>$vst)
            {
                if($vst['type'] == 1)
                {
                    $main = $vst['name'];
                }elseif($vst['type'] == 7)
                {
                    $deputy = $vst['name'];
                }elseif($vst['type'] == 2)
                {
                    $flow = $vst['name'];
                }elseif ($vst['type'] == 8)
                {
                    $transfer = $vst['name'];
                }elseif($vst['type'] == 4)
                {
                    $supervision = $vst['name'];
                }
            }
            if($v['lilun'] ==1)
            {
                $html = $html.'<tr>';
                $html = $html.'<td style="border: 1px solid #000">'.($k+1);
                $html = $html.'</td>';
                $html = $html.'<td style="width: 100px;border: 1px solid #000;word-wrap:break-word;">'.$v['organize']['name'].'<br>'.$v['count']['cut'].'人';
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$main;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$deputy;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$supervision;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$flow;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$transfer;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000"> ';
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$contact;
                $html = $html.'</td>';
                $html = $html.'</tr>';
            }
        }

        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: center">综合考核鉴定岗位人员安排</h2>
        <table width=100% cellpadding="6" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed">
            <tr>
                <td style="width: 2%;border: 1px solid #000">序号</td>
                <td style="width: 18%;border: 1px solid #000;word-wrap:break-word;">考点名称<br>（参考人数）</td>
                <td style="width: 10%;border: 1px solid #000">主考</td>
                <td style="width: 10%;border: 1px solid #000">副主考</td>
                <td style="width: 10%;border: 1px solid #000">质量<br>督导员</td>
                <td style="width: 10%;border: 1px solid #000">流动<br>监考</td>
                <td style="width: 10%;border: 1px solid #000">试卷接<br>送人员</td>
                <td style="width: 10%;border: 1px solid #000">监考人数</td>
                <td style="width: 20%;border: 1px solid #000">考点联系人<br>及电话</td>
            </tr>
';
        foreach($list['list'] as $k=>$v)
        {
            $main = '';
            $deputy = '';
            $supervision = '';
            $flow = '';
            $transfer = '';
            $contact = '';

            $staff = Db::table('exam_staff_log')->where(array('exam_plan_id'=>$request->param('plan'),'site_id'=>$v['site_id']))->select();
            foreach($staff as $kst=>$vst)
            {
                if($vst['type'] == 1)
                {
                    $main = $vst['name'];
                }elseif($vst['type'] == 7)
                {
                    $deputy = $vst['name'];
                }elseif($vst['type'] == 2)
                {
                    $flow = $vst['name'];
                }elseif ($vst['type'] == 8)
                {
                    $transfer = $vst['name'];
                }elseif($vst['type'] == 4)
                {
                    $supervision = $vst['name'];
                }
            }

            if($v['lilun'] ==1)
            {
                $html = $html.'<tr>';
                $html = $html.'<td style="border: 1px solid #000">'.($k+1);
                $html = $html.'</td>';
                $html = $html.'<td style="width: 100px;border: 1px solid #000;word-wrap:break-word;">'.$v['organize']['name'].'<br>'.$v['count']['cut'].'人';
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$main;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$deputy;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$supervision;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$flow;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$transfer;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000"> ';
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$contact;
                $html = $html.'</td>';
                $html = $html.'</tr>';
            }
        }

        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: center">实操考核鉴定岗位人员安排</h2>
        <table width=100% cellpadding="6" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed">
            <tr>
                <td style="width: 2%;border: 1px solid #000">序号</td>
                <td style="width: 18%;border: 1px solid #000;word-wrap:break-word;">考点名称<br>（参考人数）</td>
                <td style="width: 10%;border: 1px solid #000">主考</td>
                <td style="width: 10%;border: 1px solid #000">副主考</td>
                <td style="width: 10%;border: 1px solid #000">质量<br>督导员</td>
                <td style="width: 10%;border: 1px solid #000">流动<br>监考</td>
                <td style="width: 10%;border: 1px solid #000">试卷接<br>送人员</td>
                <td style="width: 10%;border: 1px solid #000">监考人数</td>
                <td style="width: 20%;border: 1px solid #000">考点联系人<br>及电话</td>
            </tr>
';
        foreach($list['list'] as $k=>$v)
        {
            $main = '';
            $deputy = '';
            $supervision = '';
            $flow = '';
            $transfer = '';
            $contact = '';

            $staff = Db::table('exam_staff_log')->where(array('exam_plan_id'=>$request->param('plan'),'site_id'=>$v['site_id']))->select();
            foreach($staff as $kst=>$vst)
            {
                if($vst['type'] == 1)
                {
                    $main = $vst['name'];
                }elseif($vst['type'] == 7)
                {
                    $deputy = $vst['name'];
                }elseif($vst['type'] == 2)
                {
                    $flow = $vst['name'];
                }elseif ($vst['type'] == 8)
                {
                    $transfer = $vst['name'];
                }elseif($vst['type'] == 4)
                {
                    $supervision = $vst['name'];
                }
            }

            if($v['lilun'] ==1)
            {
                $html = $html.'<tr>';
                $html = $html.'<td style="border: 1px solid #000">'.($k+1);
                $html = $html.'</td>';
                $html = $html.'<td style="width: 100px;border: 1px solid #000;word-wrap:break-word;">'.$v['organize']['name'].'<br>'.$v['count']['cut'].'人';
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$main;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$deputy;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$supervision;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$flow;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$transfer;
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000"> ';
                $html = $html.'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$contact;
                $html = $html.'</td>';
                $html = $html.'</tr>';
            }
        }
        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: center">江西省职业技能鉴定主考守则</h2>';
        $html = $html.'<table width=100% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 17;text-align: center;table-layout:fixed;">';
        $space = '<span style="color: #ffffff">江西</span>';

        foreach($this->endFirstPage as $k=>$v)
        {
            $html = $html.'<tr><td style="text-align: left">'.$space.$v.'</td></tr>';
        }
        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: center">江西省职业技能鉴定流动监考人员守则</h2>';
        $html = $html.'<table width=100% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 17;text-align: center;table-layout:fixed;">';
        foreach($this->endSecondPage as $k=>$v)
        {
            $html = $html.'<tr><td style="text-align: left">'.$space.$v.'</td></tr>';
        }
        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: center">江西省职业技能鉴定应试人员<br>违纪违规行为处理办法</h2>';
        $html = $html.'<table width=100% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 17;text-align: center;table-layout:fixed;">';
         foreach($this->endThirdPage as $k=>$v)
         {
             $html = $html.'<tr><td style="text-align: left">'.$space.$v.'</td></tr>';
         }

        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: left">附件一：</h2>';
        $html = $html.'<h2 style="text-align: center;width: 800px">全省统一职业技能鉴定理论考核鉴定时间安排表</h2>';
        $html = $html.'<table width=100% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;vertical-align: middle">';
        $html = $html.'<tr>
                            <td style="border: 1px solid #000;width: 30%;height:70px;">时间</td>
                            <td style="border: 1px solid #000;width: 50%">工作安排</td>
                            <td style="border: 1px solid #000;width: 20%">备注</td>
                            </tr>';
        $html = $html.'</table>';
        $html = $html.'<table width=100% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
        $html = $html.'<tr>
                        <td style="border-left: 1px solid #000;border-right: 1px solid #000;width: 98px;height: 12%"></td>
                        <td style="border : 1px solid #000;width: 9%">7:00</td>
                        <td style="border: 1px solid #000;width: 50%">各考点主考、流动监考到达省中心领取试卷</td>
                        <td style="border: 1px solid #000;width: 20%"></td>
                       </tr>';
        $html = $html.'<tr>
                            <td style="border-left: 1px solid #000;border-right: 1px solid #000;width: 98px;height: 70px"></td>
                            <td style="border : 1px solid #000;width: 48px">8:00</td>
                            <td style="border: 1px solid #000;width: 350px">监考员、工作人员到达考点考务办公司</td>
                            <td style="border: 1px solid #000;width: 80px"></td>
                       </tr>';
        $html = $html.'<tr>
                            <td style="border-left: 1px solid #000;border-right: 1px solid #000;width: 98px;height: 70px"></td>
                            <td style="border : 1px solid #000;width: 48px">8:30</td>
                            <td style="border: 1px solid #000;width: 350px">监考员领取试卷</td><td style="border: 1px solid #000;width: 80px"></td>
                        </tr>';

        $html = $html.'<tr>
                            <td style="border-left: 1px solid #000;border-right: 1px solid #000;width: 98px;height: 70px">10月28日上午</td>
                            <td style="border : 1px solid #000;width: 50px">8:40</td>
                            <td style="border: 1px solid #000;width: 350px">考生进入考场、监考员宣读考核鉴定有关规定</td>
                            <td style="border: 1px solid #000;width: 80px">短音铃</td>
                       </tr>';
        $html = $html.'<tr>
                            <td style="border-left: 1px solid #000;border-right: 1px solid #000;width: 98px;height: 70px"></td>
                            <td style="border : 1px solid #000;width: 48px">8:50</td>
                            <td style="border: 1px solid #000;width: 350px">考核鉴定预备</td>
                            <td style="border: 1px solid #000;width: 80px">短音铃</td>
                        </tr>';
        $html = $html.'<tr>
                            <td style="border-left: 1px solid #000;border-right: 1px solid #000;width: 98px;height: 70px"></td>
                            <td style="border : 1px solid #000;width: 48px">9:00</td>
                            <td style="border: 1px solid #000;width: 350px">开始考核鉴定</td>
                            <td style="border: 1px solid #000;width: 80px">长音铃</td>
                        </tr>';
        $html = $html.'<tr>
                            <td style="border-left: 1px solid #000;border-right: 1px solid #000;border-bottom: 1px solid #000;width: 98px;height: 70px"></td>
                            <td style="border : 1px solid #000;width: 48px">10:30</td>
                            <td style="border: 1px solid #000;width: 350px">考核鉴定结束</td>
                            <td style="border: 1px solid #000;width: 80px">长音铃</td>
                       </tr>';
        $html = $html.'</table>';
        $html = $html.'<table width=100% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
        $html = $html.'<tr>
                            <td style="width: 10%px;" valign="top">说明：</td><td style="text-align: left;width: 90%">理论考核鉴定一律使用2B铅笔在答题卡上填涂作答，未使用2B铅笔填涂的按零分处理。考试凭准考证和本人身份证进入考场。出按规定可携带的文具以外，严禁将各种电子、通信、计算、存储或其他设备带至座位，已带入考场的要按监考人员的要求切点电源并放在指定位置。配发草稿纸，考后回收。考生迟到30分钟不得进场。考核鉴定结束后，考生方可交卷。严禁将答题卡、时间、试卷草稿纸带出考场。监考员必须在试卷和答题卡封条上签名。</td
                       </tr>';
        $html = $html.'</table>';
        $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        $html = $html.'<h2 style="text-align: left">附件二：</h2>';

        foreach ($list['list'] as $k=>$v)
        {
            $html = $html.'<h2 style="text-align: center">全省统一职业技能鉴定理论考试考点考场安排表</h2>';
            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold">考核鉴定地点：</span><span>'.$v['organize']['name'].'</span></td></tr>';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold">考核鉴定时间：</span><span>'.date('H:i:s',$list['plan']['theory_start_time']).'-'.date('H:i:s',$list['plan']['theory_end_time']).'</span></td></tr>';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold">考核等级及人数：</span><span>'.$v['count']['cut'].'</span></td></tr>';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold"></span>';
            foreach($this->getseatCount($v['exam_plan_id'],$v['organize_id'],'LiLun') as $kk=>$vv)
            {
                $html = $html.$vv['level'].':'.$vv['name'].' '.$vv['count'].'人、';
            }
            $html = $html.'</td></tr>';
            $html = $html.'</table>';

            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
            $html = $html.'<tr>
                        <td style="width: 5%;border: 1px solid #000">考场</td>
                        <td style="width: 10%;border: 1px solid #000">职业</td>
                        <td style="width: 8%;border: 1px solid #000">等级</td>
                        <td style="width: 22%;border: 1px solid #000">考场起始号</td>
                        <td style="width: 22%;border: 1px solid #000">考场终止号</td>
                        <td style="width: 8%;border: 1px solid #000">人数</td>
                        <td style="width: 10%;border: 1px solid #000">
                            <table>
                                <tr><td style="border-bottom: 1px solid #000">领</td><td style="border-bottom: 1px solid #000">劵</td></tr>
                                <tr><td style="border-right: 1px solid #000">试卷</td><td style="text-align: center">答题卡</td></tr>
                            </table>
                        </td>
                        <td style="width: 10%;border: 1px solid #000"><table>
                                <tr><td style="border-bottom: 1px solid #000">回</td><td style="border-bottom: 1px solid #000">收</td></tr>
                                <tr><td style="border-right: 1px solid #000">试卷</td><td style="text-align: center">答题卡</td></tr>
                            </table></td></tr>';
            //获取考场列表
            $result = $this->getRoom($v['exam_plan_id'],$v['organize_id']);
            foreach ($result as $ks=>$vs)
            {
                $data_work_level = $this->roomInfo($vs,'LiLun');
                $html = $html.'<tr>';
                $html = $html.'<td style="border: 1px solid #000">'.($ks+1).'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['name'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['level'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['start'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['end'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$vs['seatCount'].'</td>';
                $html = $html.'<td style="border: 1px solid #000"></td>';
                $html = $html.'<td style="border: 1px solid #000"></td>';
                $html = $html.'</tr>';
            }
            $html = $html.'</table>';
            $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        }
        $html = $html.'<h2 style="text-align: left">附件三：</h2>';

        foreach ($list['list'] as $k=>$v)
        {
            $html = $html.'<h2 style="text-align: center">全省统一职业技能鉴定综合考试考点考场安排表</h2>';
            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold">考核鉴定地点：</span><span>'.$v['organize']['name'].'</span></td></tr>';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold">考核鉴定时间：</span><span>'.date('H:i:s',$list['plan']['comprehen_start_time']).'-'.date('H:i:s',$list['plan']['comprehen_end_time']).'</span></td></tr>';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold">考核等级及人数：</span><span>'.$v['count']['cut'].'</span></td></tr>';
            $html = $html.'<tr><td style="text-align: left;padding-left: 10px"><span style="font-weight:bold"></span>';
            foreach($this->getseatCount($v['exam_plan_id'],$v['organize_id'],'ZongHe') as $kk=>$vv)
            {
                $html = $html.$vv['level'].':'.$vv['name'].' '.$vv['count'].'人、';
            }
            $html = $html.'</td></tr>';
            $html = $html.'</table>';

            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
            $html = $html.'<tr>
                        <td style="width: 5%;border: 1px solid #000">考场</td>
                        <td style="width: 10%;border: 1px solid #000">职业</td>
                        <td style="width: 8%;border: 1px solid #000">等级</td>
                        <td style="width: 22%;border: 1px solid #000">考场起始号</td>
                        <td style="width: 22%;border: 1px solid #000">考场终止号</td>
                        <td style="width: 8%;border: 1px solid #000">人数</td>
                        <td style="width: 10%;border: 1px solid #000">
                            <table>
                                <tr><td style="border-bottom: 1px solid #000">领</td><td style="border-bottom: 1px solid #000">劵</td></tr>
                                <tr><td style="border-right: 1px solid #000">试卷</td><td style="text-align: center">答题卡</td></tr>
                            </table>
                        </td>
                        <td style="width: 10%;border: 1px solid #000"><table>
                                <tr><td style="border-bottom: 1px solid #000">回</td><td style="border-bottom: 1px solid #000">收</td></tr>
                                <tr><td style="border-right: 1px solid #000">试卷</td><td style="text-align: center">答题卡</td></tr>
                            </table></td></tr>';
            //获取考场列表
            foreach ($this->getRoom($v['exam_plan_id'],$v['organize_id']) as $ks=>$vs)
            {
                $data_work_level = $this->roomInfo($vs,'ZhuanYe');
                $html = $html.'<tr>';
                $html = $html.'<td style="border: 1px solid #000">'.$vs['id'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['name'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['level'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['start'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$data_work_level['end'].'</td>';
                $html = $html.'<td style="border: 1px solid #000">'.$vs['seatCount'].'</td>';
                $html = $html.'<td style="border: 1px solid #000"></td>';
                $html = $html.'<td style="border: 1px solid #000"></td>';
                $html = $html.'</tr>';
            }
            $html = $html.'</table>';
            $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        }
        $html = $html.'<h2 style="text-align: left">附件四：</h2>';
        foreach ($list['list'] as $k=>$v)
        {
            $html = $html.'<h2 style="text-align: center">全省统一职业技能鉴定实操技能考核安排表</h2>';
            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 14;text-align: center;table-layout:fixed;">';
            $html = $html.'<tr>
                            <td style="text-align: left;padding-left: 10px">
                                <span style="font-weight:bold">主考：王勇  副主考：耀华   考点联系人：王勇   联系电话：1380850146</span></td>
                       </tr>';
            $html = $html.'<tr>
                            <td style="text-align: left;padding-left: 10px">
                                <span style="font-weight:bold">报考单位（考试地点）：</span><span>'.$v['organize']['name'].'</span>
                            </td>
                       </tr>';
            $html = $html.'<tr>
                            <td style="text-align: left;padding-left: 10px">
                                <span style="font-weight:bold">考试时间：</span><span>'.date('Y年m月d日',$list['plan']['operation_end_time']).'-'.date('Y年m月d日',$list['plan']['operation_start_time']).'</span>
                            </td>
                       </tr>';
            $html = $html.'<tr>
                            <td style="text-align: left;padding-left: 10px">
                                <span style="font-weight:bold">考试人数：</span><span>';
            foreach($this->getseatCount($v['exam_plan_id'],$v['organize_id'],'LiLun') as $kk=>$vv)
            {
                $html = $html.$vv['level'].':'.$vv['name'].' '.$vv['count'].'人、';
            }
           $html = $html.'</span>
                            </td>
                      </tr>';
            $html = $html.'</table>';
            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
            foreach ($this->getRoom($v['exam_plan_id'],$v['organize_id']) as $ks=>$vs)
            {
                $data_work_level = $this->roomInfo($vs,'ZongHe');
                $html = $html . '<tr>
                            <td style="border: 1px solid #000;width: 20%"><span style="font-weight:bold">时间</span></td>
                            <td style="border: 1px solid #000;width: 20%"><span style="font-weight:bold">实操具体地点</span></td>
                            <td style="border: 1px solid #000;width: 18%"><span style="font-weight:bold">职业<br>（工种）</span></td>
                            <td style="border: 1px solid #000;width: 10%"><span style="font-weight:bold">人数</span></td>
                            <td style="border: 1px solid #000;width: 12%"><span style="font-weight:bold">考评员</span></td>
                            <td style="border: 1px solid #000;width: 20%"><span style="font-weight:bold">监考人员</span></td>
                          </tr>';
                $html = $html . '<tr>
                            <td style="border: 1px solid #000;width: 150px">10月30日</td>
                            <td style="border: 1px solid #000;width: 100px">实训大厅</td>
                            <td style="border: 1px solid #000;width: 100px">'.$data_work_level['name'].'</td>
                            <td style="border: 1px solid #000;width: 100px">'.$vs['seatCount'].'人</td>
                            <td style="border: 1px solid #000;width: 100px">马钰<br>丘处机<br>谭处端<br>王处一<br>郝大通<br>刘处玄<br>孙不二</td>
                            <td style="border: 1px solid #000;width: 100px">王重阳</td>
                           <tr>';
            }
            $html = $html.'</table>';
            $html = $html.'<table width=95% cellpadding="2" cellspacing="1" style="border:1px solid #000;" style="border-collapse:collapse;font-size: 12;text-align: center;table-layout:fixed;">';
            $html = $html.'<tr>
                            <td style="width: 10%" valign="top">说明：</td>
                            <td valign="top">1、</td>
                            <td style="text-align: left;width:90%">实操考核鉴定中，组织考核单位须采集考核鉴定的影像资料（照片或录像等），并把整理好的照片或录像资料刻录成光盘报告省职业技能鉴定指导中心；</td>
                      </tr>';
            $html = $html.'<tr>
                            <td></td>
                            <td valign="top">2、</td>
                            <td style="text-align: left;width: 90%">考评员必须佩带有效考评员证上岗，每个职业（工种）不得少于三名考评员；</td></tr>';
            $html = $html.'<tr>
                            <td></td>
                            <td valign="top">3、</td>
                            <td style="text-align: left;width: 90%">实操考核工作结束后，每个职业（工作）的考评组应实名出具考评情况报告，并随实操成绩报告省中心审核。</td></tr>';
            $html = $html.'</table>';
            $html = $html.'<p style="page-break-after: always;">&nbsp;</p>
<p style="page-break-before: always;">&nbsp;</p>';
        }
        return $html;
    }

    /**
     * @param $clan
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取组织册信息
     */
    public function ClanOrganizeCount($plan)
    {
        //考点列表
        $list = $this->Enroll->getQualifications(array('exam_plan_id'=>$plan),'','site_id','site_id');
        //鉴定计划信息
        $planInfo = $this->plan->getExamPlanData(array('id'=>$plan));
        foreach ($list as $k=>$v)
        {
            //考点总人数
            $list[$k]['count'] = Db::table('exam_enroll')->field('count(distinct(user_login_id)) as cut')->where(array('exam_plan_id'=>$plan,'site_id'=>$v['site_id']))->find();
            //考点信息
            $list[$k]['organize'] = $this->Organize->getBaseFind(array('id'=>$v['site_id']));
            $list[$k]['lilun'] = 0;
            $list[$k]['zonghe'] = 0;
            $list[$k]['shicao'] = 0;
            $list[$k]['lilunCount'] = 0;
            $list[$k]['zongheCount'] = 0;
            $list[$k]['shicaoCount'] = 0;
            if($v['theory'])
            {
                $list[$k]['lilun'] = 1;
                $list[$k]['lilunCount'] = $this->Enroll->getCount(array('work_id'=>$v['work_id'],'work_level_subject_level'=>$v['work_level_subject_level'],'exam_plan_id'=>$plan,'organize_id'=>$v['organize_id']));
            }
            if ($v['comprehen'])
            {
                $list[$k]['zonghe'] = 1;
                $list[$k]['zongheCount'] = $this->Enroll->getCount(array('work_id'=>$v['work_id'],'work_level_subject_level'=>$v['work_level_subject_level'],'exam_plan_id'=>$plan,'organize_id'=>$v['organize_id']));
            }
            if ($v['operation'])
            {
                $list[$k]['shicao'] = 1;
                $list[$k]['shicaoCount'] = $this->Enroll->getCount(array('work_id'=>$v['work_id'],'work_level_subject_level'=>$v['work_level_subject_level'],'exam_plan_id'=>$plan,'organize_id'=>$v['organize_id']));
            }
        }
        $data['list'] = $list;
        $data['plan'] = $planInfo;
        return $data;
    }

    public function getRoom($plan,$organize)
    {
        return $this->ExamRoom->getRoomPlanOrganize(array('exam_plan_id'=>$plan,'ExamSiteId'=>$organize));
    }

    /**
     * @return array
     * work信息
     */
    public function getWork()
    {
        $works = $this->Work->getAlls();
        $data = array();
        foreach($works as $k=>$v)
        {
            $data[$v['id']] = $v['name'];
        }
        return $data;
    }

    /**
     * @param $plan
     * @param $organize
     * @param $type
     * @return array
     * 座位统计
     */
    public function getseatCount($plan,$organize,$type)
    {
        $works = $this->getWork();
        $data = array();
        foreach($this->Seat->getAllSeat($plan,$organize,$type) as $k=>$v)
        {
            $data[$v['work_level_subject_level']][$v['work_id']] = isset($data[$v['work_level_subject_level']][$v['work_id']]) ?  $data[$v['work_level_subject_level']][$v['work_id']]+1 : 1;
            $data[$v['work_level_subject_level']]['count'] = isset($data[$v['work_level_subject_level']][$v['work_id']]) ? $data[$v['work_level_subject_level']][$v['work_id']] +1 :1;
            $data[$v['work_level_subject_level']]['name'] = $works[$v['work_id']];
            $data[$v['work_level_subject_level']]['level'] = $v['work_level_subject_level']!= 0 ? $this->level_name[$v['work_level_subject_level']] : '';
        }
        return $data;
    }

    /**
     * @param $where
     * @param $type
     * @return array
     * 获取考场信息
     */
    public function roomInfo($where,$type)
    {
        $data['count'] = 0;
        $works = $this->getWork();
        $data = array();
        foreach($this->Seat->getRoomSeat($where['exam_plan_id'],$where['ExamSiteId'],$type,$where['examRoomNum']) as $k=>$v)
        {
            $data['name'] = isset($data['name']) ? $data['name'].$works[$v['work_id']].'/' : $works[$v['work_id']].'/';
            if(isset($data['level']))
            {
                if($v['work_level_subject_level']!=0)
                {
                    $data['level'] = $data['level'].$this->level_name[$v['work_level_subject_level']].'/';
                }else{
                    $data['level'] = $data['level'].'/';
                }
            }else{
                if($v['work_level_subject_level'] !=0)
                {
                    $data['level'] = $this->level_name[$v['work_level_subject_level']].'/';
                }
            }
            $start =  $this->Seat->getBaseInfo(array('ExamSiteId'=>$where['ExamSiteId'],'exam_plan_id'=>$where['exam_plan_id'],'ExamRoomNum'=>$where['examRoomNum']),$type,'id asc');
            $end = $this->Seat->getBaseInfo(array('ExamSiteId'=>$where['ExamSiteId'],'exam_plan_id'=>$where['exam_plan_id'],'ExamRoomNum'=>$where['examRoomNum']),$type,'id desc');
            $start_card = $this->ExamCard->getCardInfo(array('enroll_id'=>$start[$type],'exam_plan_id'=>$where['exam_plan_id']));
            $end_card = $this->ExamCard->getCardInfo(array('enroll_id'=>$end[$type],'exam_plan_id'=>$where['exam_plan_id']));
        }
        $data['level'] = isset($data['level']) ? substr($data['level'],0,-1) : '';
        $data['name'] = substr($data['name'],0,-1);
        $data['start'] = $start_card['card'];
        $data['end'] = $end_card['card'];
        return $data;
    }
}