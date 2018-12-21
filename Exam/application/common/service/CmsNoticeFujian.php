<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/11/6
 * Time: 21:36
 */
namespace app\common\service;


class CmsNoticeFujian extends \app\common\model\CmsNoticeFujian
{
    /**
     * 根据文章id查询关联附件信息
     * @param $id 文章id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
   public function fujian($id)
   {
       return $this->alias('f')->join('__CMS_ARTICLE__ a','f.article_id = a.id')
           ->where('a.id',$id)->field('a.title,f.id,f.name,f.create_time,f.url,f.article_id')->select();
   }
   public function findFUid($id)
   {
      $infos =  $this->where('article_id',$id)->field('id')->select();
      $ids = [];
       foreach ($infos as $v)
       {
           $ids[] = $v['id'];
       }
       return $ids;
   }
}