<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/13
 * Time: 4:47 PM
 */

namespace app\app\controller;

use app\common\controller\AppBase;
use app\common\service\Certificate;
use app\common\service\ExamEnroll;
use app\common\service\Grade;
use app\common\service\Userinfo;
use app\common\service\UserLogin;
use think\Request;

/**
 * Class UserController
 * @package app\app\controller
 */
class UserController extends AppBase
{

    /**
     * @var UserLogin
     */
    private $SUserLogin;
    /**
     * @var Userinfo
     */
    private $SUserinfo;
    /**
     * @var Certificate
     */
    private $SCertificate;
    /**
     * @var Grade
     */
    private $SGrade;
    /**
     * @var ExamEnroll
     */
    private $SExamEnroll;

    /**
     * UserController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SUserLogin = new UserLogin();
        $this->SUserinfo = new Userinfo();
        $this->SCertificate = new Certificate();
        $this->SGrade = new Grade();
        $this->SExamEnroll = new ExamEnroll();
    }

    /**
     * 获取个性签名
     * @user 李海江 2018/11/13~5:07 PM
     */
    public function sign()
    {
        $res = $this->SUserLogin->show(['token' => appGetToken()]);
        $data = empty($res->info->signature) ? '' : $res->info->signature;
        config('app.20003.data', $data);
        result('20003');
    }

    /**
     * 修改个性签名
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/13~5:27 PM
     */
    public function editSign()
    {
        $sign = Request::instance()->only('signature');
        $res = $this->SUserinfo->updateUserinfoData($sign, ['user_login_id' => appGetUid()]);
        if ($res) {
            $data = $sign['signature'];
            config('app.20002.data', $data);
            result('20002');
        } else {
            result('40010');
        }
    }

    /**
     * 获取个人证书
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/15~12:00 PM
     */
    public function certificate()
    {
        $uid = appGetUid();
        $res = $this->SUserLogin->show(['id' => $uid]);
        $lists = $res->certificate;
        if (!empty($lists)) {
            $data = array();
            for ($i = 0; $i < count($lists); $i++) {
                $data[$i]['certificate_no'] = $lists[$i]->certificate_no;
                $data[$i]['create_time'] = date('Y年m月d日', strtotime($lists[$i]->create_time));
                $data[$i]['work'] = $lists[$i]->work;
                $data[$i]['direction'] = $lists[$i]->direction;
                $data[$i]['current_level'] = $lists[$i]->current_level;
            }
            config('app.20003.data', $data);
            result('20003');
        } else {
            result('40012');
        }
    }

    /**
     * 我的成绩
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/15~5:44 PM
     */
    public function grade()
    {
        $uid = appGetUid();
        $lists = $this->SGrade->selectGradeData(['user_login_id' => $uid]);
        if (!empty($lists)) {
            $data = array();
            $pass = config('applyStatus.pass');
            $nopass = config('applyStatus.nopass');
            for ($i = 0; $i < count($lists); $i++) {
                $data[$i]['work_name'] = $lists[$i]->work_name;
                $data[$i]['title'] = $lists[$i]->title;
                $data[$i]['level'] = $lists[$i]->level;
                $data[$i]['directionname'] = $lists[$i]->directionname;
                $data[$i]['theory_score'] = $lists[$i]->theory_score > config('applyStatus.theory_score') ? $pass : $nopass;
                $data[$i]['watch_score'] = $lists[$i]->watch_score > config('applyStatus.watch_score') ? $pass : $nopass;
                $data[$i]['synthesize_score'] = $lists[$i]->synthesize_score > config('applyStatus.synthesize_score') ? $pass : $nopass;
            }
            config('app.20003.data', $data);
            result('20003');
        } else {
            result('40019');
        }
    }

    /**
     * 考评人员和考生的报名
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/19~5:38 PM
     */
    public function enroll()
    {
        $role = request()->header('role');
        $map = [
            'enroll.type' => $role,
            'enroll.user_login_id' => appGetUid(),
            'enroll.status' => ['egt', config('ExamEnrollStatus.checkpass')],
        ];
        if ($role) {
            $list = $this->SExamEnroll->appStaffEnroll($map);
        } else {
            $list = $this->SExamEnroll->appMyEnroll($map);
        }

        config('app.20003.data', $list);
        result('20003');
    }

    /**
     * 修改密码
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/20~11:16 AM
     */
    public function password()
    {
        //修改密码 0  忘记密码 1
        $appData = Request::instance()->post(['password', 'type', 'phone']);
        $data = ['password' => $appData['password']];
        $param = ['mobile' => $appData['phone']];
        if ($appData['type']) {
            $data['token'] = create_token();
        } else {
            if (request()->header('token') == "") {
                result('40003');
            } else {
                $userLogin = new \app\common\service\UserLogin;
                $res = $userLogin->show(['token' => request()->header('token')]);
                if (empty($res)) result('40002');
            }
        }
        $res = $this->SUserLogin->edit($data, $param);
        if ($res) {
            result('20001');
        } else {
            result('40010');
        }
    }
}