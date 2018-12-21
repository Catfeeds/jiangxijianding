<?php
namespace app\common\model;

class ExamOrder extends BaseModel
{
    public $pay_state;
    public $level;
    public $status;
    public $rlstatus;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $arr=[ 1 => '未支付',2 => '线下缴费','3'=>'缓缴费成功','4'=>'已支付','5'=>'申请缓缴费','6'=>'缓缴费失败'];
        $arrstatus = [NULL=>'未提交',0=>'未提交', 1=> '已提交', 2=>'已受理' ,3=>'已开票' ,4 =>'纸质邮寄', 5=>'电子邮件发送', 6=>'线下领取' ,7=>'纸质已邮寄',8=>'邮件已发送'];
        $arrrlsatus = [NULL=>'', 1=>'初审通过(待复审)', 2=>'初审不通过', 3=>'复审通过(待终审)', 4=>'复审不通过', 20=>'终审通过(申请成功)',52=>'终审不通过',50=>'线下缴费通过'];
        $level      = config('EnrollStatusText.work_level_subject_level');
        if(count($data)>0){
            if(key_exists('pay_state',$data)){
                $this->pay_state = $arr[$data['pay_state']];
            }
            if(key_exists('level',$data)){
                $this->level = $level[$data['level']];
            }
            if(key_exists('status',$data)){
                $this->status = $arrstatus[$data['status']];
            }
            if(key_exists('rlstatus',$data)){
                $this->rlstatus = $arrrlsatus[$data['rlstatus']];
            }

        }
    }

}