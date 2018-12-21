<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/27
 * Time: 15:47
 */
namespace app\common\service;

use app\common\model\CmsContact as MCmsContact;
use app\common\model\CmsGuide;

class CmsContact extends MCmsContact
{
    /**
     * 添加栏目与栏目的关系
     */
    protected function SGuide()
    {
        return $SGuide = new CmsGuide();
    }
    /*
     * 获取关联的id
     */
    public function ids($id)
    {
        $str = $this->where('id',$id)->field('relation')->find();
        $data = explode('-',$str['relation']);
        return $data;
    }

    /**
     * 例子：附属栏目-附属栏目-
     * @param $id 主键id
     * @return string
     */
    public function detail($id)
    {

        $rel = $this->where('id',$id)->field('relation,id')->find();
        $data = explode('-',$rel['relation']);
       // $SGuide = new CmsGuide();
        foreach ($data as $v)
        {
            $info[] = $this->SGuide()->where('id',$v)->field('guide_name,id')->find();
        }
        foreach ($info as $vv)
        {
            $infos[] = $vv['guide_name'];
        }
        $shuju = implode('-',$infos);
        return $shuju;
    }

//    public function detailAll()
//    {
//        $info= $this->field('id,relation')->select();
//        $data = $this->Gguide();
//        foreach ($info as $v)
//        {
//            $info[$v['id']] = $this->detail($v['id']);
//        }
//         return $info;
//    }


    public function Gguide()
    {
         return $this->SGuide()->column('guide_name','id');
    }

    public function shudetai($id)
    {
        $rel = $this->where('id',$id)->field('relation,id')->find();
        $data = explode('-',$rel['relation']);
        // $SGuide = new CmsGuide();
        foreach ($data as $v)
        {
            $info[] = $this->SGuide()->where('id',$v)->field('guide_name')->find();
        }
        return $info;
    }
}