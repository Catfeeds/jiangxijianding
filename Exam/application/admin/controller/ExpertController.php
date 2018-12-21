<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/8
 * Time: 9:42 AM
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Expert;
use app\common\service\ExpertWorkLevel;
use think\Request;

/**
 * 专家
 * Class ExpertController
 * @package app\admin\controller
 */
class ExpertController extends AdminBase
{

    /**
     * @var Expert
     */
    private $SExpert;
    /**
     * @var ExpertWorkLevel
     */
    private $SExpertWorkLevel;

    /**
     * ExpertController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExpert = new Expert();
        $this->SExpertWorkLevel = new ExpertWorkLevel();
    }

    /**
     * 专家首页
     * @return \think\response\View
     * @user 李海江 2018/11/8~11:54 AM
     */
    public function index()
    {
        $param = searchLike(request()->param());
        $field = ['id', 'username', 'name', 'id_number', 'phone', 'hire_time', 'create_time', 'status'];
        $list  = $this->SExpert->getAll($param, $field);
        return view('', ['list' => $list]);
    }

    /**
     * 添加
     * @return \think\response\View
     * @user 李海江 2018/11/28~9:54 AM
     */
    public function add()
    {
        return view();
    }

    /**
     * 修改
     * @return \think\response\View
     * @user 李海江 2018/11/8~5:04 PM
     */
    public function edit()
    {
        $id = Request::instance()->route('id');
        $map = ['id' => $id];
        $field = ['id', 'username', 'name', 'id_number', 'phone', 'hire_time', 'status'];
        $res = $this->SExpert->getOne($map, $field);
        return view('', ['res' => $res]);
    }

    /**
     * 专家操作的权限
     * @return \think\response\View
     * @user 李海江 2018/11/12~4:54 PM
     */
    public function manage()
    {

        $id = Request::instance()->route('id');
        $map = ['expert_id' => $id];
        $field = ['*', 'expert.status'];
        $list = $this->SExpertWorkLevel->getAll($map, $field);
        return view('', ['list' => $list]);
    }


    /**
     * 修改专家权限
     * @return \think\response\View
     * @user 李海江 2018/11/28~2:50 PM
     */
    public function editManage()
    {
        $id = Request::instance()->route('id');
        $arrayList = $this->SExpertWorkLevel->getOne(['expert.id' => $id]);

        return view('editmanage', ['res' => $arrayList]);
    }

}