<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/2
 * Time: 9:25 AM
 */

namespace app\common\service;

use app\common\model\Notice as MNotice;

/**
 * Class Notice
 * @package app\common\service
 */
class Notice extends MNotice
{
    /**
     * @param $param
     * @param $field
     * @return string
     * @user 李海江 2018/11/2~10:26 AM
     */
    public function getAll($page, $param, $field)
    {
        $data = $this->BaseSelectPage($page, $param, $field, 'create_time desc');
        $data = json_decode(json_encode($data));
        return $data->data;
    }

    /**
     * @param $param
     * @param $field
     * @user 李海江 2018/11/2~10:26 AM
     */
    public function getOne($param, $field)
    {
        return $this->BaseFind($param, $field, 'create_time desc');
    }
}