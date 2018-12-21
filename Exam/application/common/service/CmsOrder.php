<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/30
 * Time: 10:11
 */
namespace app\common\service;

class CmsOrder extends \app\common\model\CmsOrder
{
    /**
     * @param array $ids
     * @param $str
     * @param $num
     */
    public function rere($ids,$str,$num)
    {
         $this->where('id','not in',$ids)->setField($str,$num);
    }

    /**
     * 顶部关联栏目查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function topgui()
    {
        return $this->alias('o')
            ->join('__CMS_GUIDE__ g','o.id=g.id')
            ->where('o.top',1)
            ->order('o.top_order')
            ->field('o.id,g.guide_name,o.top_order,top_limit')
            ->select();

    }
    /**
     * 顶部关联文章查询
     */
    public function toparticle($ids,$limit)
    {
        return $this ->alias('o')
            ->join('__CMS_ARTICLE__ a','o.id=a.guide_id')
            ->where('a.guide_id',$ids)
            ->where('a.urgency',1)
            ->where('a.delete_time','null')
            ->field('a.title,a.url,a.time')
            ->order('a.id desc')
            ->limit($limit)
            ->select();

    }

    /**
     * 顶部所需要的数据
     */
    public function topinfo($data)
    {
        foreach ($data as $v)
        {
            $infos[$v['id']]['id'] = $v['id'];
            $infos[$v['id']]['article'] = $this->toparticle($v['id'],$v['top_limit']);
        }
        return $infos;
    }

    /**
     * 中部关联栏目查询
     */
    public function sectiongui()
    {
        return $this->alias('o')->join('__CMS_GUIDE__ g','o.id = g.id')
            ->where('o.section',1)
            ->where('g.delete_time','null')
            ->order('o.section_order')
            ->field('o.id,g.guide_name,o.section_order,section_limit')
            ->select();
    }

    /**
     * 中部关联文章查询
     * @param $ids
     * @param $limit
     */
    public function sectionarticle($ids,$limit)
    {
        return $this->alias('o')->join('__CMS_ARTICLE__ a', 'o.id=a.guide_id')
            ->where('a.guide_id', $ids)
            ->where('a.urgency',1)
            ->where('a.delete_time','null')
            ->order('a.id desc')
            ->field('a.title,a.url,a.time')->limit($limit)->select();
    }

    /**
     * 中部所需数据
     * @param $data $this->sectiongui
     * @return mixed
     */
    public function sectioninfo($data)
    {
        foreach ($data as $v)
        {
            $infos[$v['id']]['id'] = $v['id'];
            $infos[$v['id']]['article'] = $this->sectionarticle($v['id'],$v['section_limit']);
        }
        return $infos;
    }

    /**
     * 底部关联栏目查询
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function bottomgui()
    {
        return $this->alias('o')->join('__CMS_GUIDE__ g','o.id = g.id')
            ->where('o.bottom',1)
            ->where('g.delete_time','null')
            ->order('o.bottom_order')
            ->field('o.id,g.guide_name,o.bottom_order,bottom_limit')->select();
    }

    /**
     * 底部关联文章查询
     * @param $ids
     * @param $limit
     */
    public function bottomarticle($ids,$limit)
    {
        return $this->alias('o')
            ->join('__CMS_ARTICLE__ a', 'o.id=a.guide_id')
            ->where('a.guide_id', $ids)
            ->where('a.urgency',1)
            ->where('a.delete_time','null')
            ->order('a.id desc')->
            field('a.title,a.time,a.url')->limit($limit)->select();
    }

    public function bottominfo($data)
    {
        foreach ($data as $v)
        {
            $infos[$v['id']]['id'] = $v['id'];
            $infos[$v['id']]['article'] = $this->bottomarticle($v['id'],$v['bottom_limit']);
        }
        return $infos;
    }
}