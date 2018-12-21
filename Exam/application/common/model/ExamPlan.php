<?php
namespace app\common\model;
use Think\Db;
class ExamPlan extends BaseModel
{

    /**
     * @param $time
     * @return false|string
     * @user xuweiqi
     */
    public function getExamTimeAttr($time)
    {
       return date('Y-m-d',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 朱颖 2018/11/2~13:48
     */
    public function getEnrollStarttimeAttr($time)
    {
       return date('Y-m-d',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 朱颖 2018/11/2~13:48
     */
    public function getEnrollEndtimeAttr($time)
    {
       return date('Y-m-d',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 朱颖 2018/11/2~13:48
     */
    public function getAuditEndtimeAttr($time)
    {
       return date('Y-m-d',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 朱颖 2018/11/2~13:48
     */
    public function getPayEndtimeAttr($time)
    {
       return date('Y-m-d',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 朱颖 2018/11/2~13:48
     */
    public function getPrintStarttimeAttr($time)
    {
       return date('Y-m-d',$time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 朱颖 2018/11/2~13:49
     */
    public function getPrintEndtimeAttr($time)
    {
       return date('Y-m-d',$time);
    }



}