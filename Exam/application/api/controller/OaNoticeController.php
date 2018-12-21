<?php
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\OaNotice;
use app\common\service\OaSend;
use think\Request;

class OaNoticeController extends BaseApi
{
    protected $SOaNotice;
    protected $SOaSend;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SOaNotice = new OaNotice();
        $this->SOaSend = new OaSend();
    }
    public function delete(Request $request)
    {
       $id = $request->param('id');
       $res = $this->SOaNotice->destroy(['id'=>$id]);

       if($res)
       {
           $this->SOaSend->BaseUpdate(['status'=>3],['notice_id'=>$id]);
           return layuiMsg(1,'删除成功');
       }
       return layuiMsg(0,'删除失败');
    }
    public function update(Request $request)
    {

    }
}