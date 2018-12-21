<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\WorkDirection;
use think\Request;

/**
 * Class WorkDirectionController
 * @package app\api\controller
 */
class WorkDirectionController extends BaseApi
{

    /**
     * @var WorkDirection
     */
    private $SWorkDirection;

    /**
     * WorkDirectionController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SWorkDirection = new WorkDirection();
    }

    /**
     * 编辑
     * @user 李海江 2018/10/9~4:31 PM
     */
    public function doedit()
    {
        $webData = Request::instance()->post();
        $intRes = $this->SWorkDirection->edit($webData, ['id' => $webData['id']]);
        return $intRes;
    }

    /**
     * 禁用
     * @user 李海江 2018/9/27~6:37 PM
     */
    public function disable()
    {
        $webData = Request::instance()->post();
        $res = $this->SWorkDirection->BaseUpdate(['status' => $webData['status']], ['id' => $webData['id']]);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }


    /**
     * 删除
     * @return array
     * @user 李海江 2018/10/9~7:42 PM
     */
    public function delete()
    {
        $id = Request::instance()->post('id');
        $intResult = $this->SWorkDirection->deleteWorkDir(['id' => $id]);
        if ($intResult) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }
    }

    public function doadd()
    {
        $webData = Request::instance()->post();
        isset($webData['status']) ?: $webData['status'] = -1;

        $data = array_insert($webData, 'name');
        $intResult = $this->SWorkDirection->doadd($data);
        if ($intResult) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }
    }

    /***
     * @return array
     * @user 李海江 2018/11/26~14:37
     */
    public function selectWorkId()
    {
        $webData = Request::instance()->post();
        $res = $this->SWorkDirection->BaseSelect(['work_id'=>$webData['id']]);
        if ($res) {
            return layuiMsg(1, '操作成功', $res);
        } else {
            return layuiMsg(0, '操作失败');
        }
    }
}