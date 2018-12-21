<?php
namespace app\api\controller;
use app\common\model\Userinfo;
use app\common\service\UserLogin;
use app\common\controller\BaseApi;
use think\captcha\Captcha;
use think\Request;
use SendMsm\Msm;
use think\Validate;
use app\common\validate\Sign;
use think\Cache;
use  app\common\service\Certificate;
use app\common\service\ExamEnroll;

class UserLoginController extends BaseApi{

    /**
     * @var UserLogin
     */
    private $SUserLogin;
    /**
     * @var Userinfo
     */
    private $SUserinfo;
    /**
     * @var
     */
    private $SCertificate;
    private $SExamEnroll;

    /**
     * UserLoginController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SUserLogin = new UserLogin();
        $this->SUserinfo = new Userinfo();
        $this->SCertificate = new Certificate();
        $this->SExamEnroll = new ExamEnroll();
    }
    public function add()
    {

    }

    public function delete()
    {

    }

    public function update()

    {

    }

    /***
     * 找回密码 发送短信验证码
     */
    public function sendMessage(Request $request)
    {
        $mobile = $request->post();
        //数据验证
//        $validate = Validate('ExamEnroll');
//        if(!$validate->scene('sendmessage')->check($mobile)){
//            return layuiMsg(0, $validate->getError());
//        }
//        die;
        $result = $this->validate($mobile, 'ExamEnroll.sendmessage');
        if (true !== $result) {
            return layuiMsg(0, $result);
        }; 
        $res = Msm::sendMessage($mobile['mobile'],$templateNum=[]);
        if (!$res['flag']) {
            return layuiMsg(0,$res['message']);
        } else {
            return layuiMsg(1,'发送成功');
        }
    }
    
    /**checkSms 找回密码手机短信验证
     * @param Request $request
     * @return array
     * @user xuweiqi
     */
    public function checkSms(Request $request)
    {
        $data = $request->post();
        $code = Cache::get($data['mobile']);
        if($code != $data['code'])
        {
            return layuiMsg(0,'验证码错误');
        } else {
            return layuiMsg(1,'',['mobile'=>$data['mobile']]);
        }
    }

    public function updatePass(Request $request)
    {
        $data = $request->post();
        $updateData['password'] = md5($data['newPass'].config('salt'));
        $res = $this->SUserLogin->BaseUpdate($updateData,['mobile'=>$data['mobile']]);
        if ( $res == true )
        {
              return layuiMsg(1,"更新成功");
        } else {
                return layuiMsg(-1,'更新失败');
        }
    }


    /**
     * 找回密码
     * @user xuweiqi 2018/10/11
     */
    public function forgetPassAction()
    {
        $data = Request::instance()->post();
        $validata = Validate('ExamEnroll');
        //场景应用
        if (!$validata->scene('forgetPass')->check($data)) {
            $arrMsg['status'] = -1;
            $arrMsg['msg'] = $validata->getError();
            return $arrMsg;
        }
        if (!preg_match('/^[1][3,4,5,6,7,8,9][0-9]{9}$/', $data['mobile'])) {
            echo '手机号码格式错误,请重新输入！';
            die;
        }
        //验证码
        $code = $data['yzm'];
        if ($code != \think\Cache::get($data['mobile']) || $code == null) {
            echo '短信验证码错误,请重新输入！';
            die;
        } else {
            //当验证码验证通过后从缓存中清除code
            \think\Cache::rm($data['mobile']);
        }
        $phonedata = $intRes = $this->SUserLogin->BaseFind($data['mobile']);;
        //响应
        if ($phonedata) {
           return $phonedata;
        } else {
            return '';
        }
    }


    //检查用户  注册/登录
    public function checkUser(){
        //接收表单值
        $data=input('post.');
        //查询条件 判断是否存在已注册手机号
        $dataFind= ['mobile'=>$data['mobile']];
        //从数据库根据条件查询
        $count=$this->SUserLogin->BaseSelectCount($dataFind);
        //结果为空返回0(没有注册)  不为空返回1(有数据已注册)
        if($count>0){
            return layuiMsg(-1,'手机号已注册');
        }
    }

    //用户找回密码 验证手机号
    public function checkPhone()
    {
        $data=input('post.');
        //查询条件 判断是否存在已注册手机号
        $dataFind= ['mobile'=>$data['mobile']];
        //从数据库根据条件查询
        $UserLoginRes=$this->SUserLogin->BaseFind($dataFind);
        //结果为空返回0(没有注册)  不为空返回1(有数据已注册)
        if(empty($UserLoginRes)){
            return layuiMsg(-1,'手机号不存在');
        }
    }

    /**
     * 注册发送验证码
     * @return array
     * @user xuweiqi 2018/12/7~5:25 PM
     */
    public function sendMsg()
    {
        //查看附加码是否正确 代码的手机号 和手机号是否一致
        $webData = Request::instance()->only([ 'mobile'], 'post');
        $count=$this->SUserLogin->BaseSelectCount(['mobile' => $webData['mobile']]);
        //结果为空返回0(没有注册)  不为空返回1(有数据已注册)
        if($count>0){
            return layuiMsg(-1,'手机号已注册');
        }

        //如果正正确发送验证码
        $res = Msm::sendMessage($webData['mobile'], 0);

        if ($res['flag']) {
            return layuiMsg(1, $res['msg']);
        } else {
            return layuiMsg(-4, $res['msg']);
        }
    }


    //修改密码
    public function my_center()
    {
        if (Request::instance()->isPost()) {
            $data = Request::instance()->param();
            $validata = Validate('User');

            //场景应用
            if (!$validata->scene('updatepwd')->check($data)) {
                return layuiMsg(-1,$validata->getError());
            }
            if ($data['password'] !== $data['passwordRes'])
            {
                return layuiMsg(-1,"两次密码输入不一致");
            }
            $map = session('user')['username'];
            $res = $this->SUserLogin->BaseUpdate(['password' =>md5($data['password'])], ['username' => $map]);
            if ($res) {
                return layuiMsg(1,"更新成功");
            } else {
                return layuiMsg(-1,'更新失败');
            }
        }else{
            return layuiMsg(-1,'请求失败');
        }
    }


    //    考生登录
    public function loginAction()
    {
        if (Request::instance()->isPost()) {
//            $objValidate = new Validate();
            $arrData = input('post.');
//            dump($arrData);die;
            if(!captcha_check($arrData['yzm'])){
                return layuiMsg(-2,'验证码错误!');
            }
            $dataType=$arrData['id_type'];
            //数据验证
            //场景应用
            $validate = Validate('Sign');
            if ($dataType=='1'){
                if(!$validate->scene('entersf')->check($arrData)){
                    return layuiMsg(-1, $validate->getError());
                }
            }else if($dataType=='2'){
                if(!$validate->scene('enterhz')->check($arrData)){
                    return layuiMsg(-1, $validate->getError());
                }
            }else if($dataType=='3'){
                if(!$validate->scene('enterjg')->check($arrData)){
                    return layuiMsg(-1, $validate->getError());
                }
            }else if($dataType=='4'){
                if(!$validate->scene('enterga')->check($arrData)){
                    return layuiMsg(-1, $validate->getError());
                }
            }

            $map['id_card'] = $arrData['id_card'];
            $map['password'] = md5($arrData['password'].config('salt'));
            $map['id_type'] = $arrData['id_type'];

            $data = $this->SUserLogin->BaseFind($map);
            $map = array(
                'type' => $arrData['id_type'],
                'id_no' =>  $arrData['id_card'],
            );
            $examJoinData = $this->SCertificate->selCertData($map);
            $countCert = count($examJoinData);

            $indexmap['status'] = [['=',50],['=',55],'or'];
            $indexmap['user_login_id'] = $data['id'];
            $examJoinData = $this->SExamEnroll->BaseSelect($indexmap);
            if (empty($data)) {
                return layuiMsg(-1,"账号或密码错误");
            } else {
                $sdata = [
                    'id' => $data['id'],
                    'id_card' => $data['id_card'],
                    'mobile' => $data['mobile'],
                    'id_type' => $data['id_type'],
                    'username' => $data->info['username'],
                    'countcert' =>$countCert,
                    'examJoinData'=>count($examJoinData),
                ];
                session('user', $sdata);
                return layuiMsg(1,"登录成功");
            }
        } else {
            return layuiMsg(-1,"请求失败");
        }
    }


    //注册方法
    public  function sign(){
        if($this->request->isPost()){
            $data=input('post.');
            //验证短信验证码
            $res = check_code($data['sms'], $data['mobile']);
            if (!$res) return layuiMsg(0, '短信验证码错误');

            unset($data['signature']);
            unset($data['passwordRes']);
            unset($data['sms']);

            //加载验证器
            //使用验证器中的sign验证方法
            $validate = Validate('Sign');
            $dataType=$data['id_type'];
            if ($dataType=='1'){
                if(!$validate->scene('sf')->check($data)){
                    return layuiMsg(-1,$validate->getError());
                }
            }else if($dataType=='2'){
                if(!$validate->scene('hz')->check($data)){
                    return layuiMsg(-1,$validate->getError());
                }
            }else if($dataType=='3'){
                if(!$validate->scene('jg')->check($data)){
                    return layuiMsg(-1,$validate->getError());
                }
            }else if($dataType=='4'){
                if(!$validate->scene('ga')->check($data)){
                    return layuiMsg(-1,$validate->getError());
                }
            }
            $mapLogin = [
                'mobile' =>$data['mobile'],
                'password' =>  md5($data['password'].config('salt')),
                'id_type' => $dataType,
                'id_card'=> $data['id_no'],
                'login_time' =>  date('Y-m-d H:i:s',time()),
                'reg_type' => 1,
                'login_ip' => $this->request->ip(),
                'status' => 1,
            ];
            $mapTime = [
                'create_time' => time(),
                'update_time' => time(),
            ];

            $mapInfo = [
                'gender' =>  $data['gender'],
                'birthday' =>  $data['birthday'],
                'mobile' =>  $data['mobile'],
                'username' =>$data['username']
            ];
            $fieldTypeData = $this->SUserLogin->show(['id_type'=>$data['id_type'],'id_card'=>$data['id_no']]);
            if(!empty($fieldTypeData)){
                return layuiMsg(-1,"证件号已注册!");
            }
            $fieldData = $this->SUserLogin->show(['mobile'=>$data['mobile']]);
            if(!empty($fieldData)){
                return layuiMsg(-1,"手机号已注册!");
            }
            $dataRes=$this->SUserLogin->addLogin(array_merge($mapLogin,$mapTime),array_merge($mapInfo,$mapTime));
            if($dataRes==true){
                return layuiMsg(1,"注册成功,请登录");
            }else{
                return layuiMsg(-1,"注册失败,请重新注册!");
            }
        }else{
            return layuiMsg(-1,"请求失败!");
        }
    }


    //退出登录
    public function loginOut()
    {
        session('user', null);
        return 1;
    }









}