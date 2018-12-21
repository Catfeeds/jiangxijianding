<?php
namespace app\learning\controller;
use think\Controller;
use think\Request;
use app\common\service\LearningReport;

class ReportController extends Controller
{
    private $Sreport;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Sreport = new LearningReport();
    }

    public function index()
    {
        $request = Request::instance()->post();

        $where = [];
        if (!empty($request['user_ip'])) {
            $where['user_ip'] = ['like', '%' . trim($request['user_ip']) . '%'];
        }
        if (!empty($request['state'])) {
            $where['state'] = trim($request['state']);
        }

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );

        $data = $this->Sreport->BaseSelectPage($paginate,$where,'',"id desc");

        return view('/report/index',['data'=>$data, 'search'=>$request]);
    }

    public function detail()
    {
        $id = Request::instance()->param('id');

        $data = $this->Sreport->find(['id'=>$id]);

        return view('/report/detail',['datas'=>$data]);
    }
}