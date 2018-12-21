<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/2
 * Time: 11:18 AM
 */

namespace app\common\service;

use app\common\model\CmsArticle as MCmsArticle;

class CmsArticle extends MCmsArticle
{


    /**
     * app获取新闻 , web用不了
     * @param $param
     * @param bool $field
     * @param string $order
     * @return mixed
     * @user 李海江 2018/11/2~11:22 AM
     */
    public function appGetAll($param, $field = [], $order = 'id desc')
    {

        $page = request()->param('page') ? request()->param('page') : 1;
        $count = request()->param('count') ? request()->param('count') : 10;
        $page = [$count, true, ['page' => $page]];
        $data = $this->BaseSelectPage($page, $param, $field, $order);
        $data = json_decode(json_encode($data));
        $data = $data->data;
        for ($i = 0; $i < count($data); $i++) {
            if (!empty($data[$i]->id)) {
                $data[$i]->url = config('APP_PATH') . '/app/cms/content/id/' . $data[$i]->id;
                unset($data[$i]->id);
            }
        }
        return $data;
    }


    /**
     * 获取新闻列表
     * @param $param
     * @param bool $field
     * @param string $order
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/2~11:34 AM
     */
    public function getAll($param, $field = true, $order = 'id desc', $limit = true)
    {
        $data = $this->BaseSelect($param, $field, $order, $limit);
        for ($i = 0; $i < count($data); $i++) {
            if (!empty($data[$i]->id)) {
                $data[$i]->url = config('APP_PATH') . '/app/cms/content/id/' . $data[$i]->id;
                unset($data[$i]->id);
            }
        }
        return $data;
    }

    /**
     * app获取指定文章
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/6~11:53 AM
     */
    public function appGetOne($id)
    {
        return $this->BaseFind(['id' => $id]);
    }
}