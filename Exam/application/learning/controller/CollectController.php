<?php

namespace app\learning\controller;

use think\Controller;
use think\Request;
use app\common\service\LearningCollect;

class CollectController extends Controller
{
    private $Scollect;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Scollect = new LearningCollect();
    }

    public function index()
    {
        $request = Request::instance()->post();

        $where = [];
        if (!empty($request['file_name'])) {
            $where['file_name'] = ['like', '%' . trim($request['file_name']) . '%'];
        }
        if (!empty($request['file_type'])) {
            $where['file_type'] = trim($request['file_type']);
        }

        $data = $this->Scollect->select($where);

        return view('collect/index', ['data' => $data]);
    }

}