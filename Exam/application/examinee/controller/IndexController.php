<?php
namespace app\examinee\controller;
use app\common\controller\Examineebase;
use app\common\controller\Base;
use think\Request;
use app\common\service\Userinfo;

class IndexController extends Base
{
    protected $SUserinfo;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SUserinfo=new Userinfo();
    }


    public function login()
    {
        return view();
    }

    public function sign()
    {
        $signature = $this->request->param();
        if(empty($signature)){
            return $this->redirect('/examinee/index/signature');
        }else{
            return view('',['signature' => $signature['name']]);
        }
    }


  //忘记密码
    public function forgetPass()
    {
        return view('');
    }


    public function my_center()
    {
        return view();
    }

    public function signature(){
        return view();
    }

    public function forgetPasstwo(){
        $mobile = input('get.mobile');
        return view('',['mobile'=>$mobile]);
    }

    public function forgetPassthree(){
        return view();
    }

}
?>

