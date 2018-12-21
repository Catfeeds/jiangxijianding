<?php
namespace app\learning\controller;
use think\Controller;
use think\Request;
use app\common\service\LearningAudit;
use think\Db;

class JuryController extends Controller
{
    private $SAudit;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->SAudit = new LearningAudit();
    }

    public function index()
    {
        $request = Request::instance()->post();

        $where = [];
        if (!empty($request['name'])) {
            $where['name'] = ['like', '%' . trim($request['name']) . '%'];
        }
        if (!empty($request['certificate'])) {
            $where['certificate'] = trim($request['certificate']);
        }
        if (!empty($request['state'])) {
            $where['state'] = trim($request['state']);
        }

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );

        $data = $this->SAudit->BaseSelectPage($paginate,$where,'',"id desc");

        return view('jury/index', ['data'=>$data, 'search'=>$request]);
    }



}