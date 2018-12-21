<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/25
 * Time: 13:43
 */

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\ExamPlan;
use app\common\service\ExamStaffLog;
use app\common\service\Instructor;
use think\Request;


/**
 * Class InstructorController
 * @package app\admin\controller
 */
class InstructorController extends AdminBase
{
    /**
     * @var ExamPlan
     */
    private $SexamPlan;
    /**
     * @var Instructor
     */
    private $SInstructor;
    /**
     * @var ExamStaffLog
     */
    private $SExamStaffLog;

    /**
     * InstructorController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SexamPlan = new ExamPlan();
        $this->SInstructor = new Instructor();
        $this->SExamStaffLog = new ExamStaffLog();

    }

    /**
     * @return \think\response\View
     * @user 李海江 2018/11/12~5:53 PM
     */
    public function index()
    {
        $field = ['id', 'name', 'id_number', 'phone', 'create_time', 'status'];
        $param = searchLike(request()->param());
        $list  = $this->SInstructor->getAll($param, $field);
        return view('', ['list' => $list]);
    }


    /**
     * 添加督导员
     * @return \think\response\View
     * @user 李海江 2018/11/27~11:24 AM
     */
    public function add()
    {
        return view();
    }

    /**
     * 修改督导员信息
     * @return \think\response\View
     * @user 李海江 2018/11/12~6:35 PM
     */
    public function edit()
    {
        $id = Request::instance()->route('id');
        $map = ['id' => $id];
        $field = ['id', 'name', 'id_number', 'phone', 'status'];
        $res = $this->SInstructor->getOne($map, $field);
        return view('', ['res' => $res]);
    }

}