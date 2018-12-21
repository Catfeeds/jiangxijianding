<?php

/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/16
 * Time: 10:12
 */
namespace app\common\model;
use think\Request;

class Certificate extends BaseModel
{
    public $isTakes;

    public $result;
    public $current_level;
    public $pay_state;
    public $type;

    public function __construct($data=[])
    {
        parent::__construct($data);
        $level = config('EnrollStatusText.work_level_subject_level');
        $state = config('ExamOrderText.pay_state');
        $type = config('ExamEnrollStatus.id_type');
        if(count($data)>0){
            if (key_exists('current_level', $data)) {
                $this->current_level = $level[$data['current_level']];
            }
            if (key_exists('pay_state', $data)) {
                $this->pay_state = $state[$data['pay_state']];
            }
            if (key_exists('type', $data)) {
                $this->type = $type[$data['type']];
            }
        }
    }

    /**
     * @param $certWay
     * @return mixed
     */
    public function getCertWayAttr($certWay)
    {
        $arr=[''=>'未确认', 1 => '自取',2 => '快递'];
        return $arr[$certWay];
    }

    /**
     * @param $isTake
     * @return mixed
     */
    public function getIsTakeAttr($isTake)
    {
        $arr=[''=>'未领取',0 => '未取证',1 => '已取证'];
        return $arr[$isTake];
    }

    /**
     * @param $obtain_evidence_time
     * @return false|string
     * @user 朱颖 2018/11/9~10:00
     */
    public function getObtainEvidenceTimeAttr($obtain_evidence_time)
    {
        return date("Y-m-d H:i:s",$obtain_evidence_time);
    }

    /**
     * @param $validity_time
     * @return false|string
     * @user 朱颖 2018/11/9~10:00
     */
    public function getValidityTimeAttr($validity_time)
    {
        return date("Y-m-d H:i:s",$validity_time);
    }

}