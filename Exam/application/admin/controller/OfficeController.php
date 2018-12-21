<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/29
 * Time: 3:46 PM
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\ExamCenter;
use app\common\service\Office;
use think\Request;

/**
 * Class OfficeController
 * @package app\admin\controller
 */
class OfficeController extends AdminBase
{
    /**
     * @var Office
     */
    private $SRole;
    /**
     * @var ExamCenter
     */
    private $SExamCenter;
    /**
     * @var Office
     */
    private $SOffice;
    /**
     * @var
     */
    private $centerType;
    /**
     * OfficeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SRole = new Office();
        $this->SExamCenter = new ExamCenter();
        $this->SOffice = new Office();
        $this->centerType = getCenterType();
    }

    /**
     * 科室管理入口
     * @user 李海江 2018/10/30~10:59 AM
     */
    public function attribute()
    {
        $centerType = $this->centerType;
        $centerId = getCenterId();
        if ($centerType == 1) $this->redirect('office/province');
        if ($centerType == 2) $this->redirect('office/city', ['cityId' => $centerId]);
        if ($centerType == 3) $this->redirect('office/county', ['countyId' => $centerId]);
    }

    /**
     * 省当前
     * @return \think\response\View
     * @user 李海江 2018/10/30~10:59 AM
     */
    public function province()
    {
        $pid = getCenterId();
        $map = ['center_id' => $pid];
        //搜索条件构造
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        //查询
        $arrayList = $this->SOffice->getAll($map);
        return view('province', ['list' => $arrayList, 'cityId' => $pid]);
    }

    /**
     * 市当前
     * @return \think\response\View
     * @user 李海江 2018/10/29~1:37 PM
     */
    public function city()
    {
        //获得所选市id
        $pid = Request::instance()->route('cityId');
        $flag = $this->centerType;
        //如果是市自己 , 获得中心id
        if (getCenterType() == 2) $pid = getCenterId();
        //查询数据
        $map = array('center_id' => $pid);
        //搜索条件构造
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);
        $list  = $this->SOffice->getAll($map);
        //渲染页面
        return view('citycurrent', ['list' => $list, 'cityId' => $pid, 'flag' => $flag]);
    }


    /**
     * 县当前
     * @return \think\response\View
     * @user 李海江 2018/10/30~10:59 AM
     */
    public function county()
    {
        $pid = Request::instance()->route('cityId');
        $flag = $this->centerType;
        if (getCenterType() == 3) $pid = getCenterId();
        $map = array('center_id' => $pid);
        //搜索条件构造
        $param = searchLike(request()->post());
        $map   = array_merge($map, $param);

        $list = $this->SOffice->getAll($map);
        return view('countycurrent', ['list' => $list, 'cityId' => $pid, 'flag' => $flag]);
    }

    /**
     * @return \think\response\View
     * @user 李海江 2018/11/9~11:10 AM
     */
    public function add()
    {
        $center_id = Request::instance()->route('center_id');
        return view('', ['center_id' => $center_id]);
    }

    /**
     * 修改科室信息
     * @return \think\response\View
     * @user 李海江 2018/11/9~11:10 AM
     */
    public function edit()
    {
        $id = Request::instance()->route('id');
        $map = ['id' => $id];
        $res = $this->SOffice->getOne($map);
        return view('', ['res' => $res]);
    }
}