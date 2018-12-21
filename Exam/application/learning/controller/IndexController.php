<?php
namespace app\learning\controller;
use think\Controller;
use think\Request;
use app\common\model\Userinfo;
use app\common\model\UserLogin;

class IndexController extends Controller
{
    protected $UserLogin;
    protected $Userinfo;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->UserLogin=new UserLogin();
        $this->Userinfo=new Userinfo();
    }

    public function index(){
        return view();
    }


    public function login()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $dataType=$arrData['id_type'];
            //数据验证
            //场景应用
            $validate = Validate('Sign');
            if ($dataType=='1'){
                if(!$validate->scene('entersf')->check($arrData)){
                    $statusMsg['status'] = -1;
                    $statusMsg['msg'] = $validate->getError();
                    return $statusMsg;
                }
            }else if($dataType=='2'){
                if(!$validate->scene('enterhz')->check($arrData)){
                    $statusMsg['status'] = -1;
                    $statusMsg['msg'] = $validate->getError();
                    return $statusMsg;
                }
            }else if($dataType=='3'){
                if(!$validate->scene('enterjg')->check($arrData)){
                    $statusMsg['status'] = -1;
                    $statusMsg['msg'] = $validate->getError();
                    return $statusMsg;
                }
            }else if($dataType=='4'){
                if(!$validate->scene('enterga')->check($arrData)){
                    $statusMsg['status'] = -1;
                    $statusMsg['msg'] = $validate->getError();
                    return $statusMsg;
                }
            }

            $map['username'] = $arrData['username'];
            $map['password'] = md5($arrData['password']);
            $map['id_type'] = $arrData['id_type'];

            $data = $this->UserLogin->BaseFind($map);
            if (empty($data)) {
                $statusMsg['status'] = -1;
                $statusMsg['msg'] = "账号或密码错误";
                //账号或密码错误
                return $statusMsg;
            } else {
                $sdata = [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'mobile' => $data['mobile'],
                ];
                session('user', $sdata);
                $statusMsg['status'] = 1;
                $statusMsg['msg'] = "登录成功";
                return $statusMsg;
            }
        } else {
            return view('login');
        }
    }


    public function loginOut()
    {
        session('user', null);
        $this->redirect('/learning/index/login');
    }

    public function my_center()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
//            dump($data);die;
            $validata = Validate('User');

            //场景应用
            if (!$validata->scene('updatepwd')->check($data)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = $validata->getError();
                return $arrMsg;
            }
            if ($data['password'] !== $data['passwordRes'])
            {
                $statusArray['status'] = -1;
                $statusArray['msg'] = "两次密码输入不一致";
                //两次密码输入不一致
                return $statusArray;
            }
            $map = session('user')['username'];
            $res = $this->UserLogin->BaseUpdate(['password' =>md5($data['password'])], ['username' => $map]);
            if ($res) {
                $statusArray['status'] = 1;
                $statusArray['msg'] = '更新成功';
                return $statusArray;
            } else {
                $statusArray['status'] = -1;
                $statusArray['msg'] = '更新失败';
                return $statusArray;
            }
        } else {
            return view();
        }
    }
}

