<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/22
 * Time: 18:14
 */
namespace app\common\model;

use traits\model\SoftDelete;

class CmsOrder extends BaseModel
{
    use SoftDelete;

    /**
     * 获得顶级栏目与是否开启导航栏关系
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getGuide()
    {
        return $this->alias('o')->join('__CMS_GUIDE__ g','o.id=g.id')
            ->where('o.status',1)->where('g.pid',0)
            ->where('g.delete_time','null')
            ->field('o.id,g.guide_name,o.guide_order,o.top,o.top_order,o.top_limit,o.section,o.section_order,o.section_limit,o.bottom,o.bottom_limit,o.bottom_order')->select();
    }

    /**
     * 次级栏目与首页顶部，中部，尾部的关系
     * @return false|\PDOStatement|string|\think\Collection
     */
     public function getGuideTwo()
     {
         return $this->alias('o')->join('__CMS_GUIDE__ g','o.id=g.id')
             ->where('g.guide_test',0)
             ->where('g.delete_time','null')
             ->field('o.id,g.guide_name,o.guide_order,o.top,o.top_order,o.top_limit,o.section,o.section_order,o.section_limit,o.bottom,o.bottom_limit,o.bottom_order')->select();
     }
    /**
     * 联表查询文章标题与地址
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getArticle()
    {
        return $this->alias('o')
            //->join('__GUIDE__ g','o.id = g.id')
            ->join('__CMS_ARTICLE__ a','o.id = a.guide_id')
            ->where('o.top',1)
            ->where('a.delete_time','null')
            ->order('o.top_order','a.id desc')
            ->field('o.id,a.title')
            //->group('o.id')
            ->limit('o.limit')
            ->select();
    }
}