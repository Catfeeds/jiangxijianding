<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/31
 * Time: 9:42 AM
 */

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\ExamCenter;
use app\common\service\Office;
use think\Request;

/**
 * Class OfficeController
 * @package app\api\controller
 */
class OfficeController extends BaseApi
{
    /**
     * @var ExamCenter
     */
    private $SExamCenter;
    /**
     * @var Office
     */
    private $SOffice;

    /**
     * OfficeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExamCenter = new ExamCenter();
        $this->SOffice = new Office();
    }

    /**
     * 市列表
     * @return \think\response\View
     * @user 李海江 2018/10/29~1:37 PM
     */
    public function cityList()
    {
        //获取市的条件
        $map = array('type' => 2, 'pid' => 0, 'status' => 1);
        //查询
        $list = $this->SExamCenter->centerSiteAll($map,['id','name']);
        return $list;
    }

    /**
     * 县列表
     * @return \think\response\View
     * @user 李海江 2018/10/29~1:37 PM
     */
    public function countyList()
    {
        $pid = Request::instance()->post('cityId');
        //获取市的条件
        $map = array('type' => 3, 'pid' => $pid, 'status' => 1);
        //查询
        $list = $this->SExamCenter->centerSiteAll($map);
        return $list;
    }

    /**
     * @return array
     * @user 李海江 2018/11/9~11:40 AM
     */
    public function add()
    {
        $webData = Request::instance()->post();
        $res = $this->SOffice->add($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * @return array
     * @user 李海江 2018/11/9~11:40 AM
     */
    public function delete()
    {
        $id = Request::instance()->post('id');
        $res = $this->SOffice->del(['id' => $id]);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * @return array
     * @user 李海江 2018/11/9~11:40 AM
     */
    public function edit()
    {
        $webData = Request::instance()->post();
        $res = $this->SOffice->edit($webData);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }
}