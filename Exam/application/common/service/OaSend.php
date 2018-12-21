<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/12/2
 * Time: 9:56
 */
namespace app\common\service;

class OaSend extends \app\common\model\OaSend
{
   public function getadmin($id,$where)
   {
       $infos = $this->alias('oa')
           ->join('__ADMIN__ a','oa.user_id = a.id')
           ->join('__OA_NOTICE__ no','oa.notice_id = no.id')
         //  ->join('__OFFICE__ of','oa.office_id = of.id')
           ->where('oa.to_id',$id)
           ->where($where)
           ->order('oa.id desc')
           ->field('oa.user_id,no.fujian,oa.status,a.name,a.office_id,oa.to_id,a.name,oa.create_time,no.id,no.title')
           ->paginate(config('paginate.list_rows'),false,['query'=>Request()->param()]);
       return $infos;
   }
}