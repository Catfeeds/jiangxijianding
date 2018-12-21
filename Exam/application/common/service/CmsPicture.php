<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/30
 * Time: 11:31
 */
namespace app\common\service;

use app\common\model\CmsPicture as MCmsPicture;

class CmsPicture extends MCmsPicture
{
    /**
     * 获取指定类型的轮播图
     * @param $type
     * @param bool $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/10/26~2:21 PM
     */
    public function getPicture($type, $field = true, $order = 'sort asc')
    {
        $arrayList = $this->BaseSelect(['type' => $type, 'status' => 1], $field, $order);
        $arrayList = addPath(collection($arrayList)->toArray(), 'url');
        return $arrayList;
    }


    public function zhanshi()
    {
        return $this->where('status=1')->where('type=1')->where('sort','<>',0)->field('id,title,url,inter')->order(['sort'=>'asc'])->select();
    }
}


