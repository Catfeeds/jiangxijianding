<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午5:35
 */
namespace app\common\service;

use app\common\model\Userinfo as MUserinfo;

class Userinfo extends MUserinfo {

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @$user xuwieqi
     */
    public function findUserinfoData($map){
        return $this->BaseFind($map);
    }

    /**
     * @param $data
     * @return mixed|string
     * @$user xuwieqi
     */
    public function saveUserinfoData($data){
        return $this->BaseSave($data);
    }

    /**
     * @param $data
     * @param $map
     * @return false|int|string
     * @$user xuwieqi
     */
    public function updateUserinfoData($data,$map){
        return $this->BaseUpdate($data,$map);
    }


}