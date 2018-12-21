<?php
namespace app\common\model;
class Organize extends BaseModel
{
    public $type;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $arr=[ 1 => '鉴定所站',2 => '院校','3'=>'机构'];
        if(count($data)>0){
            if(key_exists('type',$data)){
                $this->type = $arr[$data['type']];
            }
        }
    }

}