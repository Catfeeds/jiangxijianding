<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/29
 * Time: 13:43
 */

namespace app\common\controller;
use think\Controller;
use app\common\service\Userinfo;


class Examineebase extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $no = ['examinee/Menu/index','examinee/Center/indexbase','examinee/Center/indexknow','examinee/center/my_center'];
         $url = request()->module().'/'.request()->controller().'/'.request()->action();
        $back = request()->url();
        $planurl ='/examinee/menu/index?id=';
        if(strpos($back,$planurl) !== false){
            $planid = substr($back,mb_strlen($planurl));
            cookie('examplan_id',$planid,['path']);
        }

        if (session('user')) {
            if (!in_array($url,$no)){
                $model = new Userinfo();
                $data = $model->findUserinfoData(['user_login_id'=> session('user')['id']]);
                if($data['native_place']==''||$data['email']==''||$data['zip_code']==''||$data['education']==''||$data['company']==''||$data['address']==''||$data['avatar']==''){
                        exit('error');
                }
            }
        }else{
            $this->redirect("/cms/index/studenlogin?back=".urlencode($back));
        }
    }
}