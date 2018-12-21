<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/2
 * Time: 11:11 AM
 */

namespace app\app\controller;

use app\common\controller\AppBase;
use app\common\service\CmsArticle;
use app\common\service\CmsGuide;
use think\Request;

class CmsController extends AppBase
{
    private $SCmsArticle;
    private $SCmsGuide;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SCmsArticle = new CmsArticle();
        $this->SCmsGuide = new CmsGuide();
    }

    //0 是最新通知  1是 新闻动态
    //列表1 不是列表0
    public function news($class, $type)
    {
        if ($class == 0) {
            $gid = config('news');
            $limit = $type == 1 ?: config('newslimit');
        }
        if ($class == 1) {
            $gid = config('dynamic');
            $limit = $type == 1 ?: config('dynamiclimit');
        }
        if ($type) {
            $list = $this->test($gid);
        } else {
            $list = $this->SCmsArticle->getAll(['guide_id' => $gid], ['title', 'id', 'create_time'], '', $limit);
        }
        config('app.20001.data', $list);
        result('20001');
    }

    /**
     * 考试计划顶部几个新闻栏目,默认一个栏目带十条数据
     * @user 李海江 2018/11/5~10:27 AM
     */
    public function headerlist()
    {
        $list = array(
            ['id' => 1, 'guide_name' => '国考'],
            ['id' => 2, 'guide_name' => '专项能力'],
            ['id' => 3, 'guide_name' => '技师'],
            ['id' => 4, 'guide_name' => '预备技师'],
            ['id' => 5, 'guide_name' => '统考'],
            ['id' => 6, 'guide_name' => '竞赛'],
            ['id' => 7, 'guide_name' => '考评人员'],
            ['id' => 8, 'guide_name' => '高新考试'],
        );

        $param = ['status' => 1, 'guide_id' => config('jdjh')];

        for ($i = 0; $i < count($list); $i++) {
            $field = ['title', 'id', 'fujian', 'create_time'];
            $param = array_merge($param, ['type_exam_plan' => $list[$i]['id']]);
            $list[$i]['content'] = $this->SCmsArticle->appGetAll($param, $field);
        }
        config('app.20001.data', $list);
        result('20001');
    }


    /**
     * 计划任务每个单独列表分页使用
     * @user 李海江 2018/11/5~10:34 AM
     */
    public function lists()
    {
        //获取栏目id
        $id = Request::instance()->post('id');
        //构造laji
        $param = ['status' => 1, 'guide_id' => config('jdjh')];
        $field = ['title', 'id', 'fujian', 'create_time'];
        $param = array_merge($param, ['type_exam_plan' => $id]);
        $list['content'] = $this->SCmsArticle->appGetAll($param, $field);
        $arr = [0 => ['content' => $list]];
        config('app.20001.data', $arr);
        result('20001');
    }

    //内部方法
    protected function test($guide_id)
    {
        $map = ['guide_id' => $guide_id];
        $field = ['title', 'id', 'fujian', 'create_time'];
        return $this->SCmsArticle->appGetAll($map, $field, 'id desc');
    }


    /**
     * 获取h5新闻详情页
     * @user 李海江 2018/11/6~11:45 AM
     */
    public function content()
    {
        $id = Request::instance()->route('id');
        $res = $this->SCmsArticle->appGetOne($id);
        return view('', ['res' => $res]);
    }
}