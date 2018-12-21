<?php
namespace app\common\model;

class ExamApply extends BaseModel
{
	public $status;
	public function __construct($data=[])
	{
		parent::__construct($data);
        $arr= config('applyStatusText.status');
        if(count($data)>0){
            if(key_exists('status',$data)){
                $this->status = $arr[$data['status']];
            }
        }
	}
}