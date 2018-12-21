<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/23
 * Time: 17:06
 */
namespace app\common\model;

use think\Request;

class CmsServe extends BaseModel
{

    public function getServe()
    {
        return $this->where('status',1)->field('title,id,order')->select();
    }
    public function getServeData()
    {
        return $this->where('order','>',0)->field('title,id')->order('order')->select();
    }
}