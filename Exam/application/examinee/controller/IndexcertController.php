<?php
namespace app\examinee\controller;
use app\common\controller\Base;
use think\Request;
use app\common\service\Work;

class IndexcertController extends Base
{
    /**
     * @var Work
     */
    protected $SWork;

    /**
     * IndexcertController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SWork = new Work();
    }

    /**
     * 证书查询 登录页
     * @return \think\response\View
     * @user xuweiqi
     */
    public function index()
    {
        return view();
    }

    /**
     * 成绩查询 登录页
     * @return \think\response\View
     * @user xuweiqi
     */
    public function indexgrade(){
        return view();
    }

    /**
     * 准考证查询
     * @return \think\response\View
     * @user xuweiqi
     */
    public function indexticket(){
        return view();
    }

    /**
     * 条件查询
     * @return \think\response\View
     * @user xuweiqi
     */
    public function indexcondition(){
        $map = ['status' => 1];
        $list = $this->SWork->getAlls($map, ['id', 'code', 'name']);
        return view('',['list'=>$list]);
    }


}
?>

