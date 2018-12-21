<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/12/5
 * Time: 15:17
 */
namespace app\common\service;

use app\common\model\JuryLogin as MJuryLogin;


/**
 * Class Admin
 * @package app\common\service
 */
class JuryLogin extends MJuryLogin
{
    /**
     * @param $phone
     * @param $token
     * @return int|string
     * 添加数据
     */
    public function addInfo($phone,$token,$id)
    {
        return $this->insert(array('phone'=>$phone,'token'=>$token,'jury_id'=>$id));
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * 获取一个
     */
    public function getOne($where)
    {
        return $this->BaseFind($where,'','id desc');
    }

    /**
     * @param $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取考场考生
     */
    public function getExamSite($where)
    {
        return $this->field('exam_staff_log.exam_plan_id,exam_staff_log.site_id')->join('__EXAM_STAFF_LOG__','exam_staff_log.be_assigned_id=jury_login.jury_id')->where($where)->select();
    }

    public function delToken($where)
    {
        return $this->where($where)->delete();
    }

    public function updateToken($where)
    {
        return $this->where($where)->update(array('status'=>2));
    }
}