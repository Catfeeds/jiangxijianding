<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/7
 * Time: 11:28 AM
 */

namespace app\app\controller;

use app\common\controller\AppBase;
use app\common\service\ExamConditions;
use app\common\service\Work;
use think\Request;

/**
 * Class WorkController
 * @package app\app\controller
 */
class WorkController extends AppBase
{

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Work
     */
    private $SWork;
    /**
     * @var ExamConditions
     */
    private $SExamConditions;

    /**
     * WorkController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->SWork = new Work();
        $this->SExamConditions = new ExamConditions();
    }

    /**
     * 获得角色
     * @user 李海江 2018/11/30~3:34 PM
     */
    public function getRole()
    {
        $data = array(
            ['id' => 0, 'name' => '考生'],
            ['id' => 1, 'name' => '考评人员'],
        );
        config('app.20001.data', $data);
        result('20001');
    }

    /**
     * 报考条件工种列表
     * @user 李海江 2018/11/7~1:32 PM
     */
    public function lists()
    {
        $role = Request::instance()->post('role');
        $map = ['status' => 1];
        if ($role == 1) $map['work_type_id'] = ['neq', config('type_c')];
        $list = $this->SWork->getAlls($map, ['id', 'code', 'name']);

        //code 和 名称合到一起
        foreach ($list as $item) {
            $item['name'] = $item['code'] . '        ' . $item['name'];
            unset($item['code']);
        }
        config('app.20001.data', $list);
        result('20001');
    }


    /**
     * 根据选择的工种返回方向和等级列表
     * @user 李海江 2018/11/7~5:18 PM
     */
    public function dirAndLevel()
    {
        $data = $this->SWork->dirAndLevel();
        //返回
        config('app.20001.data', $data);
        result('20001');
    }

    /**
     * 搜索条件
     * @user 李海江 2018/11/13~10:43 AM
     */
    public function search()
    {
        //接受数据
        $map = $this->request->only(['role', 'work_id', 'dir_id', 'level'], 'post');
        //查询
        $res = $this->SExamConditions->showOne($map, ['level', 'content']);
        //返回
        if ($res) {
            if (empty($map['level'])) {
                $res['level'] = (string)0;
                $res['levelChinese'] = null;
            } else {
                $res['level'] = (string)$res['level'];
                $res['levelChinese'] = config('EnrollStatusText.work_level_subject_level')[$map['level']];
            }
            config('app.20001.data', $res);
            result('20001');
        } else {
            result('40011');
        }
    }
}