<?php

namespace app\learning\controller;

use think\Controller;
use think\Request;
use app\common\service\LearningMedia;

class MediaController extends Controller
{
    private $Smedia;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Smedia = new LearningMedia();
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
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $res = $this->Smedia->BaseSelectPage($paginate,$where,'',"id desc");

        foreach ($res as $key => $value) {
            if ($value['file_type'] == 'PDF文件格式' || $value['file_type'] == 'Flash动画文件') {
                $res[$key]['file_address'] = $value['file_address'];
                $res[$key]['file_address'] = '/app/learning/file?file_url=' .$value['file_address'];
            } else {
                $res[$key]['file_address'] = 'http://view.officeapps.live.com/op/view.aspx?src=http://' .$_SERVER['SERVER_NAME'] . $value['file_address'];
            }
        }


        return view('media/index', ['data' => $res, 'search'=>$request]);
    }

    public function add()
    {
        return view();
    }

}