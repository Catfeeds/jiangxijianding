<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\Instructor as SInstructor;
use think\Request;
use Upload\Up;

/**
 * Class InstructorController
 * @package app\api\controller
 */
class InstructorController extends BaseApi
{

    /**
     * @var SInstructor
     */
    private $SInstructor;

    /**
     * InstructorController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SInstructor = new SInstructor();
    }


    /**
     * 查找是否有指定的username
     * @user 李海江 2018/11/8~11:46 AM
     */
    public function find()
    {
        $username = Request::instance()->post('username');
        $map      = ['username' => $username];
        $this->SInstructor->getOne($map);
    }

    /**
     * @return array
     * @user 李海江 2018/12/2~1:57 PM
     */
    public function doedit()
    {
        $webData = Request::instance()->only(['id', 'name', 'id_number', 'phone', 'status'], 'post');
        //验证传来的几个是否符合规则
        $result = $this->validate($webData, 'Instructor.edit');
        if (true !== $result) return layuiMsg(0, $result);
        //验证身份证
        $isIdCardValid = validation_filter_id_card($webData['id_number']);
        if (!$isIdCardValid) return layuiMsg(0, '身份证号码格式有误');
        //验证账号是否存在
        $count = $this->SInstructor->getOne(['phone' => $webData['phone'], 'id' => ['neq', $webData['id']]]);
        if (!empty($count)) return layuiMsg(0, '该手机号码已存在');
        //添加
        $res = $this->SInstructor->edit($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * @return array
     * @user 李海江 2018/12/2~1:57 PM
     */
    public function add()
    {
        $webData = Request::instance()->only(['name', 'id_number', 'phone', 'status'], 'post');
        //验证传来的几个是否符合规则
        $result = $this->validate($webData, 'Instructor.add');
        if (true !== $result) return layuiMsg(0, $result);
        //验证身份证
        $isIdCardValid = validation_filter_id_card($webData['id_number']);
        if (!$isIdCardValid) return layuiMsg(0, '身份证号码格式有误');
        //验证账号是否存在
        $count = $this->SInstructor->getOne(['phone' => $webData['phone']]);
        if (!empty($count)) return layuiMsg(0, '该手机号码已存在');
        //添加
        $res = $this->SInstructor->add($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    public function getReapet($data)
    {
        // 获取去掉重复数据的数组
        $unique_arr = array_unique($data);
        // 获取重复数据的数组
        $repeat_arr = array_diff_assoc($data, $unique_arr);
        return $repeat_arr;
    }

    /**
     * 批量导入
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\Exception
     * @user 李海江 2018/12/2~6:52 PM
     */
    public function import()
    {
        $up  = new Up();
        $res = $up->execl('file', config('instructor'));
        //得到上传表格的结果数据
        if ($res['flag']) {
            //循环组成数组
            $dataArr = array();
            $data    = $res['data'];
            //删除空数据
            foreach ($data as $k => $v) {
                foreach ($v as $key => $val) {
                    if (empty($val) or preg_match("/\s/", $val)) {
                        unset($data[$k][$key]);
                    }
                }
            }
            //从第二行开始
            $num = 2;
            //查找每行为空的数据
            foreach ($data as $index => $item) {
                if (empty($item)) {
                    unset($data[$index]);
                } else {
                    $hang = $index + $num;
                    if (!isset($item[0]) || !isset($item[1]) || !isset($item[2])) {
                        return layuiMsg(0, '第' . $hang . '行信息不全');
                    }
                }
            }
            $id_card     = array_column($data, 1);
            $execl_phone = array_column($data, 2);
            $card        = $this->getReapet($id_card);
            $phone       = $this->getReapet($execl_phone);
            //
            if (!empty($id_card)) return layuiMsg(0, '证件号[' . current($card) . ']重复');
            if (!empty($phone)) return layuiMsg(0, '手机号[' . current($phone) . ']重复');
            //与数据库比较
            foreach ($data as $k => $v) {
                $count = $this->SInstructor->getCount(['id_number' => $v[1]], ['phone' => $v[2]]);
                $hang  = $k + $num;
                if ($count > 0) {
                    return layuiMsg(0, '第 ' . $hang . ' 的督导员数据已经存在,请勿重复录入');
                } else {
                    $data[$k]['name']      = $v[0];
                    $data[$k]['id_number'] = $v[1];
                    $data[$k]['phone']     = $v[2];
                }
            }
            //如果数组内没有值
            if (empty($data)) return layuiMsg(0, '无新增督导员');
            //保存
            $num = $this->SInstructor->addAll($data);
            //返回
            if ($num > 0) {
                return layuiMsg($num, '上传成功');
            } else {
                return layuiMsg(0, '导入失败，请重试');
            }
        } else {
            return layuiMsg(0, $res['msg']);
        }
    }
}