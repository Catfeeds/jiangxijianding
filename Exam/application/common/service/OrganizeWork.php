<?php
namespace app\common\service;
use app\common\model\Organize as MOrganize;
use app\common\model\ExamWork as MExamWork;
use app\common\model\ExamWorkLevel as MExamWorkLevel;
use app\common\service\Admin;

class OrganizeWork extends MOrganize
{
    private $SOrganize;
    private $SexamWork;
    private $SexamWorkLevel;
    private $Sadmin;

    public function __construct()
    {
        parent::__construct();
        $this->SOrganize = new MOrganize();
        $this->SexamWork = new MExamWork();
        $this->SexamWorkLevel = new MExamWorkLevel();
        $this->Sadmin = new Admin();
    }

    /**
     * @param $data
     * @param $field
     * @param string $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function onlyUser($data, $field, $userid = '')
    {
        if ($userid) {
            return $this->SOrganize->where($field, $data)
                ->where('id', 'neq', $userid)
                ->find();
        } else {
            return $this->SOrganize->where($field, $data)
                ->find();
        }

    }

    /**
     * @param array $map
     * @param array $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListData($map = [], $field = [], $order = '',$group = "",$paginate = [])
    {
        if ($paginate)
        {
            list($listRows, $simple, $config) = $paginate;
            return $this->SOrganize
                ->join("__EXAM_WORK__","organize.id=exam_work.exam_id","left")
                ->join("__WORK_TYPE__","work_type.id=exam_work.work_type","left")
                ->join("__WORK__","`work`.id=exam_work.work_id","left")
                ->join("__WORK_DIRECTION__","work_direction.work_id=`work`.id","left")
                ->join('__ADMIN__',"organize.create_id=admin.id",'left')
                ->join('__EXAM_CENTER__',"admin.exam_center_id=exam_center.id",'left')
                ->join("__EXAM_WORK_LEVEL__","`exam_work_level`.`alltype_id` = `organize`.`id` AND exam_work_level.exam_work_id = exam_work.id AND exam_work_level.type = exam_work.type","left")
                ->where($map)
                ->where("`exam_work`.`delete_time` IS NULL AND `work_type`.`delete_time` IS NULL AND `work`.`delete_time` IS NULL AND `work_direction`.`delete_time` IS NULL AND `exam_work_level`.`delete_time` IS NULL")
                ->field($field)
                ->group($group)
                ->order($order)
                ->paginate($listRows, $simple, $config);
        }
        return $this->SOrganize
            ->join("__EXAM_WORK__","organize.id=exam_work.exam_id","left")
            ->join("__WORK_TYPE__","work_type.id=exam_work.work_type","left")
            ->join("__WORK__","`work`.id=exam_work.work_id","left")
            ->join("__WORK_DIRECTION__","work_direction.work_id=`work`.id","left")
            ->join('__ADMIN__',"organize.create_id=admin.id",'left')
            ->join('__EXAM_CENTER__',"admin.exam_center_id=exam_center.id",'left')
            ->join("__EXAM_WORK_LEVEL__","`exam_work_level`.`alltype_id` = `organize`.`id` AND exam_work_level.exam_work_id = exam_work.id AND exam_work_level.type = exam_work.type","left")
            ->where($map)
            ->where("`exam_work`.`delete_time` IS NULL AND `work_type`.`delete_time` IS NULL AND `work`.`delete_time` IS NULL AND `work_direction`.`delete_time` IS NULL AND `exam_work_level`.`delete_time` IS NULL")
            ->field($field)
            ->group($group)
            ->order($order)
            ->select();

    }

    //批量添加
    public function batchAdd($data=[],$adminData=[])
    {
        // 启动事务
        $this->startTrans();
        try {
            $name_list = array_column($data,'phone');

            $objOrganize = $this->SOrganize->BaseSaveAll($data,'','id');
            if (!$objOrganize)
            {
                throw new \Exception('添加失败');
            }
            foreach ($adminData as $key=>$val)
            {
                $adminData[$key]['phone'] = explode('|',$val['phone']);
                foreach ($adminData[$key]['phone'] as $kk=>$vv){
                    $user = explode('&',$vv);
                    if(!preg_match("/^1[34578]\d{9}$/", $user[1])){
                        return layuiMsg(-1, "备用账号格式不正确");
                    }
                    $newAdminData[] =['name'=>$user[0],'phone'=>$user[1],'username'=>$user[0],'type'=>2,'oldphone'=>$val['oldphone']];
                }
            }
            $adminPhone = '';
            foreach ($newAdminData as $key=>$val){
                foreach ($objOrganize as $k=>$v)
                {
                    if ($val['oldphone'] == $v['phone']){
                        $newAdminData[$key]['exam_center_id']=$v['id'];
                    }
                }
                $adminPhone .= $val['phone'].',';
                unset($newAdminData[$key]['oldphone']);

            }

            $adminPhone = rtrim($adminPhone,',');
            $objAdmin = $this->Sadmin->BaseSelect(['phone'=>['in',$adminPhone]]);
            if (!empty($objAdmin))
            {
                throw new \Exception('备用账号重复');
            }
//            print_r($objOrganize);die;

            $objNewAdmin = $this->Sadmin->BaseSaveAll($newAdminData);
            if (!$objNewAdmin)
            {
                throw new \Exception('添加失败');
            }
            // 提交事务
            $this->commit();
            return layuiMsg(1,'添加成功,共成功'.count($objOrganize)."条");
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return layuiMsg(-1,$e->getMessage());
        }
    }

    /**
     * @param array $where
     * @param array $webData
     * @param array $arrExamWork
     * @param array $new
     * @param int $type
     * @return bool|string
     * @throws \think\exception\PDOException
     * @user 朱颖 2018/11/2~14:25
     */
    public function updateOrganize($where=[] ,$webData = [],$arrExamWork = [] ,$new = [] ,$type = 0)
    {
        // 启动事务
        $this->startTrans();
        try {
            if (!isset($webData['fullphone'])) {
                return layuiMsg(-1, "非法操作");
            }

            $adminUser = $webData['fullphone'];
            $adminUserName = $webData['fullname'];
            foreach ($adminUser as $k=>$v)
            {
                if(!preg_match("/^1[34578]\d{9}$/", $v)){
                    return layuiMsg(-1, "备用账号格式不正确");
                }
            }
            array_push($adminUser,$webData['phone']);
            array_push($adminUserName,$webData['name']);

            $adminUser = array_unique($adminUser);
            $phone = implode(",",$adminUser);
            $addUser['phone'] = ['in',$phone];
            $arrPhone = $this->Sadmin->BaseSelect($addUser);
            if (!$arrPhone || count($arrPhone) != count($adminUser))
            {
                $delAdminLog = $this->Sadmin->destroy(['exam_center_id'=>$where['id'],'type'=>2]);
                if (!$delAdminLog){
                    throw new \Exception('修改失败');
                }
                $addAdminUser = [];
                foreach ($adminUser as $k=>$v)
                {
                    $addAdminUser[$k]['phone'] = $v;
                    $addAdminUser[$k]['type'] = 2;      //组织
                    $addAdminUser[$k]['exam_center_id'] = $where['id']; //组织id
                    $addAdminUser[$k]['username'] = $adminUserName[$k]; //名称
                    $addAdminUser[$k]['name'] = $adminUserName[$k]; //名称
                    $addAdminUser[$k]['status'] = 1;
                }
                $arrAdminUser = $this->Sadmin->BaseSaveAll($addAdminUser);
                if (!$arrAdminUser){
                    throw new \Exception('修改失败');
                }
            }
//            print_r($adminUser);die;
            //修改主表
            $examplan = $this->SOrganize->BaseUpdate($webData,$where);
            if ( !$examplan )
            {
                throw new \Exception('修改失败');
            }
            $map['exam_id'] = $where['id'];
            $map['type'] = $type;
            $selExamWork = $this->SexamWork->BaseSelect($map);
            if (!empty($selExamWork)){
                $delWorkLog = $this->SexamWork->destroy($map);
                if ( !$delWorkLog )
                {
                    // 异常
                    throw new \Exception('修改失败');

                }
            }
            $ExamWork = $this->SexamWork->BaseSaveAll($arrExamWork,"",1);
            $ExamWork = collection($ExamWork)->toArray();
            if ( !$ExamWork )
            {
                throw new \Exception('修改失败');

            }
            $mapLevel['alltype_id'] = $where['id'];
            $mapLevel['type'] = $type;
            $selExamWorkLevel = $this->SexamWorkLevel->BaseSelect($mapLevel);

            if (!empty($selExamWorkLevel)){
                $delWorkLevelLog = $this->SexamWorkLevel->destroy($mapLevel);
                if ( !$delWorkLevelLog )
                {
                    throw new \Exception('修改失败');

                }
            }
            foreach ($new as $key=>$val)
            {
                foreach ($ExamWork as $k=>$v)
                {
                    if ($val['exam_work_id'] == $v['work_id']){
                        $new[$key]['exam_work_id'] = $v['id'];
                    }
                }
            }
            $ExamWorkLevel = $this->SexamWorkLevel->BaseSaveAll($new);
            if (!$ExamWorkLevel){
                throw new \Exception('修改失败');

            }
            // 提交事务
            $this->commit();
            return layuiMsg(1,'修改成功');
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return layuiMsg(-1,$e->getMessage());
        }


    }

    /**
     * @param array $data
     * @param string $table
     * @return int
     * @user 朱颖 2018/11/2~14:25
     */
    public function deletes($data=[] , $table = "")
    {
        if ($table == 'examwork'){
            return $this->SexamWork->destroy($data);
        }else if ($table == 'examworklevel'){
            return $this->SexamWorkLevel->destroy($data);
        }
    }
    public function selectWork($data=[] , $table = "")
    {
        if ($table == 'examwork'){
            return $this->SexamWork->BaseSelect($data);
        }else if ($table == 'examworklevel'){
            return $this->SexamWorkLevel->BaseSelect($data);
        }
    }


    /**
     * @param array $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWork($map=[])
    {
        $field = "work_type.`name` as wtname,`work`.name as wname,work_direction.`name` as wdname,exam_work_level.work_level as wllevel,work.id as wid,work_direction.id as wdid";
        return $this->SOrganize
            ->join("__EXAM_WORK__","organize.id=`exam_work`.exam_id","left")
            ->join("__EXAM_WORK_LEVEL__","organize.id = exam_work_level.alltype_id AND exam_work_level.exam_work_id = exam_work.id","left")
            ->join("__WORK_TYPE__","exam_work.work_type = work_type.id","left")
            ->join("__WORK__","exam_work.work_id = `work`.id","left")
            ->join("__WORK_DIRECTION__","work_direction.work_id = `work`.id","left")
            ->where($map)
            ->field($field)
            ->where("exam_work.delete_time is null and exam_work_level.delete_time is null and work_type.delete_time is null and work.delete_time is null and work_direction.delete_time is null")
            ->select();

    }
}