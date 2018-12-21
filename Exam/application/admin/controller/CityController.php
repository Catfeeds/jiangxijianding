<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/29
 * Time: 10:10 AM
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\ExamCenter;
use think\Request;

/**
 * 鉴定中心-市管理
 * Class CityController
 * @package app\admin\controller
 */
class CityController extends AdminBase
{
    /**
     * @var ExamCenter
     */
    private $SExamCenter;

    /**
     * CityController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExamCenter = new ExamCenter();
    }

    /**
     * 市列表
     * @return \think\response\View
     * @user 李海江 2018/10/29~1:37 PM
     */
    public function index()
    {
        //获取市的条件
        $map   = array('type' => 2, 'pid' => 0);
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        //查询
        $list = $this->SExamCenter->centerSiteAll($map);
        return view('index', ['list' => $list]);
    }

    /**
     * 市信息修改
     * @return \think\response\View
     * @user 李海江 2018/11/22~4:13 PM
     */
    public function edit()
    {
        $id = Request::instance()->route('id');
        $res = $this->SExamCenter->getOne(['id' => $id]);
        return view('', ['res' => $res]);
    }
}