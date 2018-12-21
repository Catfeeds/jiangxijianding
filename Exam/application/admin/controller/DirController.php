<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/8
 * Time: 6:49 PM
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Work;
use app\common\service\WorkDirection;
use think\Request;

/**
 * 登录控制器
 * Class LoginController
 * @package app\admin\controller
 */
class DirController extends AdminBase
{

    /**
     * @var Work
     */
    private $SWorkDirection;
    /**
     * @var
     */
    private $SWork;

    /**
     * DirController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SWorkDirection = new WorkDirection();
        $this->SWork = new Work();
    }

    /**
     * 方向主页
     * @user 李海江 2018/10/9~9:00 AM
     */
    public function index()
    {
        $param = Request::instance()->param();
        $map = searchLike($param);
        $arrayData = $this->SWorkDirection->getAll($param, $map);
        return view('', ['list' => $arrayData, 'param' => $param]);
    }

    /**
     * 展示一个工种下的所有方向 iframe弹
     * @return \think\response\View
     * @user 李海江 2018/10/12~9:32 AM
     */
    public function show()
    {
        $param     = Request::instance()->param();
        $map       = searchLike($param,'work_id');
        $arrayData = $this->SWorkDirection->getAll($param, $map);
        return view('', ['list' => $arrayData, 'param' => $param]);
    }

    /**
     * 编辑工种方向
     * @return \think\response\View
     * @user 李海江 2018/10/9~1:45 PM
     */
    public function edit()
    {
        $id = Request::instance()->route('id');
        $arrayRes = $this->SWorkDirection->show($id);
        //所有的工种 待修改时使用
        $arrayWorks = $this->SWork->getAlls(['status' => 1]);
        return view('', ['res' => $arrayRes, 'works' => $arrayWorks]);
    }

    /**
     * 添加工种
     * @return \think\response\View
     * @user 李海江 2018/10/12~9:33 AM
     */
    public function add()
    {

        //所有工种
        $arrayList = $this->SWork->getAlls(['status' => 1]);
        return view('',['list'=>$arrayList]);
    }
}
