<?php
namespace app\examinee\controller;
use app\common\controller\CertinquireBase;
use think\Request;
use app\common\service\Certificate;

class CertinquireController extends CertinquireBase
{
    protected $SCertificate;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this -> SCertificate = new Certificate();
    }

    //证书管理首页
    public function index()
    {
        $data = session('user');
        foreach( $data as $k=>$v ) {
            if( !$v ){
                unset( $data[$k] );
            }
        }
        $data = $this -> SCertificate -> BaseSelect($data);
        return view('',['datas' =>$data]);
    }


}