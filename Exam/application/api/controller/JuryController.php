<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\Jury as SJury;
use app\common\service\JuryReview;
use think\Request;

/**
 * Class JuryController
 * @package app\api\controller
 */
class JuryController extends BaseApi
{

    /**
     * @var SJury
     */
    private $SJury;
    /**
     * @var JuryReview
     */
    private $SJuryReview;

    /**
     * JuryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SJury = new SJury();
        $this->SJuryReview = new JuryReview();
    }


    /**
     * 查找是否有指定的username
     * @user 李海江 2018/11/8~11:46 AM
     */
    public function find()
    {
        $username = Request::instance()->post('username');
        $map = ['username' => $username];
        $this->SJury->getOne($map);
    }

    /**
     * 执行修改
     * @return array
     * @user 李海江 2018/11/8~6:52 PM
     */
    public function doedit()
    {
        $webData = Request::instance()->only(['id', 'name', 'id_number', 'phone', 'status', 'organize_id'], 'post');
        //验证传来的几个是否符合规则
        $result = $this->validate($webData, 'Jury.edit');
        if (true !== $result) return layuiMsg(0, $result);
        //验证身份证
        $isIdCardValid = validation_filter_id_card($webData['id_number']);
        if (!$isIdCardValid) return layuiMsg(0, '身份证号码格式有误');
        //添加
        $res = $this->SJury->edit($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * [allot 分配考评员申请]
     * @return [type] [description]
     */
    public function allot()
    {
        $data = Request::instance()->post();
        $arrAdmin = session("organizeuser");
        $insertData = [];
        foreach ($data['bs_id'] as $key => $val) {
            $insertData[$key]['jury_id'] = $val;
            $insertData[$key]['exam_plan_id'] = $data['plan'];
            $insertData[$key]['level'] = $data['level'];
            $insertData[$key]['work_id'] = $data['work'];
            $insertData[$key]['organize_id'] = $arrAdmin['id'];

        }
        $res = $this->SJuryReview->BaseSaveAll($insertData);
        if ($res) {
            return layuiMsg(1, '申请成功');
        } else {
            return layuiMsg(-1, '提交失败');
        }
    }

    /**
     * 添加考评人员
     * @return array
     * @user 李海江 2018/12/6~4:26 PM
     */
    public function add()
    {
        $webData = Request::instance()->post();
        //验证传来的几个是否符合规则
        $result = $this->validate($webData, 'Jury.add');
        if (true !== $result) return layuiMsg(0, $result);
        //验证身份证
        $isIdCardValid = validation_filter_id_card($webData['id_number']);
        if (!$isIdCardValid) return layuiMsg(0, '身份证号码格式有误');
        //验证账号是否存在
        $count = $this->SJury->getOne(['phone' => $webData['phone']]);
        if (!empty($count)) return layuiMsg(0, '该手机号码已存在');
        //添加
        $res = $this->SJury->add($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }
}