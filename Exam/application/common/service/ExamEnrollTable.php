<?php
namespace app\common\service;

use app\common\model\ExamEnroll as MExamEnroll;
use app\common\model\UserLogin as MUserLogin;
use app\common\model\ExamPlan as MExamPlan;
use app\common\model\Organize as MOrganize;
use app\common\model\WorkLevelSubject as MWLS;
use app\common\model\Certificate as MCertificate;
use app\common\model\Thesis as MThesis;
use app\common\model\ExamEnrollFile as MExamEnrollFile;
use app\common\model\Userinfo as MUserinfo;

class ExamEnrollTable extends MExamEnroll
{

    private $SExamEnroll;
    private $SUserLogin;
    private $SExamPlan;
    private $SOrganize;
    private $SWLS;
    private $MCertificate;
    private $MThesis;
    private $MExamEnrollFile;
    private $MUserinfo;
    public function __construct()
    {
        parent::__construct();
        $this->SExamEnroll = new MExamEnroll();
        $this->SUserLogin = new MUserLogin();
        $this->SExamPlan = new MExamPlan();
        $this->SOrganize = new MOrganize();
        $this->SWLS = new MWLS();
        $this->MCertificate = new MCertificate();
        $this->MThesis = new MThesis();
        $this->MExamEnrollFile = new MExamEnrollFile();
        $this->MUserinfo     = new MUserinfo();
    }

    /**
     * 添加报名信息
     * @param $arrDate
     * @param $path
     * @param $auditway
     * @return bool|string
     * @user xuweiqi
     */
    public function addExamEnrollOne($arrDate,$path,$auditway)
    {
        $this->startTrans();
        try {
            $req= $this->SExamEnroll->BaseSave($arrDate);
            if (!$req) {
                throw new \Exception('添加报名成功');
            }
            if($auditway == 1){
                $examEnroll = [];
                $arr = ['zheng'=>1,'fan'=>4,'xueli'=>2,'cert'=>3];

                $i = 0;
                foreach($path as $k=>$v)
                {
                    $examEnroll[$i]['path'] = $v;
                    $examEnroll[$i]['exam_enroll_id'] = $req;
                    $examEnroll[$i]['type'] = $arr[$k];
                    $i++;
                }
                $res = $this->MExamEnrollFile->BaseSaveAll($examEnroll);
                if (!$res) {
                    throw new \Exception('添加证书信息成功');
                }
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 修改报名信息
     * @param $arrDate
     * @param $map
     * @param $path
     * @param $fileMap
     * @param $audit_way
     * @return bool|string
     * @user xuweiqi
     */
    public function editExamEnrollOne($arrDate,$map,$path,$fileMap,$audit_way)
    {
        $this->startTrans();
        try {
            $fileDel = $this->MExamEnrollFile->BaseDelete(['exam_enroll_id'=>$fileMap],['id']);
            $req= $this->SExamEnroll->BaseUpdate($arrDate,$map);
            if (!$req) {
                throw new \Exception('新建报名失败');
            }
            $arrEnroll = $this->SExamEnroll -> BaseFind($map);
            $barcodeNum = substr($arrEnroll['bar_code'],18);
            $barcodeNum = $barcodeNum+1;
            $upbarcode = [
                'bar_code' => session('user')['id_card'].$barcodeNum,
            ];
            $reqcode= $this->SExamEnroll->BaseUpdate($upbarcode,$map);
            if(!$reqcode){
                throw new \Exception('新建报名失败');
            }

            if($audit_way == 1){
                $examEnroll = [];
                $arr = ['zheng'=>1,'fan'=>4,'xueli'=>2,'cert'=>3];

                $i = 0;
                foreach($path as $k=>$v)
                {
                    $examEnroll[$i]['path'] = $v;
                    $examEnroll[$i]['exam_enroll_id'] = $fileMap;
                    $examEnroll[$i]['type'] = $arr[$k];
                    $i++;
                }
                $res = $this->MExamEnrollFile->BaseSaveAll($examEnroll);
                if (!$res) {
                    throw new \Exception('更新审核文件信息失败');
                }
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    public function updateEnrollinfo($webData)
    {
        $this->startTrans();
        try {
            $res = $this->SExamEnroll->BaseUpdate($webData,['id'=>$webData['id']]);
            if(!$res){
                throw new \Exception('修改报名信息失败');
            }
            unset($webData['id']);
            $res1 = $this->SUserLogin->BaseUpdate($webData,['id'=>$webData['login_id']]);
            if(!$res1){
                throw new \Exception('修改用户信息失败');
            }
            $mes = $this->MUserinfo->BaseUpdate(['username'=>$webData['name']],['user_login_id'=>$webData['login_id']]);
            if(!$mes){
                throw new \Exception('修改用户信息失败');
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    /**
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function joinOrganize($where = [])
    {
        return $this->SExamEnroll
            ->field("organize.*,exam_enroll.id as eid,exam_enroll.exam_plan_id")
            ->join("__ORGANIZE__","exam_enroll.organize_id=organize.id")
            ->where($where)
            ->where("organize_id>0 AND organize.`status`=1")
            ->group("exam_enroll.organize_id")
            ->select();
    }

    /**
     * 查询报名表exam_enroll 数据
     * @param string $map
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function ExamEnroll($map = ''){
        $field="exam_enroll.id,exam_enroll.audit_way,exam_enroll.bar_code,exam_enroll.work_id,exam_enroll.remark,exam_enroll.audit_site,exam_enroll.exam_site,exam_enroll.work_level_subject_level,exam_enroll.user_login_id,exam_plan.title,exam_plan.work_type_name,work.id as wid,work.name as workname,work_direction.name as directionname,work_level_subject.level,exam_enroll.exam_type,exam_enroll.organize_id,exam_enroll.source,exam_enroll.create_time,exam_enroll.update_time,exam_enroll.thesis_state,exam_enroll.status,exam_enroll.id,work.id as wid,work_direction.id as did,exam_plan.id as exam_plan_id,exam_plan.pay_endtime,exam_plan.audit_endtime,exam_plan.exam_time,exam_plan.print_starttime,exam_plan.print_endtime,work_direction.id as wtid,user_login.name as username,user_login.id_card,rl.reason,rl.review_time,rl.review_ip,admin.name as admin_name,work_type.name as typename";
        // print_r($paginate);die;
        $data= $this->SExamEnroll
            ->join("__EXAM_PLAN__","exam_enroll.exam_plan_id=exam_plan.id")
            ->join("__USER_LOGIN__","user_login.id=exam_enroll.user_login_id","left")
            ->join("__WORK__","exam_enroll.work_id=`work`.id",'left')
            ->join("__WORK_DIRECTION__","exam_enroll.work_direction_id=work_direction.id",'left')
            ->join("__WORK_LEVEL_SUBJECT__","work_level_subject.level=`exam_enroll`.work_level_subject_level",'left')
            ->join("__WORK_TYPE__","work.work_type_id=work_type.id",'left')
            ->join('__REVIEW_LOG__ rl','rl.reviewed_id=exam_enroll.id and rl.reviewed_type=2','left')
            ->join('__ADMIN__','rl.review_id=admin.id and rl.review_type=1','left')
            ->field($field)
            ->where($map)
            ->where("exam_plan.delete_time IS NULL and work.delete_time IS NULL and work_direction.delete_time IS NULL and work_level_subject.delete_time IS NULL and work_type.delete_time IS NULL and admin.delete_time IS NULL")
            ->group("exam_enroll.id")
            ->order("id desc")
            ->paginate(config('paginate.list_rows'),false,['query'=>request()->param()]);
        return $data;
    }

    /**
     * @param array $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getExamEnrollTrial($paginate = [],$map = [])
    {
        $field = "exam_enroll.*,exam_plan.title,`work`.name as work_name,exam_plan.audit_endtime,work_direction.name as work_direction_name,review_log.status as review_status,user_login.id_card,userinfo.username as exam_enroll_name";
        list($listRows, $simple, $config) = $paginate;
        return $this->SExamEnroll
            ->join("__EXAM_PLAN__","exam_enroll.exam_plan_id=exam_plan.id",'left')
            ->join("__WORK__","exam_enroll.work_id=`work`.id","left")
            ->join("__WORK_DIRECTION__","exam_enroll.work_direction_id=work_direction.id","left")
            ->join("__REVIEW_LOG__","review_log.reviewed_id=exam_enroll.id","left")
            ->join("__USER_LOGIN__","exam_enroll.user_login_id=user_login.id","left")
            ->join("__USERINFO__","userinfo.user_login_id=user_login.id","left")
            ->field($field)
            ->where($map)
            ->where("`work`.delete_time IS NULL AND exam_plan.delete_time IS NULL AND work_direction.delete_time IS NULL AND review_log.delete_time IS NULL")
            ->order("exam_enroll.id desc")
            ->group("exam_enroll.id")
            ->paginate($listRows, $simple, $config);

    }


    /**
     * 连接user_login表 获取一条数据
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function joinUserById($where = [],$field = '')
    {
        $field="e.*,u.*,work_direction.name as work_direction_name,work.name as work_name,u.name as username,rl.reason";
        $data= $this->SExamEnroll->alias('e')
            ->join("__USER_LOGIN__ u","e.user_login_id=u.id","left")
            ->join("__WORK_DIRECTION__","e.work_direction_id=work_direction.id","left")
            ->join("__WORK__","e.work_id=work.id","left")
            ->join('__REVIEW_LOG__ rl','rl.reviewed_id=e.id','left')
            ->field($field)
            ->where($where)
            ->order('rl.create_time desc')
            ->find();
        return $data;
    }


    /**
     * 根据条件修改
     * @param string $data
     * @param string $where
     * @return false|int|string
     */
    public function updByid($data='',$where='')
    {
        return $this->SExamEnroll->BaseUpdate($data,$where);
    }

    /**
     * 添加数据
     * @param string $data
     * @param string $where
     * @return false|int|string
     */
    public function insByid($data)
    {
        return $this->SExamEnroll->BaseSave($data);
    }

    /**
     * 验证用户基本数据唯一，真实性
     * @param string     data 要验证的数据
     * @param string    field 要查询字段
     * @param int       userid 用户id
     * @return bool
     * @user liuxin     2018/9/17
     */
    public function onlyUser($data, $field, $userid = '')
    {
        if ($userid) {
            return $this->SExamEnroll->where($field, $data)
                ->where('id', 'neq', $userid)
                ->find();
        } else {
            return $this->SExamEnroll->where($field, $data)
                ->find();
        }

    }


    /**
     * @验证数据user_login_id exam_plan_id work_id work_direction_id work_level_id唯一性
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUser($map){
        return $this->SExamEnroll
            ->join("__USER_LOGIN__","user_login.id=exam_enroll.user_login_id")
            ->where("user_login_id",'=',$map['user_login_id'])
            ->where("exam_plan_id",'=',$map['exam_plan_id'])
            ->where("work_id",'=',$map['work_id'])
            ->where("work_direction_id",'=',$map['work_direction_id'])
            ->where("work_level_subject_level",'=',$map['work_level_subject_level'])
            ->where("user_login.delete_time is null")
//            ->field("user_login.id_card as username")
            ->select();
    }



    /**根据手机号取出 获取当前用户的信息
     * @return mixed
     * @user xuweiqi
     */
    public function getUserLoginInfo(){
        $userLoginId=$this->SUserLogin->BaseFind(['mobile'=>session('user')['mobile']]);
        return $userLoginId;
    }

    /**根剧当前用户查出 当前用户的报考信息
     * @param string $map
     * @return string
     */
    public function getExamEnrollJoinInfo($map=[],$group=''){
        $userLoginId= $this->getUserLoginInfo();
        //查询报名表各关联信息
        $field="ee.*,ep.id as epid,ep.title,ep.exam_time,ep.pay_endtime,ep.audit_endtime,ep.print_starttime,ep.print_endtime,w.name as workname,wd.name as directionname,w.id as wid,wd.id as did";
        $join=array(
            ['__EXAM_PLAN__ ep',"ee.exam_plan_id=ep.id",'left'],
            ["__USERINFO__ u","u.user_login_id=ee.user_login_id",'right'],
            ["__WORK__ w","ee.work_id=w.id",'left'],
            ["__WORK_DIRECTION__ wd","ee.work_direction_id=wd.id",'left'],
        );
        $map['ee.user_login_id'] = $userLoginId['id'];
        $examJoinData=$this->SExamEnroll->BaseJoinSelect('ee',$join,$map,[$field],'ee.create_time desc','',$group,'ee.id');
        return $examJoinData;
    }

    /**根剧当前用户查出 当前用户的报考信息
     * @param string $map
     * @return string
     */
    public function getExamEnrollPlusJoinInfo($map=[]){
        $userLoginId= $this->getUserLoginInfo();
        //查询报名表各关联信息
        $field="ee.*,ep.title,ep.exam_time,w.name as workname,wd.name as directionname,w.id as wid,wd.id as did,t.id as tid,t.path";
        $join=array(
            ['__EXAM_PLAN__ ep',"ee.exam_plan_id=ep.id",'left'],
            ["__USERINFO__ u","u.user_login_id=ee.user_login_id",'right'],
            ["__WORK__ w","ee.work_id=w.id",'left'],
            ["__WORK_DIRECTION__ wd","ee.work_direction_id=wd.id",'left'],
            ["__THESIS__ t","ee.id=t.exam_enroll_id","left"],
        );
        $map['ee.user_login_id'] = $userLoginId['id'];
        $examJoinData=$this->SExamEnroll->BaseJoinSelect('ee',$join,$map,[$field],'','','ee.id');
        return $examJoinData;
    }

    /**根剧当前用户查出 当前用户的报考信息
     * @param string $map
     * @return string
     */
    public function getExamEnrollInvoicePlusJoinInfo($map=[]){
        $userLoginId= $this->getUserLoginInfo();
        //查询报名表各关联信息
        $field="ee.*,ee.id as eeid,ep.title,ep.exam_time,w.name as workname,wd.name as directionname,w.id as wid,wd.id as did,o.order_id,i.status as istatus,i.update_time as iupdate_time,eo.order_num,eo.total_money";
//        $field="ee.id ,ee.user_login_id,ep.title,ep.exam_time,w.name as workname,wd.name as directionname,w.id as wid,wd.id as did,o.order_id,i.status,i.update_time";
        $join=array(
            ['__EXAM_PLAN__ ep',"ee.exam_plan_id=ep.id"],
            ["__USERINFO__ u","u.user_login_id=ee.user_login_id"],
            ["__WORK__ w","ee.work_id=w.id"],
            ["__WORK_DIRECTION__ wd","ee.work_direction_id=wd.id"],
            ["__EXAM_ORDER_DETAIL__ o","o.enroll_id=ee.id",'right'],
            ["__INVOICE__ i","i.order_id=o.order_id",'left'],
            ["__EXAM_ORDER__ eo","i.order_id=eo.id"],
        );
        $map['ee.user_login_id'] = $userLoginId['id'];
        $examJoinData=$this->SExamEnroll->BaseJoinSelect('ee',$join,$map,[$field],'','','ee.id');
        return $examJoinData;
    }

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function findExamEnroll($map){
        return $this->SExamEnroll->BaseFind($map);
    }


    /**查询examenroll 关联数据
     * @param $map
     * @return data
     * @user xuweiqi
     */
    public function selectExamEnroll($map=''){
        return $this->ExamEnroll($map)[0];
    }

    /** 删除数据
     * @param $map
     * @return int
     * @user xuweiqi
     */
    public function deleteExamEnrollOne($map)
    {
        return $this->SExamEnroll->BaseDelete($map);
    }

    /**
     * [查询机构下报名缴费信息]
     * @return [type] [description]
     */
    public function selectEnrollpay($map,$column='')
    {
        $field="exam_enroll.*,login.id_card,work.name as workname,wd.name as directionname,login.name as username,ep.title,ep.pay_endtime".$column;
        $data= $this->SExamEnroll
            ->join('__EXAM_PLAN__ ep','exam_enroll.exam_plan_id=ep.id and ep.delete_time IS NULL')
            ->join('__USER_LOGIN__ login',"exam_enroll.user_login_id=login.id and login.delete_time IS NULL")
            ->join("__WORK__ work","work.id=exam_enroll.work_id and work.delete_time IS NULL")
            ->join("__WORK_DIRECTION__ wd","exam_enroll.work_direction_id=wd.id and wd.delete_time IS NULL",'left')
            ->join('__LEVEL_SUBJECT_PRICE__ wls','wls.level=exam_enroll.work_level_subject_level and ((exam_enroll.theory = 1 and wls.subject_id =1 )or(exam_enroll.comprehen = 1 and wls.subject_id =3 )or(exam_enroll.operation = 1 and wls.subject_id =2 )) and wls.delete_time IS NULL')
            ->field($field)
            ->where($map)
            ->group("exam_enroll.id")
            ->order("exam_enroll.id desc")
            ->paginate(config('paginate.list_rows'),false,['query'=>['plan_id'=>$map['exam_enroll.exam_plan_id']]]);
        // $examJoinData=$this->SExamEnroll->BaseJoinSelectPage($paginate,'ee',$join,$map,[$field],'ee.status asc');
        
        return ['examJoinData'=>$data];

    }

    /**
     * [查询计划下工种级别信息]
     * @return [type] [description]
     */
    public function selectEnrollwork($map)
    {
        $field="work.id as wid,work.name,work_level_subject_level";
        $data= $this->SExamEnroll
            ->join("__WORK__ work","work.id=exam_enroll.work_id")
//            ->join("__JURY_REVIEW__ jr",'exam_enroll.work_id = jr.work_id and exam_enroll.exam_plan_id = jr.exam_plan_id and exam_enroll.work_level_subject_level=jr.level','left')
            ->field($field)
            ->where($map)
            ->select();
        
        return $data;

    }

    /**
     * [查询当前鉴定计划的需要交费的人数和总金额]
     * @return [type] [description]
     */
    public function selectPrice($map,$fields)
    {
        $join2=array(
            ['__USER_LOGIN__ login',"ee.user_login_id=login.id"],
            ['__LEVEL_SUBJECT_PRICE__ wls','ee.work_level_subject_level=wls.level','left'],
        );
        $field2='count(distinct ee.id) as people,sum(if(ee.theory = 1 and wls.subject_id =1,'.$fields.',0 )+ if(ee.comprehen = 1 and wls.subject_id =2,'.$fields.',0 )+ if(ee.operation = 1 and wls.subject_id =3,'.$fields.',0)) as price';
        $count = $this->SExamEnroll->BaseJoinFind('ee',$join2,$map,[$field2]);
        return $count;
    }

    /**
     * [orderDetail 机构详情订单数据]
     * @return [type] [description]
     */
    public function orderDetail($organize_id,$plan_id,$status,$column='')
    {
        $arrWhere['ee.organize_id'] = $organize_id;
        $arrWhere['ee.exam_plan_id'] = $plan_id;
        $arrWhere['ee.status']       = $status;
        $field="ee.id,wls.subject_id,ee.work_level_subject_level as level,if(wls.subject_id=1,wls.$column,0) as lilun_price,if(wls.subject_id=2,wls.$column,0) as shicao_price,if(wls.subject_id=3,wls.$column,0) as zonghe_price";
        $join=array(
            ['__LEVEL_SUBJECT_PRICE__ wls','ee.work_level_subject_level=wls.level','left'],
        );
        $orderData=$this->SExamEnroll->BaseJoinSelect('ee',$join,$arrWhere,[$field]);
        $newData = [];
        $arr     = [];
        foreach ($orderData as $key => $val) 
        {
            $str = $val['id'].'_'.$val['level'];
            if(in_array($str,$arr))
            {
                $newData[$str]['lilun_price'] = $val['lilun_price']+$newData[$str]['lilun_price'];
                $newData[$str]['shicao_price'] = $val['shicao_price']+$newData[$str]['shicao_price'];
                $newData[$str]['zonghe_price'] = $val['zonghe_price']+$newData[$str]['zonghe_price'];
            }
            else
            {
                $arr[] = $str;
                $newData[$str]['enroll_id'] = $val['id'];
                $newData[$str]['level'] = $val['level'];
                $newData[$str]['lilun_price'] = $val['lilun_price'];
                $newData[$str]['shicao_price'] = $val['shicao_price'];
                $newData[$str]['zonghe_price'] = $val['zonghe_price'];

            }
            
        }
        return $newData;
    }

    /** 机构打印准考证
     * @user 李海江 2018/12/7~13:31
     */
//    public function printcard($where,$field)
//    {
//        $join = [
//          ['__USER_LOGIN__ u','u.id=ee.user_login_id']
//        ];
//
//       return  $this->SExamEnroll->BaseJoinSelect('ee',$join,$where,[$field]);
//    }

    public function getAreaByExamPlanId($map=[],$field="",$group="")
    {
        return $this->SExamEnroll
            ->join("organize","exam_enroll.organize_id=organize.id","left")
            ->join("area","organize.address_code=area.code","left")
            ->join("exam_center","exam_center.`name`=exam_enroll.exam_site","left")
            ->field($field)
            ->where($map)
            ->group($group)
            ->select();
    }


}