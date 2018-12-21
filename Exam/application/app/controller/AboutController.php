<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/14
 * Time: 10:27 AM
 */

namespace app\app\controller;


use app\common\controller\AppBase;
use app\common\service\CmsAppAbout;
use think\Request;

/**
 * Class AboutController
 * @package app\app\controller
 */
class AboutController extends AppBase
{
    /**
     * @var CmsAppAbout
     */
    private $SCmsAppAbout;

    /**
     * AboutController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SCmsAppAbout = new CmsAppAbout();
    }

    /**
     * 用户协议
     * @return \think\response\View
     * @user 李海江 2018/11/14~10:43 AM
     */
    public function us()
    {
        $map = ['type' => 1, 'status' => 1];
        $field = ['content'];
        $res = $this->SCmsAppAbout->getOne($map, $field);
        $content = $res->content;
        return view('', ['content' => $content]);
    }

    /**
     * 常见问题
     * @user 李海江 2018/11/14~10:57 AM
     */
    public function faq()
    {
        //获取常见问题id
        $map = ['type' => 0, 'status' => 1];
        $res = $this->SCmsAppAbout->getOne($map, 'id');
        //查询常见问题下的数据
        $allMap = ['status' => 1, 'pid' => $res->id];
        $data = $this->SCmsAppAbout->getAppPageAll($allMap, ['title', 'content'], 'id asc');
        //返回数据
        config('app.20003.data', $data);
        result('20003');
    }
}