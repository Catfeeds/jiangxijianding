<?php

namespace app\api\controller;
use app\common\service\LearningRecord;
use think\Request;
use app\common\controller\BaseApi;

class LearningRecordController extends BaseApi
{
    private $Srecord;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->Srecord = new LearningRecord();
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

            $objDel = $this->Srecord->destroy($arrData);

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