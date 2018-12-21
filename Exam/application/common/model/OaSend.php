<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/12/2
 * Time: 9:54
 */
namespace app\common\model;

class OaSend extends BaseModel
{
    public function getStatusAttr($value)
    {

        $status = [0=>'未发送',
            1=>'<span  style="color:indianred">未读</span>',
            2=>'<span style="color: #5FB878;">已读</span>',
            3=>'<span style="color: #4000A0">删除</span>'];
        return ['val'=>$value,'text'=>$status[$value]];
    }
}