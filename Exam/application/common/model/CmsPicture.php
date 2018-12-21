<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/17
 * Time: 14:49
 */
namespace app\common\model;
use think\Request;

class CmsPicture extends BaseModel
{
  public function getTypeAttr($value)
  {
      $type = [1=>'网页轮播图',2=>'app轮播图'];
      return ['val'=>$value,'text'=>$type[$value]];
  }
}