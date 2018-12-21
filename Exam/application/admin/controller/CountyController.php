<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/29
 * Time: 1:34 PM
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\ExamCenter;
use think\Request;

/**
 * Class CountyController
 * @package app\admin\controller
 */
class CountyController extends AdminBase
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
     * 市列表列表
     * @return \think\response\View
     * @user 李海江 2018/11/22~2:52 PM
     */
    public function cityList()
    {
        //获取市的条件
        $map = array('type' => 2, 'pid' => 0);
        $list = $this->SExamCenter->centerSiteAll($map);
        return view('cityList', ['list' => $list]);
    }

    /**
     * 展示指定市下的县列表
     * @return \think\response\View
     * @user 李海江 2018/11/22~2:53 PM
     */
    public function current()
    {
        $this->assign('flag', true);
        $cityId = Request::instance()->route('cityId');
        //如果是省 需要先展示市列表
        if (getCenterType() == 1 && $cityId == null) $this->redirect('county/cityList');
        //如果是市 赋值自己的id
        if (getCenterType() == 2) {
            $cityId = getCenterId();
            $this->assign('flag', false);
        }
        //查询当前市的信息
        $cityName = $this->SExamCenter->getOne(['id' => $cityId]);
        //搜索条件构造
        $map   = array('pid' => $cityId, 'type' => 3);
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        //查询县的数据
        $arrayCountList = $this->SExamCenter->centerSiteAll($map);

        //渲染页面
        return view('current', ['list' => $arrayCountList, 'cityName' => $cityName['name']]);
    }

    /**
     * 县信息修改
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