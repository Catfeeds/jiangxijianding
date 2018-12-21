<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2018/12/18
 * Time: 21:42
 */

namespace app\common\service;
use app\common\model\ApplyCertDetail as MapplyCertDetail;
use app\common\service\ReviewLog;
use app\common\service\ApplyCertificate;

class ApplyCertDetail extends MapplyCertDetail
{
    private $MapplyCertDetail;
    private $SreviewLog;
    private $SApplyCertificate;

    public function __construct()
    {
        parent::__construct();
        $this->MapplyCertDetail = new MapplyCertDetail();
        $this->SreviewLog = new ReviewLog();
        $this->SApplyCertificate = new ApplyCertificate();
    }
    public function addLog($arrData,$cert_id,$center_id,$id)
    {
        // 启动事务
        $this->MapplyCertDetail->startTrans();
        try {
            $addCert = $this->MapplyCertDetail->BaseSaveAll($arrData);
            if (!$addCert){
                throw new \Exception('审核失败');
            }
            $addLog = [];
            $addLog['reviewed_id'] = $cert_id;
            $addLog['reviewed_type'] = 10;
            $addLog['review_time'] = time();
            $addLog['review_type'] = 1;
            $addLog['review_id'] = $id;
            $addLog['review_ip'] = getip();
            $addLog['status'] = 1;
            $certLog = $this->SreviewLog->BaseSave($addLog);
            if (!$certLog){
                throw new \Exception('审核失败');
            }
            $updateApply = $this->SApplyCertificate->BaseUpdate(['status'=>1],['id'=>$cert_id]);
            if (!$updateApply){
                throw new \Exception('审核失败');
            }
            $this->MapplyCertDetail->commit();
            return layuiMsg(1,'审核成功');
        } catch (\Exception $e) {
            // 回滚事务
            $this->MapplyCertDetail->rollback();
            return layuiMsg(-1,$e->getMessage());
        }
    }
}