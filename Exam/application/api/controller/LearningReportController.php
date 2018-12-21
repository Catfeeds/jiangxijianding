<?php

namespace app\api\controller;
use app\common\service\LearningReport;
use think\Request;
use app\common\controller\BaseApi;

class LearningReportController extends BaseApi
{
    private $Sreport;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->Sreport = new LearningReport();
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

            $objDel = $this->Sreport->Supdate($where,$update);

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
}