<?php

/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/16
 * Time: 10:12
 */

namespace app\common\model;

use think\Request;

class CmsArticle extends BaseModel
{

    public function getTimeAttr($time)
    {
        return date('Y-m-d',$time);
    }
/**
     * 获得所有文章与栏目的数据
     */

    public function getGuideDate($where=[])
    {
        $data =  $this->alias('a')
            ->join('__CMS_GUIDE__ g', 'a.guide_id = g.id')
            ->join('__CMS_FU__ f', 'a.id = f.article_id')
            ->order('id desc')
            ->where($where)
            ->where('g.delete_time','null')
            ->field('a.title,a.id,a.guide_id,a.urgency,a.url,a.source,a.time,a.status,g.guide_name,f.content')
            ->paginate(config('paginate.list_rows'),false,['query'=>Request()->param()]);
//        return $this->getLastSql();
        return $data;
    }

    /**
     * 获得一篇文章
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getArticlefind($id)
    {
        $infos =  $this->alias('a')
            ->where('a.id', $id)
            ->join('__CMS_GUIDE__ g', 'a.guide_id=g.id')
            ->join('__CMS_FU__ f', 'a.id=f.article_id')
            ->where('g.delete_time','null')
            ->field('a.id,a.title,a.url,a.time,a.urgency,a.source,a.seotitle,a.description,a.guide_id,g.guide_name,f.content,a.exam_type,a.work_type,a.type_exam_id,a.red')
            ->find();

        return $infos;
    }


    /**
     * 获得所有为发布的文章
     */
    public function getArticle()
    {
        return $this->alias('a')
            ->join('__CMS_GUIDE__ g', 'a.guide_id=g.id')
            ->join('__CMS_FU__ f', 'a.id=f.article_id')
            ->where('id desc')
            ->where('g.delete_time','null')
            ->field('a.id,a.title,a.time,a.urgency,a.source,a.seotitle,a.keywords,a.description,a.guide_id,g.guide_name,f.content')
            ->select();
    }

    /**
     * 时间构造器
     * @param $time
     * @return false|string
     * @user 李海江 2018/11/6~11:46 AM
     */
    public function getCreateTimeAttr($time)
    {
        return date('Y.m.d', $time);
    }

    /**
     * 模型关联 一对一 详情
     * @return \think\model\relation\HasOne
     * @user 李海江 2018/11/6~11:50 AM
     */
    public function content()
    {
        return $this->hasOne('CmsFu', 'article_id', 'id');
    }

    /**
     * 模型关联 一对多 附件
     * @return \think\model\relation\HasMany
     * @user 李海江 2018/11/6~11:48 AM
     */
    public function fujians()
    {
        return $this->hasMany('CmsNoticeFujian', 'article_id', 'id');
    }


}