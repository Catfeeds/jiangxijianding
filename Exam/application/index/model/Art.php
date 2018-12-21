<?php
/**
 * Created by PhpStorm.
 * User: liuxin
 * date: 2018/7/9
 * Time: 下午2:35
 */
namespace app\index\model;

class Art extends model
{

    public function sel(){
        $data=$this->select();
        return $data;
    }
}