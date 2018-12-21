<?php  

namespace app\common\model;


class JuryReview extends BaseModel
{
	public $status;
	public $level;
	public function __construct($data=[])
	{
		parent::__construct($data);
        $arr= config('EnrollStatusText.allot');
        $leveltext = config('EnrollStatusText.work_level_subject_level');
        if(count($data)>0){
            if(key_exists('status',$data)){
                $this->status = $arr[$data['status']];

            }
            if(key_exists('level',$data)){
                $this->level = $leveltext[$data['level']];
            }
        }
	}
}