<?php

namespace app\learning\controller;

use think\Controller;
use think\Request;
use app\common\service\LearningRecord;

class RecordController extends Controller
{
    private $Srecord;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Srecord = new LearningRecord();
    }

    public function index()
    {
        $request = Request::instance()->post();

        $where = [];
        if (!empty($request['name'])) {
            $where['name'] = ['like', '%' . trim($request['name']) . '%'];
        }
        if (!empty($request['category'])) {
            $where['category'] = trim($request['category']);
        }
        if (!empty($request['level'])) {
            $where['level'] = trim($request['level']);
        }

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );

        $data = $this->Srecord->BaseSelectPage($paginate,$where,'',"id desc");

        return view('record/index', ['data' => $data, 'search'=>$request]);
    }

}