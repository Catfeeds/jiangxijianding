<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/30
 * Time: 4:29 PM
 */
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\service\WorkType;

/**
 * Class TypeController
 * @package app\admin\controller
 */
class TypeController extends AdminBase
{
    /**
     * @var WorkType
     */
    private $SWorkType;
    /**
     * TypeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SWorkType = new WorkType();
    }

    /**
     * 工种管理首页
     * @return \think\response\View
     * @user 李海江 2018/9/30~4:34 PM
     */
    public function index()
    {
        $arrayData = $this->SWorkType->showall();
        return view('',['list'=>$arrayData]);
    }
}