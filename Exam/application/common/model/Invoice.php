<?php
namespace app\common\model;

class Invoice extends BaseModel
{
	public $pay_state;
	public $istatus;
	public function __construct($data=[])
	{
		parent::__construct($data);
        $arr= config('ExamOrderText.pay_state');
        $arrs= config('ExamOrderText.status');
        if(count($data)>0){
            if(key_exists('pay_state',$data)){
                $this->pay_state = $arr[$data['pay_state']];
            }
            if(key_exists('status',$data)){
                $this->istatus = $arrs[$data['status']];

            }
        }
	}

    /**
     * @param $update_time
     * @return false|string
     * @user xuweiqi
     */
	public function getUpdateTimeAttr($update_time)
    {
        return date('Y-m-d H:i:s',$update_time);
    }


}
