<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/23
 * Time: 9:41
 */

namespace app\common\service;

use app\common\model\Jury as Mjury;

/**
 * Class Jury
 * @package app\common\service
 */
class Jury extends Mjury
{

    private $SJuryCertificate;

    public function __construct($data = [])
    {
        $this->SJuryCertificate = new JuryCertificate();
        parent::__construct($data);
    }

    /**
     * 获取所有专家 分页
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/8~10:16 AM
     */
    public function getAll($map, $field = [], $order = 'id desc')
    {
        $page = ['', '', ['query' => request()->param()]];
        return $this->BaseSelectPage($page, $map, $field, $order);
    }

    /**
     * 获取指定一条
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user 李海江 2018/11/8~11:43 AM
     */
    public function getOne($map, $field = [])
    {
        return $this->BaseFind($map, $field);
    }

    /**
     * 更新数据
     * @param $data
     * @return false|int|string
     * @user 李海江 2018/11/8~3:03 PM
     */
    public function edit($data)
    {
        $data['status'] = (bool)$data['status'];
        return $this->BaseUpdate($data, ['id' => $data['id']]);
    }

    /**
     * 添加考评员
     * @param $data
     * @return mixed|string
     * @user 李海江 2018/12/4~5:32 PM
     */
    public function add($data)
    {
        $data['status'] = (bool)$data['status'];
        $id = $this->BaseSave($data);
        if ($id > 0) {
            $list = [];
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $v['card_create_time'] = strtotime($v['card_create_time']);
                    $v['card_expire_time'] = $v['card_create_time'] + 60 * 60 * 24 * 365 * 3;
                    $v['jury_id'] = $id;
                    $v['name'] = $data['name'];
                    $list[] = $v;
                }
            }
            $this->SJuryCertificate->add($list);
        }
        return $id;
    }

    /**
     * 获取考评人员的
     * @param string $where
     * @param string $field
     * @param string $order
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/15~14:26
     */
    public function getWorkByWorkId($where = "", $field = "", $order = "")
    {
        return $this
            ->join("__JURY_CERTIFICATE__", "jury.id = jury_certificate.jury_id", 'left')
            ->join("__WORK__", "jury_certificate.work_id = `work`.id", 'left')
            ->where($where)
            ->where("jury_certificate.delete_time IS NULL AND work.delete_time IS NULL")
            ->field($field)
            ->order($order)
            ->group("jury.name")
            ->select();
//        return $this
//            ->join("__EXAM_WORK__","jury.id=exam_work.exam_id AND type=8",'left')
//            ->join("__EXAM_WORK_LEVEL__","exam_work_level.exam_work_id=exam_work.id AND exam_work_level.alltype_id=jury.id AND exam_work_level.type=8",'left')
//            ->join("__WORK__","work.id=exam_work.work_id",'left')
//            ->where($where)
//            ->where("exam_work.delete_time IS NULL AND exam_work_level.delete_time IS NULL AND work.delete_time IS NULL")
//            ->field($field)
//            ->order($order)
//            ->group("jury.name")
//            ->select();
    }

    /**
     * @throws \think\exception\DbException
     */
//    public function selWorkLevelByid($paginate=[],$where=[])
//    {
//        list($listRows, $simple, $config) = $paginate;
//
//        return $this
//            ->join("__EXAM_WORK__","exam_work.exam_id=jury.id AND exam_work.type=3","left")
//            ->join("__WORK__","`work`.id=exam_work.work_id","left")
//            ->join("__EXAM_WORK_LEVEL__","exam_work_level.exam_work_id=exam_work.id AND exam_work_level.alltype_id=jury.id AND exam_work_level.type=3","left")
//            ->where($where)
//            ->field("jury.*,exam_work.id as eid,`work`.id as wid,`work`.`name` as wname,exam_work_level.id as ewlid,exam_work_level.work_level")
//            ->paginate($listRows, $simple, $config);
//    }

    /**
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//    public function selWorkLevel($where=[])
//    {
//        return $this
//            ->join("__EXAM_WORK__","exam_work.exam_id=jury.id AND exam_work.type=3","left")
//            ->join("__WORK__","`work`.id=exam_work.work_id","left")
//            ->join("__WORK_TYPE__","work.work_type_id=work_type.id")
//            ->join("__EXAM_WORK_LEVEL__","exam_work_level.exam_work_id=exam_work.id AND exam_work_level.alltype_id=jury.id AND exam_work_level.type=3","left")
//            ->where($where)
//            ->field("jury.*,jury.area as address_code,exam_work.id as eid,`work`.id as wid,`work`.`name` as wname,exam_work_level.id as ewlid,exam_work_level.work_level,work_type.id as wort_type_id")
//            ->select();
//    }
//    
    /**
     * [allotExam 机构分配考评人员]
     * @return [type] [description]
     */
    public function allotExam($where)
    {
        $field = 'jc.id,j.name,jc.card_no,jc.hire_time,jc.expire_date,j.phone,id_number,jc.card_level';
        $join = array(
            ['__JURY_CERTIFICATE__ jc', 'j.id=jc.jury_id'],
            ['__WORK__ w', 'w.id=jc.work_id']

        );

        return $this->BaseJoinSelect('j', $join, $where, [$field]);
    }

}