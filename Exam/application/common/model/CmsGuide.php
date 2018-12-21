<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/16
 * Time: 10:12
 */
namespace app\common\model;

use traits\model\SoftDelete;
use think\Request;

class CmsGuide extends BaseModel
{
    use SoftDelete;


    public function getGuide()
    {
        return $this->field('id,guide_name')->select();
    }

    /**
     * 详情模块主栏目下的关联栏目
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function zhuGuide($id)
    {
      $data = $this->where('id',$id)->field('relation')->find();
        $ids = explode('-',$data['relation']);
        $names = $this->column('id,guide_name');

        foreach ($ids as $v)
        {
            $infos[$v]['guide_name'] = $names[$v];
            $infos[$v]['article'] = $this->getArticle($v,$limit=5);
        }
        return $infos;
    }

    /**
     * 获得同栏目下的所有文章
     * @param $id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getArticle($id,$limit=8,$num=1)
    {
      $info =  $this->alias('g')
            ->join('__CMS_ARTICLE__ a','g.id = a.guide_id')
            ->where('g.id',$id)
            ->where('a.urgency',1)
          ->where('a.delete_time','null')
          ->field('a.title,a.guide_id,guide_name,a.url,a.time')
            ->order('a.id desc')
            ->limit($limit)
            ->page($num)
            ->select();
        foreach ($info as $v)
        {
            $v['time'] = date('Y-m-d',$v['time']);
        }

      return $info;

    }
    /**
     * 获取所有id
     */
  public function ids()
  {
      return $this->field('id')->select();
  }

    /**
     * 获得所有栏目
     */
  public function guideData()
  {
      return $this->where('pid',0)->field('guide_name,id')->select();
  }

    /**获得关联栏目
     * @param $id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
  public function guideCOntact($id)
  {
      $info = $this->where('pid',$id)->field('guide_name')->select();
      $arr = [];
      foreach ($info as $v)
      {
          $arr[]=$v['guide_name'];
      }
      return $arr;
  }
}