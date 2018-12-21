<?php
namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\Certificate;
use app\common\service\ExamEnroll;
use app\common\service\CertWay;
use app\common\service\Grade;
use app\common\service\ApplyCertificate;
use think\Config;
use think\Request;

class CertificateController extends BaseApi
{
    protected $SCertificate;
    private $SExamEnroll;
    private $SCertWay;
    private $Sgrade;
    private $SapplyCertificate;

    public function __construct()
    {
        $this->SCertificate = new Certificate();
        $this->SExamEnroll = new ExamEnroll();
        $this->SCertWay    = new CertWay();
        $this->Sgrade    = new Grade();
        $this->SapplyCertificate    = new ApplyCertificate();
    }

    /**
     * 申请空白证书
     * @return array
     * @user 朱颖 2018/12/20~11:29
     */
    public function add()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();
            $file = $request->file('file');
            $uploadPath = ROOT_PATH . 'public' . DS .'uploads'.DS .'certPay';
            if (! file_exists ($uploadPath )) {
                mkdir ( $uploadPath, 0755, true );
            }
            if (empty($file))
            {
                return layuiMsg('-1','凭证上传失败,网络错误');
            }
            $info = $file->validate(['size'=>1967800,'ext'=>'jpg,png,jpeg'])->move($uploadPath);
            if(!$info)
            {
                return layuiMsg('-1','凭证上传失败');
            }
            $adminuser = session('adminuser');
            $addCert['apply_num'] = $webData['apply_num'];
            $addCert['unused_num'] = $webData['apply_num'];
            $addCert['apply_id'] = $adminuser['id'];
            $addCert['center_id'] = $adminuser['center_id'];
            $addCert['apply_role'] = $adminuser['center_type'];
            $addCert['file_path'] = 'public' . DS .'uploads'.DS .'certPay'.DS.$info->getSaveName();
            $arrCert = $this->SapplyCertificate->addSave($addCert);
            if ($arrCert)
            {
                return layuiMsg('1','申请成功');
            }else{
                return layuiMsg('-1','申请失败,网络错误');
            }
        }else{
            return layuiMsg('-1','非法操作');
        }
    }
    //一键申请证书
    public function addLog()
    {
        if (!Request::instance()->isPost()){
            return layuiMsg('-1', '非法操作');
        }
        $webData = Request::instance()->param();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }
//        echo 1;die;
        $arrGrade = $this->Sgrade->getGradeByid(['result'=>1,'grade.exam_plan_id'=>$webData['exam_plan_id']],['`grade`.*',' `exam_order`.`pay_state`','
	userinfo.id_card AS id_no','exam_enroll.work_direction_name AS work_direction_name','exam_plan.type as exam_plan_type','exam_plan.batch_num','exam_plan.title as exam_plan_name','exam_enroll.organize_id as enroll_organize','userinfo.gender','userinfo.id as userinfo_id','userinfo.gender','exam_center.address_code as center_address_code','organize.address_code as organize_address_code','exam_center.`code` as center_code','organize.`code` as organize_code','exam_center.type as exam_center_type']);
        //有效期 当前时间的 三年后
        $now = strtotime(date('Y-m-d',time()).'+3year');
        if (empty($arrGrade))
        {
            return layuiMsg(-1, "暂无证书可申请");
        }
        $adminuser = session('adminuser');
        foreach ($arrGrade as $k=>$v)
        {
            $addData[$k]['id_type'] = $v['id_type'];//证件类型
            $addData[$k]['type'] = $v['exam_plan_type'];//证书类型
            $addData[$k]['id_card'] = $v['id_card'];//证件号
            $addData[$k]['userinfo_id'] = $v['userinfo_id'];//用户id
            $addData[$k]['exam_card'] = $v['TicketNum'];//准考证
            //专项能力
            if ($v['exam_plan_type'] == config('ExamWorkType.special_ability')&& $adminuser['center_type'] == 2 || $v['exam_plan_type'] == config('ExamWorkType.special_ability') && $adminuser['center_type'] == 3 )
            {
                $addData[$k]['series_number'] = $v['exam_enroll_id'];//导入编号
            }
            $addData[$k]['no'] = $v['exam_enroll_id'];//证书编号
            $addData[$k]['exam_batch'] = $v['batch_num'];//考试批次
            $addData[$k]['work'] = $v['work_name'];//工种
            $addData[$k]['current_level'] = $v['level'];//证书级别
            //只有考评员有有效期
            if($v['exam_plan_type'] == config('ExamWorkType.exam_plan_type'))
            {
                $addData[$k]['validity_time'] = $now;//有效期
            }
            $addData[$k]['exam_enroll_id'] = $v['exam_enroll_id'];//报名id
            $addData[$k]['source'] = $v['enroll_organize'];//考生来源
            $addData[$k]['username'] = $v['username'];//考生姓名
            $addData[$k]['theory_score'] = $v['theory_score'];//理论分数
            $addData[$k]['watch_score'] = $v['watch_score'];//实操分数
            $addData[$k]['synthesize_score'] = $v['synthesize_score'];//综合分数
            $addData[$k]['gender'] = !empty($v['gender'])?$v['gender']:'女';//性别
            $addData[$k]['direction'] = $v['work_direction_name'];//方向
            $addData[$k]['exam_plan_id'] = $v['exam_plan_id'];//鉴定计划id
            $addData[$k]['exam_plan_name'] = $v['exam_plan_name'];//鉴定计划名称
            $addData[$k]['create_id'] = $adminuser['id'];//创建人id
            $addData[$k]['center_id'] = $adminuser['center_id'];//鉴定中心id
            $addData[$k]['exam_time'] = $v['exam_time'];//考试时间
            $addData[$k]['address_code'] = !empty($v['center_address_code'])?$v['center_address_code']:$v['organize_address_code'];//考试地点
            $addData[$k]['code'] = !empty($v['organize_code'])?$v['organize_code']:$v['center_code'];//机构或鉴定中心代码
            $addData[$k]['level'] = $v['level'];//级别
            $addData[$k]['exam_center_type'] = $v['exam_center_type'];//鉴定中心类型
        }
        $this->SCertificate->createCertNum($addData);
        print_r($addData);
        die;
    }

    public function delete()
    {

    }

    public function update()

    {

    }

    /** 证书邮寄
     * @return array
     * @user xuweiqi
     */
    public function certMail()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $data = [
                'cert_way' => 2,
            ];
            $res = $this->SCertificate->updateCertData($data, ['id' => $arrData['id']]);
            if ($res == true) {
                return layuiMsg(1, '操作成功');
            } else {
                return layuiMsg(-1, '您未修改操作!');
            }
        };
    }

    /** 证书邮寄编辑
     * @return array
     * @user xuweiqi
     */
    public function editCertMail()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $id = $arrData['id'];
            unset($arrData['id']);
            $res = $this->SCertificate->updateCertData($arrData, ['id' => $id]);
            if ($res == true) {
                return layuiMsg(1, '操作成功');
            } else {
                return layuiMsg(-1, '您未修改操作!');
            }
        };
    }

    /** 证书现场领取
     * @return array
     * @user xuweiqi
     */
    public function invite()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $data = [
                'cert_way' => 1,
            ];
            $res = $this->SCertificate->updateCertData($data, ['id' => $arrData['id']]);
            if ($res == true) {
                return layuiMsg(1, '操作成功');
            } else {
                return layuiMsg(-1, '您未修改操作!');
            }
        }
    }

    /**
     * [cert 证书领取方式修改]
     * @return [type] [description]
     */
    public function cert()
    {
        if (Request::instance()->isPost()) {
            $arrData['exam_plan_id'] = input('post.plan');
            $arrData['organize_id'] = session("organizeuser")['id'];
            $arrData['status'] = config("ExamEnrollStatus.printticket");
            $cert = input('post.cert');
            $arrId = $this->SExamEnroll->BaseSelect($arrData, ['id']);

            $arrId = collection($arrId)->toArray();
            $where['exam_enroll_id'] = array('in', array_column($arrId, 'id'));
            $res = $this->SCertificate->BaseUpdate(['cert_way' => $cert, 'update_time' => time()], $where);
            if ($res) {
                return layuiMsg(1, '操作成功');
            } else {
                return layuiMsg(-1, '操作失败');
            }
        }
    }

    /**  证书查询登录
     * @return array
     * @user xuweiqi
     */
    public function certloginAction()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            if(!captcha_check($arrData['yzm'])){
                return layuiMsg(-2,'验证码错误!');
            }
            if (($arrData['id_no'] && $arrData['username']) || ($arrData['id_no'] && $arrData['certificate_no']) || ($arrData['username'] && $arrData['certificate_no'])) {
                $map['id_no'] = $arrData['id_no']?$arrData['id_no']:'';
                $map['username'] = $arrData['username']?$arrData['username']:'';
                $map['certificate_no'] = $arrData['certificate_no']?$arrData['certificate_no']:'';
                foreach( $map as $k=>$v){
                    if( !$v ){
                        unset( $map[$k] );
                    }
                }
                $data = $this->SCertificate->BaseFind($map);
                if (empty($data)) {
                    return layuiMsg(-1,"未查询到相关信息!");
                } else {
                    $sdata = [
                        'id_no' => $arrData['id_no'],
                        'username' => $arrData['username'],
                        'certificate_no' => $arrData['certificate_no'],
                    ];
                    session('user', $sdata);
                    return layuiMsg(1,"查询成功");
                }
            } else {
                return layuiMsg(-1, '输入以上两项内容才可查询');
            }
            return layuiMsg(-1,"请求失败");

        }
    }

    /** 添加领取信息
     *
     */
    public function apply()
    {
        if (Request::instance()->isPost()) {

            $arrData = input('post.');
            $info = [];
            $field = 'c.id';
            $arrAdmin = session("organizeuser");
            $arrWhere['c.source'] = $arrAdmin['id'];
            $arrWhere['ed.order_id'] = $arrData['plan'];
            $data = $this->SCertificate->selectExamGrade($arrWhere);
            foreach ($data as $key => $val)
            {
                $info[$key]['cert_get_way'] = $arrData['cert'];
                $info[$key]['zip_code'] = $arrData['zip_code'];
                $info[$key]['address']   = $arrData['address'];
                $info[$key]['phone']    = $arrData['phone'];
                $info[$key]['certificate_id'] = $val['id'];
            }
            $res = $this->SCertWay->BaseSaveAll($info);
            if($res)
            {
                return layuiMsg(1,'申领成功');
            }else{
                return layuiMsg(-1,'申领失败');
            }
        }
    }

    /** 证书查询 - 退出登录
     * @return int
     * @user xuweiqi
     */
    public function loginOut()
    {
        session('user', null);
        return 1;
    }

}