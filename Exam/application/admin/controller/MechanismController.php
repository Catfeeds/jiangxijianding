<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/30
 * Time: 11:11
 */

namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\service\OrganizeWork;
use app\common\service\Organize;
use app\common\service\Work;
use app\common\service\StbArea;
use app\common\service\ExamCenter;
use app\common\service\Admin;
use think\Request;

class MechanismController extends AdminBase
{
    private $organizeModel;
    private $Organize;
    private $WorkModel;
    private $SstbArea;
    private $SexamCenter;
    private $Sadmin;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->organizeModel = new OrganizeWork();
        $this->Organize = new Organize();
        $this->WorkModel = new Work();
        $this->SstbArea = new StbArea();
        $this->SexamCenter = new ExamCenter();
        $this->Sadmin = new Admin();
    }

    /**
     * 首页展示
     * @return \think\response\View
     */
    public function index()
    {
        $map = [];
        //获取ID
        $arrAdmin = session("adminuser");
        $id = $arrAdmin['id'];
        $arrData['username'] = '';
        $arrData['code'] = '';
        $arrData['linkman'] = '';
        $arrData['phone'] = '';
        if (Request::instance()->isPost() || Request::instance()->isGet())
        {
            $arrData = Request::instance()->param();
            if (!empty($arrData['username'])){
                $map['name'] = ['like','%'.$arrData['username'].'%'];
            }else{
                $arrData['username'] = '';
            }

            if (!empty($arrData['code'])){
                $map['code'] = ['like','%'.$arrData['code'].'%'];
            }else{
                $arrData['code'] = '';
            }
            if (!empty($arrData['linkman'])){
                $map['linkman'] = ['like','%'.$arrData['linkman'].'%'];
            }else{
                $arrData['linkman'] = '';
            }
            if (!empty($arrData['phone'])){
                $map['phone'] = ['like','%'.$arrData['phone'].'%'];
            }else{
                $arrData['phone'] = '';
            }
        }
        $map['create_id'] = $id;
        //分页参数
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $arrOrganize = $this->Organize->BaseSelectPage($paginate,$map,'',"id desc ");
//        print_r($arrOrganize);die;
        return view("index",['arrOrganize'=>$arrOrganize,'map'=>$arrData]);
    }

    /**
     * 添加页面
     * @return \think\response\View
     */
    public function addorganize()
    {
//        $map['parent_id'] = 0;
        $map['parent_id'] = 1359;  //江西省
        $arrArea = $this->SstbArea->BaseSelect($map);
        $arrAdmin = session("adminuser");
        if ($arrAdmin['center_type'] == 1)
        {
            $center = $this->SexamCenter->where(['status'=>1])->select();
        }else{
            $center = $arrAdmin['center_id'];
        }
//        print_r($center);die;
        return view("add",['arrArea'=>$arrArea,'center_type'=>$arrAdmin['center_type'],'center'=>$center]);
    }

    public function details()
    {
        if (Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (empty($arrData['id']) || empty($arrData['type'])){
                /** @var TYPE_NAME $this */
                return $this->success("非法操作","/admin/ExamPlan/index");
            }
            if ($arrData['type'] == 1){
                $arrData['type'] = 6;
            }else if ($arrData['type']== 2){
                $arrData['type'] = 7;
            }else if ($arrData['type'] == 3){
                $arrData['type'] = 4;
            }else{
                $arrData['type'] = 0;
            }
//            print_r($arrData);die;
            //获取ID
            $arrAdmin = session("adminuser");
            $organizeId = $arrAdmin['id'];
//            $arrOrganize = $this->organizeModel->find($arrData);
            $field = "organize.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrData,$field);

//            print_r($arrWork);die;
            return view("details",['arrWork'=>$arrWork]);
        }
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
    public function getList($where = [],$field)
    {
        $map['organize.id'] = $where['id'];
        $map['exam_work.type'] = $where['type'];
        $map['exam_work_level.type'] = $where['type'];
        //查询鉴定计划所有的数据
        $arrOrganize = $this->organizeModel->getListData($map,$field);
        $arrOrganize = collection($arrOrganize)->toArray();

        foreach ($arrOrganize as $k=>$v){
            $v['address'] = str_replace(","," ",$v['address']);
            $arrOrganize[$k]['build_date'] = date("Y-m-d H:i:s",$v['build_date']);
        }

        //取出不同的东西
        $arr = array_columns($arrOrganize,['wid','workname','wdname','work_level']);
        //根据workid 去重
        $arrWork = array_unique_key($arrOrganize,"wid");

        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k=>$v){
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'wdname'));
            $work_level = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
        }
        if (empty($arrOrganize)){
            $arrWork = $this->Organize->BaseSelect(['id'=>$where['id']]);
            foreach ($arrWork as $k=>$v){
//                $v['address'] = str_replace(","," ",$v['address']);
                $arrWork[$k]['build_date'] = date("Y-m-d",$v['build_date']);
                $arrWork[$k]['typename'] = '';
                $arrWork[$k]['wtid'] = '';
                $arrWork[$k]['wid'] = '';
                $arrWork[$k]['workname'] = '';
                $arrWork[$k]['work_level'] = '';
                $arrWork[$k]['wdname'] = [];
                $arrWork[$k]['level'] = [];

            }
        }

        return $arrWork;
    }

    /**
     * 跳转到修改页面
     * @return \think\response\View
     */
    public function uporganize()
    {
        if (Request::instance()->isGet())
        {
            $arrData = input();
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            //获取work
            $work = $this->WorkModel->BaseSelect(['status'=>1]);
            if ($arrData['type'] == 1){
                $arrData['type'] = 6;
            }else if ($arrData['type']== 2){
                $arrData['type'] = 7;
            }else if ($arrData['type'] == 3){
                $arrData['type'] = 4;
            }else{
                $arrData['type'] = 0;
            }
            $field = "organize.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrData,$field);
            $level = "";
            $arrOrganize = [];
            foreach ($arrWork as $k=>$v){
                if (isset($arrWork[$k]['level'][0])){
                    $level = $arrWork[$k]['level'][0];
                }
                $arrOrganize['type'] = $v['type'];
                $arrOrganize['status'] = $v['status'];
                $arrOrganize['is_institution'] = $v['is_institution'];
                $arrOrganize['username'] = $v['username'];
                $arrOrganize['name'] = $v['name'];
                $arrOrganize['code'] = $v['code'];
                $arrOrganize['address'] = explode(",",$v['address']);
                $arrOrganize['build_date'] = $v['build_date'];
                $arrOrganize['dutyer'] = $v['dutyer'];
                $arrOrganize['linkman'] = $v['linkman'];
                $arrOrganize['phone'] = $v['phone'];
                $arrOrganize['address_code'] = $v['address_code'];
                $arrOrganize['id'] = $v['id'];
                $arrOrganize['detail_address'] = $v['detail_address'];
                $arrOrganize['subordinate_admin'] = $v['subordinate_admin'];
            }
//            $map['parent_id'] = 0;
            $map['parent_id'] = 1359;   //江西省
            $arrArea = $this->SstbArea->BaseSelect($map);
            if ($arrOrganize['address'])
            {
                $arrOrganize['address'][0] = isset($arrOrganize['address'][0])?$arrOrganize['address'][0]:" ";
                $arrOrganize['address'][1] = isset($arrOrganize['address'][1])?$arrOrganize['address'][1]:" ";
                $arrOrganize['address'][2] = isset($arrOrganize['address'][2])?$arrOrganize['address'][2]:" ";
            }
            $adminUserOrg = $this->Sadmin->BaseSelect(['exam_center_id'=>$arrData['id'],'type'=>2],['phone','username']);
            if ($adminUserOrg){
                $adminUserOrg = collection($adminUserOrg)->toArray();
                foreach ($adminUserOrg as $k=>$v)
                {
                    if ($v['phone'] == $arrOrganize['phone']){
                        unset($adminUserOrg[$k]);
                    }
                }
            }else{
                $adminUserOrg = '';
            }
            $arrAdmin = session("adminuser");
            if ($arrAdmin['center_type'] == 1)
            {
                $center = $this->SexamCenter->where(['pid'=>$arrAdmin['center_id']])->whereOr(['id'=>$arrAdmin['center_id']])->select();
            }else{
                $center = $arrAdmin['center_id'];
            }
//            print_r($adminUserOrg);die;
            return view("update",['arrArea'=>$arrArea,'arrOrganize'=>$arrOrganize,"work"=>$work,"arrWork"=>$arrWork,"level"=>$level,'adminUserOrg'=>$adminUserOrg,'center_type'=>$arrAdmin['center_type'],'center'=>$center]);
        }
    }

    /**
     * 跳转到批量添加页面
     * @return \think\response\View
     */
    public function batchorganize()
    {
        return view('batch');
    }


    /**
     * 批量添加
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function batch()
    {
        //获取ID
        $arrAdmin = session("adminuser");
        $organizeId = $arrAdmin['id'];
        //判断文件夹是否存在不存在则创建
//        if (! file_exists ( ROOT_PATH . 'public' . DS .'uploads'.DS .'excel'. DS .$organizeId )) {
//            mkdir ( ROOT_PATH . 'public' . DS .'uploads'.DS . 'excel'. DS .$organizeId, 0777, true );
//        }
        //处理Excel文件请求
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('file');
        if (is_null($file)) {
            return layuiMsg(-1,"上传文件不能为空~");
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS .'uploads'. DS .'excel'. DS .$organizeId);

        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $organizeId . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
//            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            //将数据拼接成数组
            $sumCount = 0;
            $data = [];
            //获取ID
            $arrAdmin = session("adminuser");
            $id = $arrAdmin['id'];
//            print_r($excel_array);die;
            foreach ($excel_array as $k => $v) {
                if ($v[10] != 1 && $v[10] != -1){
                    return layuiMsg(-1,"请检查收费标准,重新上传~");
                }
                $data[$k]['phone'] = trim($v[0]); //手机号
                $adminData[$k]['oldphone'] = trim($v[0]); //手机号
                $adminData[$k]['phone'] = trim($v[1]); //备用账号
                $adminData[$k]['type'] = 2; //类型   组织
                $adminData[$k]['status'] = 1; //状态
                $data[$k]['name'] = trim($v[2]); //组织名称
                $data[$k]['code'] = trim($v[3]);//代码
                $data[$k]['address'] = trim($v[4]);//地址
                $data[$k]['detail_address'] = trim($v[5]);//详细地址
                $data[$k]['build_date'] = trim(strtotime($v[6]));//创建时间
                $data[$k]['linkman'] = trim($v[7]);//联系人
                $data[$k]['dutyer'] = trim($v[8]);//负责人
                if ($v[9] == "鉴定所"){
                    $v[9] = 1;
                }else if ($v[9]== "院校"){
                    $v[9] = 2;
                }else if ($v[9] == "机构"){
                    $v[9] = 3;
                }
                $data[$k]['type'] = trim($v[9]);//组织类型
                $data[$k]['is_institution'] = trim($v[10]);//收费标准
                $data[$k]['create_id'] = $id;//创建人id
                $data[$k]['subordinate_admin'] = $arrAdmin['center_id'];//所属鉴定中心
                $sumCount++;
            }

            //删除空数据
            foreach ($data as $k => $v) {
                if (preg_match("/\s/", $v['name'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['code'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['address'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['detail_address'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s\s/", $v['build_date'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['linkman'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['dutyer'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['phone'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['type'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['is_institution'])) {
                    unset($data[$k]);
                }
            }

            //验证导入的数据真实性
            $newArray = $this->verificationData($data);
            if (isset($newArray['code']) &&$newArray['code'] == -1) {
                return layuiMsg(-1,$newArray['msg']);
            }
            //拼接成数组，插入数据
//            print_r($addData);die;
            $phone = array_column($data, 'phone');
            $username = array_column($data, 'username');
            $code = array_column($data, 'code');

            $repeat_arr = repeat_arr($phone);
            $repeat_num = implode(array_values($repeat_arr), ',');

            $username_arr = repeat_arr($username);
            $username_num = implode(array_values($username_arr), ',');

            $code_arr = repeat_arr($code);
            $code_num = implode(array_values($code_arr), ',');

            if (!empty($username_arr)){
                return layuiMsg(-1,'导入失败: 表格中' . $username_num . '用户名重复');
            }

            if (!empty($repeat_arr)){
                return layuiMsg(-1,'导入失败: 表格中' . $repeat_num . '手机号重复');
            }

            if (!empty($code_arr)){
                return layuiMsg(-1,'导入失败: 表格中' . $code_num . '代码重复');
            }
            $success = $this->organizeModel->batchAdd($data,$adminData);
            return $success;
        }else{
            return layuiMsg(-1,$file->getError());
        }

    }

    public function verificationData($paramArray)
    {
        //删除重复数组 删除个数记录下返回给用户
        $ii = 1;
        foreach ($paramArray as $key => $array) {
            $ii++;
            //验证数据是否为空
            if (empty($array['phone']) || empty($array['name']) || empty($array['code']) || empty($array['address'])|| empty($array['detail_address']) || empty($array['build_date']) || empty($array['linkman']) || empty($array['dutyer']) || empty($array['type']) || empty($array['is_institution']) || empty($array['create_id']) || empty($array['subordinate_admin'])) {
                return layuiMsg(-1,'导入失败:第' . $ii . '行，数据不能为空');
            }
            //验证姓名合法性
            $preg_name='/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
            if(!preg_match($preg_name,$array['name'])){
                return layuiMsg(-1,'导入失败:第'. $ii .'行，姓名不合法');
            }
            //验证手机号唯一性
            $onlyphone = $this->organizeModel->onlyUser($array['phone'],'phone');
            if ($onlyphone) {
                return layuiMsg(-1,'导入失败:第 ' . $ii . '行，存在重复手机号');
            }
            //验证用户名唯一性
            $onlyusername = $this->organizeModel->onlyUser($array['name'],'name');
            if ($onlyusername) {
                return layuiMsg(-1,'导入失败:第 ' . $ii . '行，存在重复用户名');
            }
            //验证用户名唯一性
            $onlycode = $this->organizeModel->onlyUser($array['code'],'code');
            if ($onlycode) {
                return layuiMsg(-1,'导入失败:第 ' . $ii . '行，存在重复代码');
            }

        }
//        dump($paramArray);die;
        //验证数据格式
        //数据验证
        $validata = Validate('Organize');

        $i = 1;
        foreach ($paramArray as $key => $item) {
            $i++;
            //场景应用
            if (!$validata->scene('addorganize')->check($item)) {
                return layuiMsg(-1,'导入失败: ' . $i . '行' . $validata->getError());
            }
        }
//        dump($paramArray);die;
        return $paramArray;
    }



}