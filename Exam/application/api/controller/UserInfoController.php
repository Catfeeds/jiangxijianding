<?php
namespace app\api\controller;
use app\common\service\Userinfo;
use app\common\controller\BaseApi;
use think\Request;

class UserInfoController extends BaseApi
{

    /**
     * @var Userinfo
     */
    private $SUserInfo;

    /**
     * UserInfoController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SUserInfo = new UserInfo();
    }


//完善基本信息页面
    public function updataInfo(Request $request)
    {
        //获取当前考生ID
        $data['user_login_id'] = session('user')['id'];
        $dataForm = $request->param();
        unset($dataForm['file']);
        $userinforesult = $this->SUserInfo->updateUserinfoData($dataForm,['user_login_id'=>session('user')['id']]);
        if ($userinforesult == true) {
            return layuiMsg(1, "操作成功",$dataForm['avatar']);
        } else {
            return layuiMsg(-1, "您未修改操作");
        }
    }

    //修改基本信息页面
    public function updataInfoEdit(Request $request)
    {
        //获取当前考生ID
        $data['user_login_id'] = session('user')['id'];
        $dataForm = $request->param();
        unset($dataForm['provid']);
        //数据验证
        $result = $this->validate($data, 'ExamEnroll.basicEdit');
        if (true !== $result) {
            return layuiMsg(-1, $result);
        }
        $userinforesult = $this->SUserInfo->updateUserinfoData($dataForm,$data);
        if ($userinforesult == true) {
//            session('updataInfo', $data);
            return layuiMsg(1, "操作成功");
        } else {
            return layuiMsg(-1, "您未修改操作");
        }
    }


    public function perfect(){
       $sessionId= session('user')['id'];
       $data = $this->SUserInfo->findUserinfoData(['user_login_id'=>$sessionId]);
        if(empty($data)){
            return layuiMsg(-1,'请先完善信息');
        }else{
            return layuiMsg(1);
        }
    }

    /***
     *  身份证文件上传
     * @param Request $request
     * @return string
     */
    public function upload(Request $request)
    {
        //请求
        $file = $request->file('file');
        //移动文件/public/uploads/目录
        $uploadDir = DS . 'uploads/avatar' . DS .'avatar' . DS;
        $info = $file->validate(['ext' => 'jpg'])->move(ROOT_PATH . 'public' . $uploadDir);

        //将上传文件的路径返回
        if ($info) {
//            dump($uploadDir . $info->getSaveName());die;
            return json_encode($uploadDir . $info->getSaveName());
        } else {
           return layuiMsg(-1,"请上传.jpg格式图片");
        }
    }
}