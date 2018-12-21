<?php
namespace app\common\model;

class Userinfo extends BaseModel
{
    public $education;
    public function __construct($data=[])
    {
        parent::__construct($data);
        $education = config('EnrollStatusText.education');
        if (count($data) > 0) {
            if (key_exists('education', $data)) {
                $this->education = $education[$data['education']];
            }
        }
    }
}

