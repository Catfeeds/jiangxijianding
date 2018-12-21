<?php
/**
 * Created by PhpStorm.
 * User: zhuying
 * Date: 2018/11/23
 * Time: 16:27
 */

namespace app\common\service;

use app\common\model\ExamStaffLog as MExamStaffLog;
use app\common\model\Temporary;
use app\common\model\Organize;
use app\common\model\ExamCenter;
class TemporaryExamStaffLog
{
    private $MOrganize;
    private $Mtemporary;
    private $MexamCenter;
    private $MExamStaffLog;

    public function __construct()
    {
//        parent::__construct();
        $this->MOrganize = new Organize();
        $this->MExamStaffLog = new MExamStaffLog();
        $this->Mtemporary = new Temporary();
        $this->MexamCenter = new ExamCenter();

    }

    public function addTemporary($webData = [],$adminuser=[])
    {
        $addTemporary = [];
        $whereTem = '';
        foreach ($webData['staff_name'] as $k=>$v)
        {
            $addTemporary[$k]['name'] = $v;
            if(!preg_match("/^1[34578]\d{9}$/", $webData['phone'][$k])){
                return layuiMsg(-1, "手机号不正确");
            }
            $addTemporary[$k]['phone'] = $webData['phone'][$k];
            $addTemporary[$k]['type'] = $webData['roleType'];
            $addTemporary[$k]['remarks_column'] = $webData['remarks_column'][$k];
            $whereTem .='name = "'.$v.'" and phone = '.$webData['phone'][$k].' and type = '.$webData['roleType'].' and remarks_column = "'.$webData['remarks_column'][$k].'" or ';
        }
        $whereTem = substr($whereTem, 0, -3);
        // 启动事务
        $this->MExamStaffLog->startTrans();
        try {

            $arrTemporary = $this->Mtemporary->where($whereTem)->select();
            if (!empty($arrTemporary) && count($arrTemporary) == count($addTemporary))
            {
                $idTemporary = $arrTemporary;
            }else{
                $idTemporary = $this->Mtemporary->BaseSaveAll($addTemporary,'','id');
            }
            if (!$idTemporary){
                throw new \Exception('分配失败');
            }
            if ($webData['typeArea'] == 2)
            {
                $code = $this->MOrganize->BaseFind(['id'=>$webData['code']],"address_code")['address_code'];
            }else if ($webData['typeArea'] == 3){
                $webData['code'] = substr($webData['code'],0,strlen($webData['code'])-1);
                $code = $this->MexamCenter->BaseFind(['id'=>$webData['code']],"address_code")['address_code'];
            }else{
                $code = "";
            }
            $addStaffLog = [];
            $logWhere = '';
            foreach ($idTemporary as $k=>$v)
            {
                $addStaffLog[$k]['exam_plan_id'] = $webData['exam_plan_id'];
                $addStaffLog[$k]['be_assigned_id'] = $v['id'];
                $addStaffLog[$k]['name'] = $webData['staff_name'][$k];
                $addStaffLog[$k]['site_type'] = $webData['typeArea'];
                $addStaffLog[$k]['site_id'] = $webData['code'];
                $addStaffLog[$k]['distribution_id'] = $adminuser['id'];
                $addStaffLog[$k]['distribution_type'] = $adminuser['center_type'];
                $addStaffLog[$k]['type'] = $webData['roleType'];
                $addStaffLog[$k]['exam_place'] = $code;
                $logWhere .= 'be_assigned_id = '.$v['id'].' and exam_plan_id = '.$webData['exam_plan_id'].' and type ='.$webData['roleType'].' or ';
            }
            $logWhere = substr($logWhere, 0, -3);
            $Verification = $this->MExamStaffLog->where($logWhere)->select();

            if (!empty($Verification))
            {
                throw new \Exception('分配失败,已分配过');
            }
            $arrStaffLog = $this->MExamStaffLog->BaseSaveAll($addStaffLog);
            if (!$arrStaffLog){
                throw new \Exception('分配失败');
            }
//            print_r($arrStaffLog);die;
            // 提交事务
            $this->MExamStaffLog->commit();
            return layuiMsg(1,'添加成功');
        } catch (\Exception $e) {
            // 回滚事务
            $this->MExamStaffLog->rollback();
            return layuiMsg(-1,$e->getMessage());
        }
    }

}