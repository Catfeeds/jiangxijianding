<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/14
 * Time: 10:29 AM
 */

namespace app\common\service;

use app\common\model\CmsAppAbout as MCmsAppAbout;

/**
 * Class CmsAppAbout
 * @package app\common\service
 */
class CmsAppAbout extends MCmsAppAbout
{
    /**
     * 获取一条数据
     * @param $param
     * @param array $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/14~10:34 AM
     */
    public function getOne($param, $field = [])
    {
        return $this->BaseFind($param, $field);
    }


    /**
     * 获取app所有数据 分页
     * @param $param
     * @param array $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/14~10:49 AM
     */
    public function getAppPageAll($param, $field = [], $order = 'id desc')
    {
        //分页
        $page = request()->param('page') ? request()->param('page') : 1;
        $count = request()->param('count') ? request()->param('count') : 10;
        $paginate = [$count, true, ['page' => $page]];
        //查询
        $data = $this->BaseSelectPage($paginate, $param, $field, $order);
        $data = json_decode(json_encode($data));
        $data = $data->data;
        //拼接题号
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->title = (((int)$page - 1) * (int)$count + (int)$i + 1) . '、' . $data[$i]->title;
        }
        return $data;
    }
}