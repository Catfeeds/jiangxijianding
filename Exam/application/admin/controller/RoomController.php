<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/18
 * Time: 10:58
 */
namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\common\service\ExamRoom;
use app\common\service\ExamAddress;
use app\common\service\ExamPew;
use think\Request;

/**
 * 考务管理
 * Class ServingController
 * @package app\admin\controller
 */
class RoomController extends AdminBase
{
    private $exam_room;
    private $exam_address;
    private $exam_pew;

    /**
     * 初始化数据
     */
    public function __construct()
    {
        parent::__construct();
        $this->exam_room = new ExamRoom();
    }

    /**
     * 考场信息设置
     */
    public function index()
    {
        return view('\room\index');
    }
}