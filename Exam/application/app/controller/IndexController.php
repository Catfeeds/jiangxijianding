<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/26
 * Time: 11:50 AM
 */

namespace app\app\controller;

use app\common\controller\AppBase;
use app\common\service\CmsPicture;
use app\common\service\Notice;
use app\common\service\UserLogin;
use app\common\service\Version;
use think\Request;

/**
 * app首页
 * Class IndexController
 * @package app\app\controller
 */
class IndexController extends AppBase
{
    /**
     * @var CmsPicture
     */
    private $SCmsPicture;
    /**
     * @var UserLogin
     */
    private $SUserLogin;
    /**
     * @var Notice
     */
    private $SNotice;
    /**
     * @var Version
     */
    private $SVersion;


    /**
     * IndexController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SCmsPicture = new CmsPicture();
        $this->SNotice = new Notice();
        $this->SVersion = new Version();
        $this->SUserLogin = new UserLogin();
    }

    /**
     * 获取首页轮播图
     * @user 李海江 2018/10/26~11:58 AM
     */
    public function advert()
    {
        $res = $this->SCmsPicture->getPicture(2, ['url']);


        config('app.20001.data', $res);
        result('20001');
    }


    /**
     * 获得系统通知 0是单条  1是多条
     * @param $type
     * @user 李海江 2018/11/2~11:01 AM
     */
    public function notice($type)
    {
        $map = ['admin_id' => $this->info->id];
        if ($type) {
            $page = request()->param('page') ? request()->param('page') : 1;
            $count = request()->param('count') ? request()->param('count') : 10;
            $page = [$count, true, ['page' => $page]];
            $list = $this->SNotice->getAll($page, $map, ['content', 'create_time']);
        } else {
            $list = $this->SNotice->getOne($map, ['content']);
        }
        config('app.20001.data', $list);
        result('20001');
    }


    /**
     * 检查版本号
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/23~5:45 PM
     */
    public function version()
    {
        $data = request()->only(['num', 'type', 'app_type'], 'post');
        $res = $this->SVersion->checkversion($data);
        if ($res['flag']) {
            config('app.20006.data', $res['data']);
            result('20006');
        } else {
            //buxuyaogengxin
            result('20007');
        }
    }


    /**
     * 发送验证码
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/23~6:40 PM
     */
    public function sendMessage()
    {
        $data = Request::instance()->only(['phone', 'type'], 'post');
        $result = $this->validate($data, 'App.appSendMsg');
        if (true !== $result) {
            result('40013');
        } else {
            //1忘记密码 0修改密码
            if ($data['type']) {
                $count = $this->SUserLogin->showCount(['mobile' => $data['phone']]);
                if ($count < 1) result('40017');
            } else {
                $res = $this->SUserLogin->show(['id' => appGetUid()]);
                if ($res->mobile != $data['phone']) result('40016');
            }
            $res = sendMsg($data['phone'], 0);
            if ($res['flag']) {
                result('20004');
            } else {
                result('40010');
            }
        }

    }

    /**
     * 检查验证码
     * @user 李海江 2018/11/23~4:50 PM
     */
    public function checkMessage()
    {
        $data = Request::instance()->only(['phone', 'code'], 'post');
        //如果短信验证码与Cache里的不一致返回验证码错误 , 如果验证码错误直接return错误代码
        $res = check_code($data['code'], $data['phone']);
        //如果验证码不通过
        if (!$res) {
            result('40014');
        } else {
            result('20005');
        }
    }
}