<?php

namespace app\api\controller;
use app\common\service\LearningAudit;
use think\Request;
use app\common\controller\BaseApi;

class LearningAuditController extends BaseApi
{
    private $Saudit;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->Saudit = new LearningAudit();
    }

    public function state()
    {
        if (Request::instance()->isPost())
        {
            $arrData = Request::instance()->post();

            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            $where = ['id'=>$arrData['id']];
            $update = ['state'=>$arrData['state']];

            $objDel = $this->Saudit->Supdate($where,$update);

            if ($objDel){
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "操作成功";
                return $arrMsg;
            }else{
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "操作失败";
                return $arrMsg;
            }
        }
    }

    public function delete()
    {
        if (Request::instance()->isPost())
        {
            $arrData = input('post.');
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            $objDel = $this->Saudit->destroy($arrData);

            if ($objDel){
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "删除成功";
                return $arrMsg;
            }else{
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "删除失败";
                return $arrMsg;
            }
        }
    }
}
