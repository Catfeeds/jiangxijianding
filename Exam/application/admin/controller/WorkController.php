<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/26
 * Time: 4:50 PM
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\Subject;
use app\common\service\Work;
use app\common\service\WorkType;
use app\common\service\WorkView;
use think\Request;

/**
 * 职业工种管理
 * Class Work
 * @package app\admin\controller
 */
class WorkController extends AdminBase
{


    /**
     * @var WorkView
     */
    private $SWorkView;
    /**
     * @var Work
     */
    private $SWork;
    /**
     * @var WorkType
     */
    private $SWorkType;

    /**
     * @var Subject
     */
    private $SSubject;

    /**
     * Work constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SWorkView = new WorkView();
        $this->SWork = new Work();
        $this->SWorkType = new WorkType();
        $this->SSubject = new Subject();
    }


    /**
     * 职业工种管理 首页
     * @return \think\response\View
     * @user 李海江 2018/9/27~4:10 PM
     */
    public function index()
    {
        $param = Request::instance()->param();
        $map = searchLike($param);
        //分页条数
        $arrayData = $this->SWork->getAll($param, $map);
        return view('', ['list' => $arrayData, 'param' => $param]);
    }


    /**
     * 考评人员选择工种
     * @return \think\response\View
     * @user 李海江 2018/12/5~6:27 PM
     */
    public function jurylist()
    {
        $param = Request::instance()->param();
        $map = searchLike($param);
        //分页条数
        $arrayData = $this->SWork->getAll($param, $map);
        return view('', ['list' => $arrayData, 'param' => $param]);
    }


    /**
     * 编辑工种信息
     * @return \think\response\View
     * @user 李海江 2018/9/28~4:49 PM
     */
    public function editWork()
    {
        $id = Request::instance()->route('id');
        if (!empty($id)) {
            //当前的工种信息 + 方向 + 类型
            $arrayRes = $this->SWork->showOne($id);
            $this->assign('res', $arrayRes);
        }
        //所有工种类型
        $arrayWorkType = $this->SWorkType->showall();
        return view('edit', ['type' => $arrayWorkType]);

    }


    /**
     * 查看工种的详情
     * @return \think\response\View
     * @user 李海江 2018/10/8~10:32 AM
     */
    public function show()
    {
        $id = Request::instance()->route('id');
        //当前的工种信息 + 方向 + 类型
        $arrayRes = $this->SWork->showOne($id);
        //渲染
        return view('', ['res' => $arrayRes]);
    }


}