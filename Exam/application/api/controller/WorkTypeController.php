<?php
namespace app\api\controller;

use app\common\service\WorkType;
use app\common\controller\BaseApi;
use think\Request;

/**
 * Class WorkTypeController
 * @package app\api\controller
 */
class WorkTypeController extends BaseApi{

    /**
     * @var WorkType
     */
    private $SWorkType;

    /**
     * WorkTypeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SWorkType = new WorkType();
    }


    public function add()
    {

    }


    /**
     * 删除工种分类
     * @return array
     * @user 李海江 2018/10/10~7:09 PM
     */
    public function delete()
    {
        $id = Request::instance()->post('id');
        $intResult = $this->SWorkType->deleteWorkType(['id' => $id]);
        if ($intResult) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }
    }

}