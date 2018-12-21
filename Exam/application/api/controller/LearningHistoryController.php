<?php

namespace app\api\controller;
use app\common\service\LearningHistory;
use think\Request;
use app\common\controller\BaseApi;
use app\common\service\LearningPaperHistory;

class LearningHistoryController extends BaseApi
{
    private $Shistory;
    private $SLearningPaperHistory;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->Shistory = new LearningHistory();
        $this->SLearningPaperHistory = new LearningPaperHistory();
    }

    public function delete()
    {
        if (Request::instance()->isPost())
        {
            $arrData = Request::instance()->post();

            if (!$arrData && empty($arrData['id']))
            {
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            $objDel = $this->SLearningPaperHistory->destroy($arrData['id']);

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