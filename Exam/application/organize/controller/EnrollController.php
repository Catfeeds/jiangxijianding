<?php
namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\ExamPlan;
use app\common\service\WorkType;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\UserLogin;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamEnroll;
use app\common\service\ExamEnrollFile;
use app\common\service\OrganizeWork;
use app\common\service\Userinfo;
use app\common\service\Thesis;
use app\common\service\ExamOrder;
use app\common\service\Grade;
use think\Config;
use think\Request;
use Upload\Up;

class EnrollController extends Organizebase
{
    private $ExamPlan;
    private $worktype;
    private $Work;
    private $WorkDirection;
    private $UserLogin;
    private $ExamEnroll;
    private $ExamEnrollOne;
    private $OrganizeWork;
    private $SUserinfo;
    private $SExamEnrollFile;
    private $SThesis;
    private $SExamOrder;
    private $SGrade;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->ExamPlan = new ExamPlan();
        $this->worktype = new WorkType();
        $this->Work = new Work();
        $this->WorkDirection = new WorkDirection();
        $this->UserLogin = new UserLogin();
        $this->ExamEnroll = new ExamEnrollTable();
        $this->ExamEnrollOne = new ExamEnroll();
        $this->OrganizeWork = new OrganizeWork();
        $this->SUserinfo = new Userinfo();
        $this->SExamEnrollFile = new ExamEnrollFile();
        $this->SThesis    = new Thesis();
        $this->SExamOrder = new ExamOrder();
        $this->SGrade    = new Grade();
    }



    /**
     * 批量上传首页
     * @return \think\response\View
     * @user 朱颖 9.20
     */
    public function index()
    {
        //获取ID
        $arrAdmin = session("organizeuser");

        $map = [];
        $map['organize_id'] = $arrAdmin['id'];
        $arrWhere['organize_id'] = $arrAdmin['id'];
        $map['exam_enroll.status'] = [">=",config('ExamEnrollStatus.init')];
        $arrData['exam_type'] = '';
        $arrData = input();
        if(!isset($arrData['plan_id']))
        {
            $field = "ep.title,ep.enroll_starttime,ep.enroll_endtime,ep.audit_endtime,ep.exam_time,count(if(ee.status>=30 or ee.status = 20,1, null)) as ready,count(if(ee.status<20 or ee.status =25,1,null)) as nopass,count(ee.id) as num,ep.id,print_starttime,print_endtime";
            $where['ep.status'] = 1;
            $where['ee.delete_time'] = NULL;
            if(!isset($arrData['demo']))
            {
                $where['exam_time'] = array('gt',time());
                $where['enroll_starttime'] = array('lt',time());
                $demo = '';
                $examplan = $this->ExamPlan->nowPlan($where,$field);
                // echo $this->ExamPlan->getLastSql();die;
            }else{
                $demo = 'demo';
                $where['exam_time'] = array('lt',time());
                $examplan = $this->ExamPlan->nowPlan($where,$field);
            }
            // print_r($examplan);die;
            return view('examplan',['examplan'=>$examplan,'demo'=>$demo,'type'=>'/organize/enroll/index','title'=>'报名计划']);

        }
        $arrWhere['exam_plan_id'] = $arrData['plan_id'];
        $map['exam_plan.id'] = $arrData['plan_id'];
        $zong = $this->ExamEnrollOne->BaseSelectCount($arrWhere);
        if (!empty($arrData['name'])){
            $map['user_login.name'] = array('like','%'.$arrData['name'].'%');
        }else{
            $arrData['name'] = '';
        }
        if (!empty($arrData['status'])){
            $map['exam_enroll.status'] = $arrData['status'];
        }else{
            $arrData['status'] = '';
        }
        if (!empty($arrData['id_card'])){
            $map['user_login.id_card'] = $arrData['id_card'];
        }else{
            $arrData['id_card'] = '';
        }
        if (!empty($arrData['exam_type'])){
            $map['exam_type'] = $arrData['exam_type'];
        }else{
            $arrData['exam_type'] = '';
        }
        if(isset($arrData['state']) && $arrData['state']==1)
        {
            $map['exam_enroll.status'] = array(array('egt',config('ExamEnrollStatus.checkpass')),array('eq',config('ExamEnrollStatus.nopass')),'or');
        }
        if(isset($arrData['state']) && $arrData['state']==0)
        {
            $map['exam_enroll.status'] = array(array('lt',config('ExamEnrollStatus.nopass')),array('eq',config('ExamEnrollStatus.reject')),'or');
        }
        $arrEnroll = $this->ExamEnroll->ExamEnroll($map);
        $arrWhere['status'] = array(array('eq',config('ExamEnrollStatus.uploadfile')),array('eq',config('ExamEnrollStatus.reject')),'or');
        $count = $this->ExamEnrollOne->getPlanInfo($arrWhere);
        $count['zong'] = $zong;
        $field='sum(if(status='.config('ExamEnrollStatus.uploadfile').' or status='.config('ExamEnrollStatus.reject').',1,0)) as ready,sum(if(status='.config('ExamEnrollStatus.init').',1,0)) as wait,sum(if(status>='.config('ExamEnrollStatus.submit').' && status!='.config('ExamEnrollStatus.reject').',1,0)) as pass';
        $data = $this->ExamEnrollOne->BaseSelect(['exam_plan_id'=>$arrData['plan_id'],'organize_id'=>$arrAdmin['id']],[$field]);
        $screen = config('EnrollStatusText.screen');
        return view('index',['arrEnroll'=>$arrEnroll,'map'=>$arrData,'count'=>$count,'plan'=>$arrData['plan_id'],'audit'=>$screen,'type'=>'/organize/Enroll/index','title'=>'报名管理','data'=>$data]);
    }

    /**
     * 跳转到上传页面
     * @return \think\response\View
     * @user 朱颖 9.20
     */
    public function batchenroll(){
        $webData = input();
        return view("enroll",['plan'=>$webData['plan']]);
    }

    /**
     * 跳转到修改页面
     * @return \think\response\View
     * @user 朱颖 9.20
     */
    public function edit(){
        if (Request::instance()->isGet())
        {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            $maps['exam_enroll.id'] = $arrData['id'];
            $arrEnroll = $this->ExamEnroll->selectExamEnroll($maps);
            $arrWhere['id'] = $arrData['plan_id'];
            $arrExamPlan = $this->ExamPlan->BaseFind($arrWhere);

            $userLoginId= $this->UserLogin->getUserLoginCurrent($arrEnroll['user_login_id']);
            // $userInfo = $this->UserLogin->BaseFind($userMap);

            //获取计划的工种
            $examWhere['exam_plan.id'] = $arrData['plan_id'];
            $examWhere['exam_plan.status'] = 1;
            $examWhere['exam_work.type'] = config('ExamWorkType.exam');
            $examWhere['exam_work_level.type'] = config('ExamWorkType.exam');
            $examWhere['exam_plan.work_type'] = $arrExamPlan['work_type'];
            $newArray = $this->dataHangle($examWhere);
            //鉴定计划
            return view("update", ['arrExamPlan'=>$arrExamPlan,'arrExamEnroll' => $arrEnroll,'work'=>$newArray,'logininfo'=>$userLoginId]);
        }
    }

    /**
     * [dataHangle 鉴定计划和机构工种权限的交集]
     * @return [type] [description]
     */
    public function dataHangle($examWhere)
    {
        $newArray = [];
        $organizeWhere['organize.status'] = 1;   //状态
        $organizeWhere['organize.id'] = session('organizeuser')['id'];   //id
        $organizeWhere = array_merge($organizeWhere,getTypeBy(session('organizeuser')['type']));   
        $organizeWork = $this->OrganizeWork->getWork($organizeWhere);
        $organizeWork = collection($organizeWork)->toArray();
        $arrWorkType = $this->ExamPlan->getDataWithTable($examWhere);
        $arrWorkType = collection($arrWorkType)->toArray();
        foreach ($organizeWork as $key => $val) 
        {
            $res = search($arrWorkType,$val);
            if($res !== false )
            {
                $newArray[] = $arrWorkType[$res];
            }
        }
        $newArray = array_columns($newArray,['work_id','wname']);
        $arr = [];
        foreach ($newArray as $key => $val) 
        {
            if(in_array($val['work_id'],$arr))
            {
                unset($newArray[$key]);
            }
            else{
                $arr[] = $val['work_id'];
            }
        }
        return $newArray;

    }

    public function detail(){
        if (Request::instance()->isGet())
        {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            $map['e.id'] = $arrData['id'];
            //用户详情
            $arr = config('EnrollStatusText.work_level_subject_level');
            $arrPlanUser= $this->ExamEnroll->joinUserById($map);
            $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrPlanUser['exam_plan_id'],$field);
            return view("detail",['arrPlanUser'=>$arrPlanUser,'arrWork'=>$arrWork,'arr'=>$arr]);
        }
    }


    public function planDetail()
    {
        $arrData = input();
        $arr = config('EnrollStatusText.work_level_subject_level');
        $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
        $arrWork = $this->getList($arrData['id'],$field);
        return view("plandetail",['arrWork'=>$arrWork,'arr'=>$arr]);
    }

    /**
     * 获取联查数据
     * @param string $id
     * @param $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($id='',$field = "")
    {
        $map['exam_plan.id'] = $id;
//        $map['exam_work.type'] = 5;
//        $map['exam_work_level.type'] = 5;

        //查询鉴定计划所有的数据
        $arrExamPlan = $this->ExamPlan->getListData($map,$field);
        $arrExamPlan = collection($arrExamPlan)->toArray();

        //取出不同的东西
        $mapField = ['wid','workname','wdname','work_level'];
        $arr = array_columns($arrExamPlan,$mapField);
        //根据workid 去重
        $arrWork = array_unique_key($arrExamPlan,"wid");

        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k=>$v){
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'wdname'));
            $work_level = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
        }

        return $arrWork;
    }


    /**
     * 创建账号
     * @param $arrDataUser
     * @return array|false|int|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖
     * @throws \Exception
     */
    public function createUser($arrDataUser)
    {

        if (empty($arrDataUser)){
            return -1;
        }
        $where = '';
        $count = [];
        foreach ($arrDataUser as $v){
            $where .= "(id_card='".$v['id_card']."' and id_type=".$v['id_type'].") or";
            $count[] = $v['id_card'].'_'.$v['id_type'];
        }
        $where = rtrim($where,'or');
        $arrUserLogin = $this->UserLogin->where($where)->select();
        // $count = array_column($arrDataUser,'id_card');
        $count = array_unique($count);
        if (count($arrUserLogin) == count($count)){
            return collection($arrUserLogin)->toArray();
        }

        $newArray = [];
        $old = [];
        
        if (!empty($arrUserLogin)){

            foreach ($arrUserLogin as $k=>$v){
                foreach ($arrDataUser as $kk=>$vv){
                    if ($v['id_card'].'_'.$v['id_type'] == $vv['id_card'].'_'.$vv['id_type']){
                        $arrDataUser[$kk]['id'] = $v['id'];
                    }
                }
            }
            foreach ($arrDataUser as $k=>$v){
                if (isset($v['id'])){
                    unset($arrDataUser[$k]);
                    $old[$k]=$v;
                }
            }
            $newArray = $this->verificationData($arrDataUser);
        }else{
            $newArray = $this->verificationData($arrDataUser);
        }
        if(!is_array($newArray))
        {
            return $newArray;
        }
        $arr = [];
        $unique = $newArray;
        foreach ($newArray as $k=>$v)
        {
            if(in_array($v['id_card'].'_'.$v['id_type'],$arr))
            {
                unset($newArray[$k]);
            }else{
                $arr[] = $v['id_card'].'_'.$v['id_type'];
                $userinfo[$k]['native_place'] = $v['native_place'];
                $userinfo[$k]['username'] = $v['name'];
                $userinfo[$k]['id_card'] = $v['id_card'];
                $userinfo[$k]['id_type'] = $v['id_type'];
                unset($newArray[$k]['native_place']);
            }  
        }

        $mobile = array_column($newArray,'mobile');
        $data = $this->UserLogin->BaseSelect(['mobile'=>array('in',$mobile)],['mobile']);
        if(!empty($data))
        {
            $data = collection($data)->toArray();

            $mobile = array_column($data,'mobile');
            $data = implode(',',$mobile);
            return '手机号'.$data.'已注册';
        }
        //数据添加操作
        /** @var TYPE_NAME $newArray */
        $id = $this->UserLogin->SaveAll($newArray);
        $id = collection($id)->toArray();
        foreach ($userinfo as $k=>$v)
        {
            foreach ($id as $key=>$val)
            {
                if ($v['id_card'].'_'.$v['id_type'] == $val['id_card'].'_'.$val['id_type'])
                {
                    $userinfo[$k]['user_login_id'] = $val['id'];
                    unset($userinfo[$k]['id_card']);
                    unset($userinfo[$k]['id_type']);
                }
            }
        }
        $userId = $this->SUserinfo->saveAll($userinfo);
        foreach ($unique as $k=>$v)
        {
            foreach ($id as $key=>$val)
            {
                if ($v['id_card'].'_'.$v['id_type'] == $val['id_card'].'_'.$val['id_type'])
                {
                    $unique[$k]['id'] = $val['id'];
                }
            }
        }
        return $return = array_merge($unique,$old);
    }

    /**
     *  批量报名
     * @return string|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 9.20
     */
    public function enrollbatch()
    {
//        $up = new Up();
        //获取ID
        $arrAdmin = session("organizeuser");
        $organizeId = $arrAdmin['id'];
        $type = $arrAdmin['type'];

//        //判断文件夹是否存在不存在则创建
//        if (! file_exists ( ROOT_PATH . 'public' . DS .'uploads'.DS .'excel'. DS ."enroll". DS .$organizeId )) {
//            mkdir ( ROOT_PATH . 'public' . DS .'uploads'.DS . 'excel'. DS ."enroll". DS .$organizeId, 0777, true );
//        }
        //处理Excel文件请求
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('file');
        $plan_id = request()->post('plan_id');
        if(!$plan_id)
        {
            return layuiMsg(-1,'鉴定计划不能为空');
        }
        if (is_null($file)) {
            return layuiMsg(-1,'上传文件不能为空');
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS .'uploads'. DS .'excel'. DS ."enroll". DS .$organizeId);

        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS ."enroll". DS . $organizeId . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            if (empty($excel_array)){
                return layuiMsg(-1,'上传文件不能为空');
            }
            //删除空数据
            foreach ($excel_array as $k => $v) {
                foreach ($v as $key=>$val){
                    if (empty($val) or preg_match("/\s/", $val)) {
                        unset($excel_array[$k][$key]);
                    }
                }
            }
            $userdata = []; //用户表数据
            $num = 2;
            $yan   = [];    //验证用户同一计划的工种级别最多三个
            $yan['phone']=[];
            $yan['card']=[];
            $yan['yan']=[];
            $yan['card_phone'] = [];
            $cardtype = ["身份证","护照","军官证","港澳台证","其他"];
            foreach ($excel_array as $key => $val) 
            {
                $hang = $key+$num;
                //<-- 验证用户数据是否为空 以及所报工种级别是否重复  以及次数上限和拼接用户数据  start
                if(!isset($val[0]) || !isset($val[1]) || !isset($val[2]) || !isset($val[3]) || !isset($val[4]) || !isset($val[5]) || !isset($val[6]) || !isset($val[8]) || !isset($val[9]) || !isset($val[10]))
                {
                    return layuiMsg(-1,'第'. $hang .'行信息不全');
                }
                if($val[8] < 0 || $val[8]>5)
                {
                    return layuiMsg(-1,'第'.$hang.'行级别不存在');
                }
                $yan['card_phone'][] = $val[1].'_'.$val[2].'_'.$val[5];
                for ($i=0; $i < count($yan['card_phone']); $i++) { 
                    $arr = explode('_',$yan['card_phone'][$i]);
                    if(($val[1].'_'.$val[2] == $arr[0].'_'.$arr[1]  &&  $val[5] != $arr[2]) or ($val[1].'_'.$val[2] != $arr[0].'_'.$arr[1]  &&  $val[5] == $arr[2]))
                    {
                        return layuiMsg(-2,'有证件号'.$val[1].'与手机号不匹配');
                    }
                }
                $phonecount = array_count_values($yan['phone']);
                $cardcount = array_count_values($yan['card']);
                $directionname = isset($val[7])?trim($val[7]):'';
                if(in_array($val[1].'_'.$val[2].'_'.trim($val[6]).'_'.$directionname.'_'.trim($val[8]),$yan['yan'])){
                    return layuiMsg(-2,'证件号'.$val[1].'所报工种级别有重复');
                }
                $yan['yan'][] = $val[1].'_'.$val[2].'_'.trim($val[6]).'_'.$directionname.'_'.trim($val[8]); 
                if(isset($cardcount[$v[1].'_'.$val[2]]) && $cardcount[$v[1].'_'.$val[2]]>3)
                {
                    return layuiMsg(-1,'证件号'.$val[1].'重复3次以上,每个身份最多报3个不同工种级别');
                }
                $yan['card'][] = (string) $val[1].'_'.$val[2];
                if(isset($phonecount[$v[5]]) && $phonecount[$v[5]]>3)
                {
                    return layuiMsg(-1,'手机号'.$val[5].'重复3次以上,每个身份最多报3个不同工种级别');
                }
                $yan['phone'][] = (string) $val[5];
                if(!in_array($v[2],$cardtype))
                {
                    return layuiMsg(-1,'第'.$hang.'行证件类型错误');
                }
                $userdata[$key]['id_type'] = array_keys($cardtype,trim($val[2]))[0]+1;
                $userdata[$key]['id_card'] = $val[1];
                $userdata[$key]['native_place'] = $val[3];
                $userdata[$key]['password']  = $val[4];
                $userdata[$key]['mobile']  = $val[5];
                $userdata[$key]['name'] = $val[0];
                $userdata[$key]['reg_type'] = $type;
                $userdata[$key]['create_time'] = time();
                $userdata[$key]['update_time'] = time();

                //--> 验证用户 end
                
            }
            $arr = $this->createUser($userdata);
            if($arr == -1)
            {
                return layuiMsg(-1,'导入失败：表格内容为空');
            }
            if(!is_array($arr))
            {
                return layuiMsg(-1,$arr);
            }
            foreach ($arr as $key => $item){
                foreach ($excel_array as $k=>$v){
                    if ($v[1].'_'.$v[2] == $item['id_card'].'_'.$cardtype[$item['id_type']-1]){
                        $excel_array[$k]['user_login_id'] = $item['id'];
                    }
                }
            }
            $arrCheck = [];
            $organizeWhere = [];
            $organizeWhere['organize.status'] = 1;   //状态
            $organizeWhere['organize.id'] = $organizeId;   //id
            $organizeWhere = array_merge($organizeWhere,getTypeBy($type));
            $organizeWork = $this->OrganizeWork->getWork($organizeWhere);
            $organizeWork = collection($organizeWork)->toArray();

            if(empty($organizeWork))
            {
                return layuiMsg(-1,'暂无可报名工种');
            }

            foreach ($arr as $key => $item){
                foreach ($excel_array as $k=>$v){
                    if ($v[1].'_'.$v[2] == $item['id_card'].'_'.$item['id_type']){
                        $excel_array[$k]['user_login_id'] = $item['id'];
                    }
                }
            }

            $i = 0;
            $mapExam = '';
            foreach ($excel_array as $k=>$v){
                    $enrolldata[$k]['user_login_id'] = $v['user_login_id'];
                    $enrolldata[$k]['status'] = Config::get("enrollstatus.init");
                    $i++;
                    $enrolldata[$k]['exam_plan_id'] = $plan_id;

                    switch (trim($v[9])){
                        case "新考" :$v[9] = 1; break;
                        case "补考" :$v[9] = 2; break;
                        default : $v[9] = 3; break;
                    }
                    //审核方式
                    switch (trim($v[10])){
                        case "线上" :$v[10] = 1; break;
                        case "线下" :$v[10] = 0; break;
                        default : $v[10] = 2; break;
                    }

                    //拼接入库的数据
                    $enrolldata[$k]['organize_id'] = trim($organizeId);
                    $enrolldata[$k]['exam_type'] = trim($v[9]);//考试类型
                    $enrolldata[$k]['audit_way'] = trim($v[10]);//审核类型
                    $enrolldata[$k]['bar_code'] = get_rnd_id();//条形码
                    $enrolldata[$k]['source'] = $type;//来源
                    $enrolldata[$k]['create_time'] = time(); //创建时间
                    $enrolldata[$k]['update_time'] = time(); //修改时间
                    if ($enrolldata[$k]['exam_type'] > 2){
                        return layuiMsg(-1,'考试类型不存在');
                    }
                    if ($enrolldata[$k]['exam_type'] > 1){
                        return layuiMsg(-1,'审核方式不存在');
                    }
                   $enrolldata[$k]['audit_way'] = $v[10];
                    $enrolldata[$k]['exam_type'] = $v[9];
                    //需要验证的数据
                    $arrCheck[$k]['wname'] = trim($v[6]);
                    $arrCheck[$k]['wdname'] = !empty($v[7])?trim($v[7]):0;
                    $arrCheck[$k]['wllevel'] = trim($v[8]);
//                    $arrCheck[$k]['wtname']  = ;
                }
            

            $examWhere['exam_plan.id'] = $plan_id;
            $examWhere['exam_plan.status'] = 1;
            $examWhere['exam_work.type'] = config('ExamWorkType.exam');
            $examWhere['exam_work_level.type'] = config('ExamWorkType.exam');

            //鉴定计划
            $arrWorkType = $this->ExamPlan->getDataWithTable($examWhere);
//            print_r($arrWorkType);die;
            $arrWorkType = collection($arrWorkType)->toArray();
            // print_r($arrWorkType);die;
            foreach ($arrWorkType as $k=>$v)
            {
                if (empty($v['wdid']))
                {
                    $arrWorkType[$k]['wdid'] = 0;
                }
                if (empty($v['wdname']))
                {
                    $arrWorkType[$k]['wdname'] = 0;
                }
            }
            foreach ($organizeWork as $k=>$v)
            {
                if (empty($v['wdname']))
                {
                    $organizeWork[$k]['wdname'] = 0;
                }
            }

            $i = 0;

            //需要验证的数据  验证鉴定计划是否可以报名
            $now = time();
            foreach ($arrCheck as $k=>$v){
                if (empty($v['wdname']))
                {
                    $v['wdname'] = 0;
                }
                $i++;
                $arrChecked[$k] = search($arrWorkType,$v);
                // 验证失败
                if ($arrChecked[$k] === false){
                    return layuiMsg(-1,'第'.$i.'行报名信息有误,请核对鉴定计划的工种级别方向');
                }else{
//                        echo 1;die;
                    $enrolldata[$k]['work_id'] = $arrWorkType[$arrChecked[$k]]['work_id'];
                    $enrolldata[$k]['work_direction_id'] = $arrWorkType[$arrChecked[$k]]['wdid'];
                    $enrolldata[$k]['work_level_subject_level'] = $arrWorkType[$arrChecked[$k]]['wllevel'];
                    $enrolltime[$k]['enroll_starttime'] = $arrWorkType[$arrChecked[$k]]['enroll_starttime'];
                    $enrolltime[$k]['enroll_endtime'] = $arrWorkType[$arrChecked[$k]]['enroll_endtime'];
                    $enrolldata[$k]['audit_site'] = "";
                    $enrolldata[$k]['exam_site'] = "";
                }
                $arrCheckedOrganize[$k] = search($organizeWork,$v);
                //验证机构失败
                if ($arrCheckedOrganize[$k] === false){
                    return layuiMsg(-1,'第'.$i.'行,您不能为此工种级别报名,请核对你应有的工种级别方向');
                    // return $this->success("{$i}行,您不能为此工种报名", '/organize/enroll/index');
                } 
            }
            foreach ($enrolldata as $key => $val) 
            {   
                $i = $key+2;
                if($val['exam_type']==1 && $val['work_level_subject_level']>=3)
                {
                    $enrolldata[$key]['theory'] = (bool) 1;
                    $enrolldata[$key]['operation'] = (bool) 1;
                }else if($val['exam_type']==1 && $val['work_level_subject_level']<3){
                    $enrolldata[$key]['theory'] = (bool) 1;
                    $enrolldata[$key]['operation'] = (bool) 1;
                    $enrolldata[$key]['comprehen'] = (bool) 1;
                }else{
                    $grade = $this->SGrade->BaseFind(['user_login_id'=>$val['user_login_id'],'work_id'=>$val['work_id'],'work_direction_id'=>$val['work_direction_id'],'level'=>$val['work_level_subject_level']]);
                    if(!empty($grade))
                    {
                        $enrolldata[$key]['theory'] = $grade['theory_score_result']==1?(bool) 0:(bool)1;
                        $enrolldata[$key]['operation'] = $grade['watch_score_result']==1?(bool) 0:(bool)1;
                        $enrolldata[$key]['comprehen'] = $grade['synthesize_score_result']==1?(bool) 0:(bool)1;
                    }else{
                        return layuiMsg(-1,'第'.$i.'行考试类型有误,不满足补考条件');
                    }
                    

                }
            }

            //删除空数据
            foreach ($enrolldata as $k => $v) {
                if (preg_match("/\s/", $v['user_login_id'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['exam_plan_id'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['work_id'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['work_direction_id'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['work_level_subject_level'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['source'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['audit_site'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['exam_site'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['organize_id'])) {
                    unset($enrolldata[$k]);
                }
                if (preg_match("/\s/", $v['exam_type'])) {
                    unset($enrolldata[$k]);
                }
            }

            //验证导入的数据真实性
           $newenrolldata = $this->verificationEnrolldata($enrolldata);
           if ($newenrolldata !== true) {
               return layuiMsg($newenrolldata['code'],$newenrolldata['msg']);
           }
            $card = '';
            $data = $this->ExamEnrollOne->BaseSelect(['exam_plan_id'=>$plan_id],['count(user_login_id) as count,user_login_id,exam_plan_id'],'','','user_login_id');
            // print_r($data);die;
            foreach ($excel_array as $k => $v) {
                foreach ($data as $key => $val) {
                    if($val['user_login_id'] == $v['user_login_id'] && $data[$key]['count']+1 <3)
                    {
                        $data[$key]['count'] = $val['count'] + 1;
                    }else if($val['user_login_id'] == $v['user_login_id']){
                        // echo $data[$key]['count'];
                        $card .= $v[1].',';
                    }
                }
            }
            if($card != '')
            {
                return layuiMsg(-2,'证件号'.$card.'在此计划报名超过上限(3次)');
            }
            //数据添加操作
            $success = $this->ExamEnrollOne->BaseSaveAll($enrolldata);
            if ($success){
                //响应
                return layuiMsg(1,'导入成功');
            }else{
                return layuiMsg(-1,'导入失败');
            }

        }
    }





    /**
     * 验证数据
     * @param $paramArray
     * @user 朱颖
     */
    public function verificationData($paramArray)
    {
        //删除重复数组 删除个数记录下返回给用户
        $ii = 1;
        foreach ($paramArray as $key => $array) {
            $ii++;

           // print_r($array);die;
            switch (trim($array["id_type"])){
                    case "1" :
                        //验证身份证合法性
                        $preg_name='/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/';
                        if(!preg_match($preg_name,$array['id_card'])){
                            return '导入失败:第'. $ii .'行，身份证不合法';
                        }
                        break;
                    case "2" :
                        //验证护照合法性
                        $preg_name='/^[a-zA-Z0-9]{3,21}$/';
                        if(!preg_match($preg_name,$array['id_card'])){
                            return '导入失败:第'. $ii .'行，护照不合法';
                        }
                        break;
                    case "3" :
                        //验证军官证合法性
                        $preg_name='/^[a-zA-Z0-9]{7,21}$/';
                        if(!preg_match($preg_name,$array['id_card'])){
                            return '导入失败:第'. $ii .'行，军官证不合法';
                        }
                        break;
                    case "4" :
                        //验证港澳台证合法性
                        $preg_name='/^[a-zA-Z0-9]{5,21}$/';
                        if(!preg_match($preg_name,$array['id_card'])){
                            return '导入失败:第'. $ii .'行，港澳台证不合法';
                        }
                        break;
                    default :
                        return '导入失败:第'. $ii .'行，证件不合法';
                        break;
                }


        }
        //验证数据格式
        //数据验证
        $validata = Validate('User');
        $i = 1;
        foreach ($paramArray as $key => $item) {
            $i++;
            //场景应用
            if (!$validata->scene('userlogin')->check($item)) {
                return '导入失败: ' . $i . '行' . $validata->getError();
            }
            $paramArray[$key]['password'] = md5($item['password']);
        }
        return $paramArray;
    }

    /**
     * 验证报名表数据
     * @param $paramArray
     * @user 朱颖
     */
    public function verificationEnrolldata($paramArray)
    {
        //删除重复数组 删除个数记录下返回给用户
//        print_r($paramArray);die;
        $ii = 1;
        $user_login_id = '';
        $exam_plan_id = '';
        $work_id = '';
        $work_direction_id = '';
        $work_level_id = '';
        foreach ($paramArray as $key => $array) {

            $ii++;

            $map['user_login_id'] = $array['user_login_id'];
            $map['exam_plan_id'] = $array['exam_plan_id'];
            $map['work_id'] = $array['work_id'];
            $map['work_direction_id'] = $array['work_direction_id'];
            $map['work_level_subject_level'] = $array['work_level_subject_level'];

            $onlyusername = $this->ExamEnroll->checkUser($map);
            if (!empty($onlyusername)){
                return layuiMsg(-1,'导入失败: 第'.$ii.'行，证件号为 '.$onlyusername[0]["id_card"].' 已报名此工种级别');
            }

        }
        return true;
        
    }

    /**
     * 报名材料上传【身份证】
     * @return [type] [description]
     */
    public function enrollData()
    {
        $plan = input();
        return view('enrolldata',['plan_id'=>$plan['plan_id']]);
    }

    /**
     * [enrollData 论文上传]
     * @return [type] [description]
     */
    public function enrollthesis()
    {
        $plan = input();
        return view('enrollthesis',['plan_id'=>$plan['plan_id']]);
    }

    /**
     * [缴费列表]
     * @return [type] [description]
     */
    public function enrollpay()
    {
        $plan_id = input('plan_id');
        if(!$plan_id)
        {
            $field = "ep.title,ep.pay_endtime,ep.audit_endtime,ep.exam_time,count(if(ee.status=50,1, null)) as ready,count(if(ee.status=49,1,null)) as huan,count(if(ee.status>=30 and ee.status!=50 and ee.status!=49 ,1, null)) as wait,count(ee.id) as num,ep.id";
            $where['ep.status'] = 1;
            $where['pay_endtime'] = array('gt',time());
            $where['enroll_starttime'] = array('lt',time());
            $examplan = $this->ExamPlan->nowPlan($where,$field);
            return view('payexamplan',['examplan'=>$examplan,'type'=>'/organize/enroll/enrollpay','title'=>'缴费计划']);
        }
        $arrAdmin = session("organizeuser");
        $map['ee.exam_plan_id'] = $arrWhere['exam_enroll.exam_plan_id'] = $plan_id;  
        $map['ee.organize_id'] = $arrWhere['exam_enroll.organize_id'] = $arrAdmin['id'];
        $arrWhere['exam_enroll.status'] = array('in',[config('ExamEnrollStatus.checkpass'),config('ExamEnrollStatus.payost'),config('ExamEnrollStatus.paydelayed'),config('ExamEnrollStatus.huanfalse'),config('ExamEnrollStatus.huan')]);
        $map['ee.status'] = array(array('eq',config('ExamEnrollStatus.checkpass')),array('eq',config('ExamEnrollStatus.huanfalse')),'or');
        if($arrAdmin['is_institution']== 1)
        {
            $column = ',sum(wls.institution_turnin_money) as price';
            $fields = 'institution_turnin_money';
        }else{
            $column = ',sum(wls.turnin_money) as price';
            $fields = 'turnin_money';
        }
        $payInfo = $this->ExamEnroll->selectEnrollpay($arrWhere,$column);
//         print_r($payInfo);die;
        $priceInfo = $this->ExamEnroll->selectPrice($map,$fields);
        return view('enrollpay',['payInfo'=>$payInfo['examJoinData'],'priceInfo'=>$priceInfo,'plan_id'=>$plan_id,'title'=>'缴费管理']);
    }


    /**
     * [缴费原因及凭证]
     * @return [type] [description]
     */
    public function applyremark()
    {
        $plan_id = input('plan_id');
        $type    = input('type');
        $arrWhere['ee.exam_plan_id'] = $plan_id;
        $arrAdmin = session("organizeuser");
        $arrWhere['ee.organize_id'] = $arrAdmin['id'];
        $arrWhere['ee.status']      = array(array('eq',config('ExamEnrollStatus.checkpass')),array('eq',config('ExamEnrollStatus.huanfalse')),'or');
        // $arrWhere['ee.status']      = array('in',[52,20]);
        if($arrAdmin['is_institution']== 1)
        {
            $fields = 'institution_turnin_money';
        }else{
            $fields = 'turnin_money';
        }
        $payInfo = $this->ExamEnroll->selectPrice($arrWhere,$fields);
        if($type=='apply')
        {
            return view('applyremark',['count'=>$payInfo,'plan_id'=>$plan_id]);
        }
        else
        {
            return view('offlinepay',['count'=>$payInfo,'plan_id'=>$plan_id]);
        } 
    }

     /**
     * [修改 缴费原因及凭证]
     * @return [type] [description]
     */
    public function orderremark()
    {
        $plan_id = input('plan_id');
        $order_id = input('order_id');
        $type    = input('type');
        $arrAdmin = session("organizeuser");
        $payInfo = $this->SExamOrder->BaseFind(['id'=>$order_id],['total_money']);
        if($type=='apply')
        {
            $field = "exam_plan.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($plan_id,$field);
            return view('examorder/applyremark',['count'=>$payInfo,'plan_id'=>$plan_id,'arrWork'=>$arrWork,'order_id'=>$order_id]);
        }
        else
        {
            return view('examorder/offlinepay',['count'=>$payInfo,'plan_id'=>$plan_id,'order_id'=>$order_id]);
        } 
    }

    public function printEnrollTable()
    {
        $plan_id = input('plan_id');
        $arrAdmin = session("organizeuser.id");



    }

    /**
     * [printcard 批量打印准考证]
     * @return [type] [description]
     */
    public function printcard()
    {
        $plan_id = input('plan_id');
        $arrAdmin = session("organizeuser.id");
        $url = getFileName($plan_id,$arrAdmin,'','card');
        downFile($url);exit;
    }


    /**
     * [enroll 报名压缩材料]
     * @return [type] [description]
     * @author [张乐乐] <[email address]>
     * @param [array] $where [必传文件缺少的人]
     * @param array $insertData [需要添加或修改的数据]
     */
    public function enrolldataFile()
    {
        $file = request()->file('file');
        $plan = request()->post('plan_id');
        $where = [];
        $arr = [];
        $photo = [];
        $insertData = [];
        $userinfo   = [];
        if(!$plan)
        {
            return layuiMsg(-1,'鉴定计划不能为空');
        }
        $arrAdmin = session("organizeuser");
        $organizeId = $arrAdmin['id'];

        if(empty($file))
        {
            return layuiMsg(-1,'上传文件为空');
        }
        
        $path = ROOT_PATH . 'public' . DS .'uploads'.DS .'enroll_datum'. DS ."zip_".$organizeId;
        if (! file_exists ($path)) {
            mkdir ($path, 0755, true );
        }
        $uploadInfo = $file->validate(['ext'=>'zip'])->move($path);
        if(!$uploadInfo)
        {
            return layuiMsg(-1,'文件上传失败');
        }
        $imgPath = ROOT_PATH . 'public' . DS .'uploads'.DS .'enroll_datum'.DS.'zip_'.$organizeId;
        $path2 = $uploadInfo->getSaveName();
        $uppath = $path.DS.$path2;
        $this->unzip_file($uppath,$imgPath);
        $handle = opendir($imgPath);
        while ($filename = readdir($handle)) {
            if($filename == 'card' || $filename == 'education' || $filename == 'level' || $filename == 'photo')
            {
                if(is_dir($imgPath.DS.$filename))
                {    
                    $handle2 = opendir($imgPath.DS.$filename);
                    while($filename2 =  readdir($handle2))
                    {
                        if($filename2 != '.' && $filename2!= '..')
                        {
                            if(!in_array(explode('.',$filename2)[1],['jpeg','jpg','png'])){
                                return layuiMsg(-2,$filename.'中'.$filename2.'图片格式错误');
                            }
                            $res = $this->enrollmanage(trim($filename),$filename2,$imgPath,$plan);
                            if($res)
                            {
                                if(trim($filename)=='photo')
                                {
                                    $userinfo[$res['login_id']]['user_login_id'] = $res['login_id'];
                                    $userinfo[$res['login_id']]['avatar'] = $res['arrFileData']['path'];
                                }

                                if(trim($filename)=='card' || trim($filename)=='photo'){

                                    if ($res['type']=1) {
                                        if(isset($where[$res['id']])){
                                            $count = array_count_values($where[$res['id']]);
                                            if($count[$res['card']]=3){
                                                unset($where[$res['id']]);
                                                $arr[] = $res['id'];
                                            }
                                        }
                                        else{
                                            $where[$res['id']][] = $res['card'];
                                        }
                                        
                                    }else if($res['type']=0 && trim($filename)=='photo'){
                                            $arr[] = $res['id'];
                                    }
                                    
                                }
                                $insertData[] = $res['arrFileData'];
                            }
                        }
                        
                    }
                    // rmdir($imgPath.DS.$filename);
                }
            }
        }

        if(empty($insertData)){
            return layuiMsg(-1,'暂无需提交资料的考生');
        }
        $where = array_column($where,'card');
        if(empty($where)){
            $str = '上传完成';
        }else{
            $str = implode(',',$where).'身份证信息证件照材料有误';
        }
        $res = $this->SExamEnrollFile->uploadFiles($insertData,['status'=>config('ExamEnrollStatus.uploadfile')],['id'=>array('in',$arr),'status'=>array(array('eq',config('ExamEnrollStatus.init')),array('eq',config('ExamEnrollStatus.reject')),'or')],$userinfo);
        if($res===true)
        {
            return layuiMsg(1,$str);
            // return $this->success('上传完成','/organize/enroll/index?plan_id='.$plan);
        }else{
            return layuiMsg(-1,$res);
        }
        

    }

    /**
     * [根据报名材料 对报名信息进行处理]
     * @return [type] [description]
     * @author [张乐乐] <[email address]>
     */
    public function enrollmanage($filename,$filename2,$imgPath,$plan)
    {
        $time = date('ymdHi',time());
        $arrAdmin = session("organizeuser");
        $IDcard = explode('.',$filename2)[0];
        $arrWhere['id_card'] = explode('_',$IDcard)[0];
        $arrFileData = [];
        $result = $this->UserLogin->BaseFind($arrWhere);
        if(empty($result))
        {
            return false;
        }
        $arrEnroll['user_login_id'] = $result['id']; 
        $arrEnroll['organize_id']   = $arrAdmin['id'];
        $arrEnroll['exam_plan_id']  = $plan;
        $upload_path = ROOT_PATH.'public/uploads/enroll_datum/zip_'.$arrAdmin['id'].'/'.$time.'/'.$filename;
        if (! file_exists ($upload_path)) {
            mkdir ($upload_path, 0755, true );
        }
        if($filename=='card' && explode('_',$IDcard)[1] == 'zheng'){
            $arrFileData['type'] = 1;
        }else if($filename=='card' && explode('_',$IDcard)[1] == 'fan'){
            $arrFileData['type'] = 4;
        }
        else if($filename=='education')
        {
            $arrFileData['type'] = 2;
        }
        else if($filename=='level')
        {
            $arrFileData['type'] = 3;
        }
        else if($filename=='photo')
        {
            $arrFileData['type'] = 6;
        }
        $newPath = $upload_path.DS.$filename.'_'.$filename2;
        $move = rename($imgPath.DS.$filename.DS.$filename2,$newPath);
        if($move)
        {
            $arrFileData['path'] = '/uploads/enroll_datum/zip_'.$arrAdmin['id'].'/'.$time.'/'.$filename.'/'.$filename.'_'.$filename2;
            $arrFileData['create_time'] = time();
            $arrEnroll['status'] = array(array('elt',config('ExamEnrollStatus.uploadfile')),array('eq',config('ExamEnrollStatus.reject')),'or'); 
            $enrollInfo = $this->ExamEnrollOne->BaseFind($arrEnroll);
            if(!empty($enrollInfo))
            {
                $where['type'] = $arrFileData['type'];
                $where['exam_enroll_id'] = $enrollInfo['id'];
                $arrFileData['exam_enroll_id'] = $enrollInfo['id'];
                $result = $this->SExamEnrollFile->BaseFind($where);

                if($result){
                    $arrFileData['id'] = $result['id'];
                }
                return ['arrFileData'=>$arrFileData,'id'=>$enrollInfo['id'],'card'=>$arrWhere['id_card'],'type'=>$enrollInfo['audit_way'],'login_id'=>$arrEnroll['user_login_id']];
               
            }else{
            
                return false;
            }
        }

    }

   

    /**解压文件
     *@file  需要解压的文件的路径
     *@destination  解压之后存放的路径   富文本编辑器：文字+图片+格式+排版
     */
    function unzip_file($file, $destination){
        // 实例化对象
        $zip = new \ZipArchive() ;
        //打开zip文档，如果打开失败返回提示信息
        if ($zip->open($file) !== TRUE) {
            die ("不能打开文件!");
        }
        //将压缩文件解压到指定的目录下
        $zip->extractTo($destination);
        //关闭zip文档
        $zip->close();
        // echo '提取成功';
    }

    public function thesisZip()
    {
        $file = request()->file('file');
        $plan = request()->post('plan_id');
        $arr = [];
        $insertData = [];
        $arrAdmin = session("organizeuser");
        $organizeId = $arrAdmin['id'];
        if(!$plan)
        {
            return layuiMsg(-1,'鉴定计划不能为空');
        }
        if(empty($file))
        {
            return layuiMsg(-1,'上传文件为空');
        }
        
        $path = ROOT_PATH . 'public' . DS .'uploads'.DS .'enroll_datum'. DS ."zip_".$organizeId;
        if (! file_exists ($path)) {
            mkdir ($path, 0644, true );
        }
        $uploadInfo = $file->move($path);
        if(!$uploadInfo)
        {
            return layuiMsg(-1,'文件上传失败');
        }
        $imgPath = ROOT_PATH . 'public' . DS .'uploads'.DS .'enroll_datum'.DS.'zip_'.$organizeId;
        $path2 = $uploadInfo->getSaveName();
        $uppath = $path.DS.$path2;
        $this->unzip_file($uppath,$imgPath);
        $handle = opendir($imgPath);
        while ($filename = readdir($handle)) {

            if($filename == 'thesis')
            {
                if(is_dir($imgPath.DS.$filename))
                {
                    
                    $handle2 = opendir($imgPath.DS.$filename);
                    while($filename2 =  readdir($handle2))
                    {
                        if($filename2 != '.' && $filename2!= '..')
                        {
                            $res = $this->thesisData($filename,$filename2,$imgPath,$plan);
                            if($res)
                            {
                                $arr[] = $res['id'];
                                $insertData[] = $res['arrFileData'];
                            }
                            
                        }
                        
                    }
                    rmdir($imgPath.DS.$filename);
                }
                
            }
        }
        if(empty($insertData)){
            return layuiMsg(1,'暂无需提交论文的考生');
        }
        $this->SThesis->BaseSaveAll($insertData);
        $res = $this->ExamEnrollOne->BaseUpdate(['thesis_state'=>2],['id'=>array('in',$arr)]);
        if($res)
        {
            return layuiMsg(1,'上传完成');
        }else{
            return layuiMsg(-1,'上传失败');
        }
    }

    /**
     * [thesisData 论文上传]
     * @return [type] [description]
     */
    public function thesisData($filename,$filename2,$imgPath,$plan)
    {
        $time = date('ymdHi',time());
        $arrAdmin = session("organizeuser");
        $IDcard = explode('.',$filename2)[0];
        $arrWhere['id_card'] = explode('_',$IDcard)[0];
        $arrFileData = [];
        
        $result = $this->UserLogin->BaseFind($arrWhere);
        $arrEnroll['user_login_id'] = $result['id']; 
        $arrEnroll['organize_id']   = $arrAdmin['id'];
        $arrEnroll['exam_plan_id']  = $plan;
        $upload_path = ROOT_PATH.'public/uploads/enroll_datum/zip_'.$arrAdmin['id'].'/'.$time.'/'.$filename;
        if (! file_exists ($upload_path)) {
            mkdir ($upload_path, 0644, true );
        }
        $newPath = $upload_path.DS.$filename.'_'.$filename2;
        $move = rename($imgPath.DS.$filename.DS.$filename2,$newPath);
        if($move)
        {
            $arrFileData['path'] = '/uploads/enroll_datum/zip_'.$arrAdmin['id'].'/'.$time.'/'.$filename.'/'.$filename.'_'.$filename2;
            
            $arrEnroll['status'] = array(array('lt',config('EnrollStatus.submit')),array('eq',config('ExamEnrollStatus.reject')),'or'); 
            $arrEnroll['work_level_subject_level'] = array('in',[1,2]);
            $enrollInfo = $this->ExamEnrollOne->BaseFind($arrEnroll);
            if(!empty($enrollInfo))
            {
                $where['exam_enroll_id'] = $enrollInfo['id'];
                $arrFileData['exam_enroll_id'] = $enrollInfo['id'];
                $result = $this->SThesis->BaseFind($where);
                if($result)
                {
                    $arrFileData['id'] = $result['id'];
                }
                return ['arrFileData'=>$arrFileData,'id'=>$enrollInfo['id']];  
               
            }else{
            
                return;
            }
        }
    }

}

